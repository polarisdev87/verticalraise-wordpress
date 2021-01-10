<?php


use \classes\models\mixed\Admins;
use \classes\app\encryption\Encryption;
use \classes\app\sms\SMS;
use \classes\app\sms\SMS_Utils;

use \classes\app\utm\UTM;

class Invite_SMS 
{

    protected $admins;

    public function __construct($user_ID, $fundraiser_ID, $sharing_type, $share_type) {
        
        $this->user_ID             = (int) $user_ID;                                      // Define user ID
        $this->fundraiser_ID       = (int) $fundraiser_ID;                                // Define fundraiser ID
        $this->sharing_type        = $sharing_type;                                       // Define Sharing Type
        $this->share_type          = $share_type;                                         // Define Share Type for Thank you

        load_class('invite_sms/build_sms.class.php');
        load_class('invite_sms/form_handler.class.php');
        load_class('invite_sms/contact_import.class.php');

        $this->admins              = new Admins();    
        $this->sms                 = new SMS();                                     // SMS class object
        $this->form_handler        = new Form_Handler();                            // Form Handler class object
        $this->participant_records = new Participant_Sharing_Totals();              // Participant Sharing Totals class object

        $this->build_sms           = new Build_SMS();                               // Build SMS class object
        $this->sms_utils           = new SMS_Utils();                               // SMS Utils class object
        $this->encryption          = new Encryption();                              // Encryption class object
        
        $this->type                = $this->get_type();
        $this->parent              = $this->get_parent();
        $this->mobile              = $this->get_mobile();
        $this->template_type       = $this->get_template_type();
        $this->limit               = _SMS_INVITE_LIMIT;
        
        $this->contact_import      = new Contact_Import($this->template_type, $this->message_args(), $this->sharing_type);      // Contact Import class object

    }
    
    /**
     * The main send function
     */
    public function send() {
                
        $invalid_numbers = array();
        $valid_numbers = array();

        // All incoming phone numbers
        $numbers_array = $this->form_handler->get_numbers();
        
        // Potential Donors
        $potential_donors = $this->get_potential_donors($this->fundraiser_ID);
        
        // User object
        $user_info = get_userdata($this->user_ID);

        // Build the message
        $from_name = $this->build_sms->set_from_name($this->user_ID);
        $full_name = $this->build_sms->set_full_name($this->user_ID);
        
        $utm       = new UTM;
        if ( $this->sharing_type == 'spread_word' ){
            if ( $this->get_mobile() ){
                $utm_code = $utm->getUTMCode('SMS_Share_Mobile');
            }else{
                $utm_code = $utm->getUTMCode('SMS_Share_Desktop');
            }
        }else{
            if ( $this->get_mobile() ){
                $utm_code = $utm->getUTMCode('SMS_Invite_Mobile');
            }else{
                $utm_code = $utm->getUTMCode('SMS_Invite_Desktop');
            }
        }
        if ( $this->share_type == 1 ){
            $utm_code = $utm->getUTMCode('Thank_You_SMS_Share');
        }
        $url = $this->build_sms->set_click_url($this->fundraiser_ID, $this->user_ID, $this->get_parent(), $utm_code);
        
        $title     = $this->build_sms->set_title($this->fundraiser_ID);

        $template_args = [
            'FROM_NAME'        => $from_name,
            'PARTICIPANT_NAME' => $full_name,
            'FUNDRAISER_TITLE' => $title,
            'URL'              => $url
        ];
        
        $i = 0;



        foreach ( $numbers_array as $number ) {
            
            try {
            
                if ( !empty($number) && $i <= $this->limit ) {

                    $phone = $this->sms_utils->format_number($number);
                    
                    $checkDuplicate = $this->check_for_duplicates( $this->user_ID, $potential_donors, $phone );

                    // If it's a duplicate, skip
                    if ( $checkDuplicate == 'true' ) { 
                        continue;
                    }
                    
                    $result = $this->send_sms($phone, $this->template_type, $template_args);
                    
                    // Success
                    if ( $result == 'success' ) {
                        
                        // Process our records if there is a User ID
                        if ( $this->type == 'spread_the_word_user' || $this->type == 'invite' ) {
                            
                             
                            if ( !in_array($phone, $invalid_numbers) ) {
                                $this->process_records($this->user_ID, $this->fundraiser_ID, $phone);
                            }
                        }

                        $valid_numbers[] = $phone;

                    } else if ( $result != 'success' && $result != '' ) {
                        $invalid_numbers[] = $result['phone'];
                    }
                }                
                $i++;
            }

            catch (Exception $e) {
                if ( extension_loaded('newrelic') ) { // Ensure PHP agent is available
                    newrelic_notice_error($e->getMessage(), $e);
                }
            }
        }
        
        $results['numbers_array']   = $numbers_array;
        $results['invalid_numbers'] = $invalid_numbers;
        $results['valid_numbers']   = $valid_numbers;

        return $results;
    }
 
    /**
     * Send the SMS
     * @param  int    $phone
     * @param  string $type
     * @param  array  $template_args
     * @return ?
     */
    private function send_sms($phone, $type, $template_args) {
        return $this->sms->send($phone, $type, $template_args); 
    }
    
    /**
     * Get the SMS sharing type.
     * @return string Type
     */
    private function get_type() {
        $admins = $this->get_admins();

        if ( $this->form_handler->is_spread_the_word() == true || $this->sharing_type == 'spread_word' ) {
            if ( empty($this->user_ID) || $this->user_ID == 0  ) {
                return "spread_the_word_generic";
            } else {
                return "spread_the_word_user";
            }
        } else if( $this->is_an_admin($admins) ){
            return "spread_the_word_generic";
        } else {
            return "invite";
        }
    }
    
    /**
     * Get the parent status.
     * @return string Type
     */
    private function get_parent() {    
        return $this->form_handler->is_parent();
    }
    
    /**
     * Get the admin user ids.
     * @ return mixed
     */
    private function get_admins() {

        // Check to see if this is an admin or secondary admin
        if ( $this->user_ID != 0 || $this->user_ID != '' ) {
            ### TODO: Check if we have created a class/function for this yet? ###
            $temp['sadmins']    = json_decode(get_user_meta($this->user_ID, 'campaign_sadmin', true));
            $temp['author_id']  = get_post_field ('post_author', $this->fundraiser_ID);
            return $temp;
        }
        
        return false;
    }
    
    /**
     * Check if the user is admin status.
     * @param  array $admins
     * @return bool
     */
    private function is_an_admin($admins = null) {

        if ( !empty($admins) ) {
            // Is an author
            if ( !empty($admins['author_id']) ) {
                if ( $admins['author_id'] == $this->user_ID  ) {
                    return true;
                }
            }
            // Is an sadmin
            if ( !empty($admins['sadmin']) ) {
                if ( in_array_my($this->fundraiser_ID, $admins['sadmin']) ) {
                    return true;
                }
            }

        }
        return false;
    }
    
    /**
     * Check to see if the user is mobile.
     * @return bool
     */
    private function get_mobile() {
        if ( is_mobile_new() ) {
            return true;
        } else {
            return false;
        }
    }
    
    private function get_template_type() {
        
        if ( $this->type == 'spread_the_word_generic' ) {
            switch($this->mobile) {
                case true;
                    return "mobile_spread_the_word";
                    break;
                case false;
                    return "spread_the_word";
                    break;
            }
        }
        
        if ( $this->type == 'spread_the_word_user' ) {
            switch($this->mobile) {
                case true;
                    return "mobile_spread_the_word_user";
                    break;
                case false;
                    return "spread_the_word_user";
                    break;
            }
        }
        
        
        if ( $this->type == 'invite' && $this->parent == true ) {
            // Need a case for parent invite wizard
            switch($this->mobile) {
                case true;
                    return "mobile_p_invite";
                    break;
                case false;
                    return "p_invite";
                    break;
            }
        } else if ( $this->type == 'invite' ) {
            // Need a case for parent invite wizard
            switch($this->mobile) {
                case true;
                    return "mobile_invite";
                    break;
                case false;
                    return "invite";
                    break;
            }
        }
        
    }
    
    /**
     * Check for duplicate phone numbers so that we do not resend to recipients who have already been texted for this user and fundraiser.
     * @param  int    $user_ID
     * @param  array  $potential_donors An array of email addresses for the potential donors.
     * @param  string $number The number we want to make sure is not a duplicate.
     * @return bool
     */
    private function check_for_duplicates($user_ID, $potential_donors = null, $number) {
        if ( $this->form_handler->is_spread_the_word() == true ) {
            return false;
        }
        if ( empty($potential_donors) ) {
            return false;
        }
        
        $check_duplicate = false;

        // Check for duplicates
        foreach ( $potential_donors as $pd ) {
            if ( !empty($pd) ) {
                if ( ($pd[0] == $user_ID) && ($this->sms_utils->format_number($pd[1]) == $this->sms_utils->format_number($number)) ) {
                    $check_duplicate = 'true';
                }
            }
        }

        return $check_duplicate;
    }
    
    /**
     * Get the list of potential donors for this fundraiser id.
     * @param  int $fundraiser_id 
     * @return mix Array of potential donors or false.
     */
    private function get_potential_donors($fundraiser_id) {
        $potential_donors = json_decode(get_post_meta($fundraiser_id, 'potential_donors_sms_array', true));
        if ( !empty($potential_donors) ) {
            return $potential_donors;
        } else {
            return false;
        }
    }
    
    /**
     * Process all of the sharing updates we want to update.
     * @param int    $user_ID
     * @param string $to
     * @param array  $mail_details
     */
    private function process_records($user_ID, $fundraiser_ID, $phone) {
        $is_admin      = $this->admins->is_fundraiser_admin_or_site_admin($this->user_ID , $this->fundraiser_ID);
        
        // Update the Fundraiser's Sharing Record
        $this->update_sharing($fundraiser_ID, $user_ID, $phone);

        // Update Potential Donors
        $this->update_potential_donors($fundraiser_ID, $user_ID, $phone);

//         Update/Insert the Participant's Sharing Record

        // If not generic
        if ( $this->is_generic() != true && $is_admin == false ) {

            // Increment the user's text sharing record by one
            $this->participant_records->adjust($fundraiser_ID, $user_ID, 'text', 1);

        }

    }
    
    /**
     * Update the sharing record for this specific invite.
     * @param int    $fundraiser_id
     * @param int    $user_ID
     * @param string $to The email address for the recipient.
     * @param array  $mail_details An array of data relevant to the emails being sent.
     */    
    private function update_sharing($f_id, $user_ID) {

        $sms_share = json_decode(get_post_meta($f_id, 'sms_share', true), true);

        // The SMS Share record is empty
        if ( empty($sms_share) ) {
                        
            $sms_share['total']        = 1;
            $user_array                = array();
            $user_array[0]['uid']      = $user_ID;
            $user_array[0]['total']    = 1;
            $sms_share['user_array']   = $user_array;
            
            update_post_meta($f_id, 'sms_share', json_encode($sms_share));
            
        } else {
            // A record already existed, so we will append to it
            $flag = 0;
            $sms_share['total'] = $sms_share['total'] + 1;
            
            foreach ( $sms_share['user_array'] as $key => $user_array ) {
                if ( $user_array['uid'] == $user_ID ) {
                    $sms_share['user_array'][$key]['total'] = $user_array['total'] + 1;
                    $flag = 1;
                }
            }
            
            if ( $flag == 0 ) {
                $user_array          = array();
                $user_array['uid']   = $user_ID;
                $user_array['total'] = 1;
                
                array_push($sms_share['user_array'], $user_array);
            }
            
            // Update the sms share record
            update_post_meta($f_id, 'sms_share', json_encode($sms_share));
        }
        
    }
    
    /**
     * Update potential donors
     * @param int $f_id
     * @param int $user_ID
     * @param int $phone
     */
    private function update_potential_donors($f_id, $user_ID, $phone) {
        $potential_donors = json_decode(get_post_meta($f_id, 'potential_donors_sms_array', true));

        if ( !empty($potential_donors) ) {
            $potential_donors[] = array($user_ID, $phone);
        } else {
            $potential_donors[] = array($user_ID, $phone);
        }
        update_post_meta($f_id, 'potential_donors_sms_array', json_encode($potential_donors));
        
    }
    
    
    /**
     * Grab the template args for the contact import button message.
     * @return array The params
     */
    private function message_args() {
        $utm       = new UTM;
        if ( $this->sharing_type == 'spread_word' ){
            $utm_code = $utm->getUTMCode('SMS_Share');
        }else{
            if ( $this->get_mobile() ){
                if ( $this->get_parent() == 1 ){
                    $utm_code = $utm->getUTMCode('Parent_SMS_Invite_Mobile');
                }else{
                    $utm_code = $utm->getUTMCode('SMS_Invite_Mobile');
                }
            }else{
                if ( $this->get_parent() == 1 ){
                    $utm_code = $utm->getUTMCode('Parent_SMS_Invite_Desktop');
                }else{
                    $utm_code = $utm->getUTMCode('SMS_Invite_Desktop');
                }
            }
        }
        $params['FROM_NAME']        = $this->build_sms->set_from_name($this->user_ID);
        $params['PARTICIPANT_NAME'] = $this->build_sms->set_full_name($this->user_ID);
        $params['URL']              = $this->build_sms->set_click_url($this->fundraiser_ID, $this->user_ID, 0, $utm_code);
        $params['FUNDRAISER_TITLE'] = $this->build_sms->set_title($this->fundraiser_ID);
        
        return $params;
    }


    private function is_generic() {
        if ( $this->user_ID == 0 || $this->user_ID == '' ) {
            return true;
        }

        return false;
    }


}