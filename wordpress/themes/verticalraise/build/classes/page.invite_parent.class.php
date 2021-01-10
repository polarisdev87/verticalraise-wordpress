<?php

use \classes\Get_User_Info;
use \classes\VerticalRaise_Shorturl;
use \classes\app\sms\SMS;
use \classes\app\loaders\Notification_Template_Loader as Notification_Template_Loader;

use \classes\app\utm\UTM;


class Page_Invite_Parent
{

    private $short_url;
    private $get_user_info;
    private $sms;
    private $participant_records;
    private $user_ID;
    private $fundraiser_ID;

    /**
     * Class Constructor.
     * @param int $user_ID
     * @param int $fundraisr_ID
     */
    public function __construct( $user_ID, $fundraiser_ID ) {

        load_class('get_user_info.class.php');
        load_class('participant_records.class.php');
        load_class('verticalraise_shortcode.class.php');

        $this->short_url           = new VerticalRaise_Shorturl();       // Shorturl class object
        $this->get_user_info       = new Get_User_Info();                // Get user info class object
        $this->sms                 = new SMS();                          // SMS class object
        $this->participant_records = new Participant_Sharing_Totals();   // Participant sharing totals class object

        $this->user_ID             = $user_ID;
        $this->fundraiser_ID       = $fundraiser_ID;
        $this->sms_path            = get_template_directory() . '/notifications/sms/templates/';

        $this->template_loader = new Notification_Template_Loader($this->sms_path);
    }

    /**
     * Process the form.
     */
    public function process() {

        if ( isset($_POST['invite_submit']) ) {

            $result = '';

            $user_info = get_userdata($this->user_ID);

            $i = 0;

            $numbers_array = $_POST['invitesms'];
            $numbers_array = array_unique($numbers_array);

            $invalid = array();
            $valid = array();


            foreach ( $numbers_array as $number ) {
                if ( !empty($number) && $i <= 50 ) {

                    /**
                     * Send the text.
                     */

                    // Check if we have sent it before
                    $stored_parent_numbers = get_user_meta($this->user_ID, 'stored_parents');

                    if ( !empty($stored_parent_numbers) && isset($stored_parent_numbers[0][$this->fundraiser_ID])) {
                        if ( in_array($number, $stored_parent_numbers[0][$this->fundraiser_ID]) == true ) {
                            continue;
                        }
                    }

                    // Phone number
                    $phone = trim($number);

                    $full_name = $this->get_user_info->get_full_name_with_backup($this->user_ID);

                    //$base_url = get_site_url() . '/invite-parent-start/?fundraiser_id=' . $this->fundraiser_ID . '&parent=1&user_id=' . $this->user_ID;
                    $utm= new UTM;
                    $utm_code = $utm->getUTMCode('');
                    $url = $this->short_url->get($this->fundraiser_ID, $this->user_ID, 'sms', 1, $utm_code);
                    
                    
                    
                    $from = get_post_meta($this->fundraiser_ID, 'con_name', true);
                    
                    $template_args = [
                        'FROM'             => get_post_meta($this->fundraiser_ID, 'con_name', true),
                        'PARTICIPANT_NAME' => $full_name,
                        'FUNDRAISER'       => get_the_title($this->fundraiser_ID),
                        'URL'              => $url
                    ];


                    $result = $this->sms->send($phone,  'inviteparent', $template_args);


                    // Success
                    if ( $result == 'success' ) {

                        // Add the phone number to the user meta for 'stored parents'
                        $stored_parents_original = get_user_meta($this->user_ID, 'stored_parents');

                        if ( !empty($stored_parents_original) ) {

                            $stored_parents = $stored_parents_original[0];
                            if ( !in_array($phone, $stored_parents) ) {
                                $stored_parents[$this->fundraiser_ID][] = $phone;
                            }

                            update_user_meta($this->user_ID, 'stored_parents', $stored_parents);

                        } else {
                            $stored_parents[$this->fundraiser_ID][0] = $phone;
                            update_user_meta($this->user_ID, 'stored_parents', $stored_parents);
                        }

                        $this->participant_records->adjust($this->fundraiser_ID, $this->user_ID, 'parents', 1);

                        $valid[] = $phone;
                    } else if ( $result != 'success' && $result != '' ) {
                        $invalid[] = $result['phone'];
                    }

                }
            }


            $results['invalid'] = $invalid;
            $results['valid'] = $valid;
            return $results;

        }
    }

    public function get_mobile_message() {

        $template = 'mobile_inviteparent';

        $full_name = $this->get_user_info->get_full_name_with_backup($this->user_ID);
        
        $utm= new UTM;
        $utm_code = $utm->getUTMCode('');
        $click_url = $this->short_url->get($this->fundraiser_ID, $this->user_ID, 'sms', 1, $utm_code);
        

        $template_args = [
            'FROM'             => get_post_meta($this->fundraiser_ID, 'con_name', true),
            'PARTICIPANT_NAME' => $full_name,
            'FUNDRAISER'       => get_the_title($this->fundraiser_ID),
            'URL'              => $click_url
        ];

        $template = $this->template_loader->load_template($template, '.sms.html');

        $content = $this->template_loader->load_content($template, $template_args);

        return $content;

    }
    public function get_mobile_copylink_message() {

        $template = 'mobile_inviteparent';

        $full_name = $this->get_user_info->get_full_name_with_backup($this->user_ID);
        
        $utm= new UTM;
        $utm_code = $utm->getUTMCode('');
        $click_url = $this->short_url->get($this->fundraiser_ID, $this->user_ID, 'sms', 1, $utm_code);


        $template_args = [
            'FROM'             => get_post_meta($this->fundraiser_ID, 'con_name', true),
            'PARTICIPANT_NAME' => $full_name,
            'FUNDRAISER'       => get_the_title($this->fundraiser_ID),
            'URL'              => $click_url
        ];

        $template = $this->template_loader->load_template($template, '.sms.html');

        $content = $this->template_loader->load_content($template, $template_args);

        return $content;

    }
}
