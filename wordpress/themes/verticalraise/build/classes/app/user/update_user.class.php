<?php

/**
 * Update User name in participant fundraiser details.
 */

namespace classes\app\user;

use \classes\models\tables\Participant_Fundraiser_Details as Participant_Fundraiser_Details;
use \classes\models\mixed\User as User;

class Update_User
{

    private $postdata;                            // Post data
    private $table;                               // Participant Fundraiser Details class object
    private $user_ID;                             // Participant ID  
    private $profile_data;                        // Participant profile data array    

    public function __construct( $user_ID ) {
        $this->table     = new Participant_Fundraiser_Details;
        $this->user_meta = new User;
        $this->user_ID   = $user_ID;
    }

    /**
     * 
     * @param type $post
     * @return boolean|string
     */
    public function process_update( $post ) {
        $this->postdata = $post;
        $email_id       = sanitize_email($this->postdata['email_addr']);
        $first_name     = sanitize_text_field($this->postdata['fname']);
        $last_name      = sanitize_text_field($this->postdata['lname']);

        $data['status'] = false;

        if ( $this->check() ) {
            $this->profile_data = array (
                'participant'  => $this->user_ID,
                'email'        => $email_id,
                'first_name'   => $first_name,
                'last_name'    => $last_name,
                'display_name' => $first_name . " " . $last_name,
                'pw1'          => $this->postdata['pw1']
            );

            // Update wp_user table.
            $result = $this->update_profile();
            if ( is_wp_error($result) ) {                
                $errors = '';
                foreach ( $result->errors as $item ) {
                    $errors .= '<li style="list-style:none">' . $item[0] . '</li>';
                }

                $data['message'] = $errors;
                return $data;
            }

            // Update particiapant_fundraiser_details table.
            $this->update_fundraiser_detail();

            $data['message'] = "Your profile has been updated.";
            $data['status']  = true;
            return $data;
        }

        $data['message'] = 'Failed profile update';
        return $data;
    }

    /**
     * Update User Meta.
     */
    private function update_profile() {
        return $this->user_meta->update_user_meta($this->profile_data);
    }

    /**
     * Update participant Name.   
     */
    private function update_fundraiser_detail() {
        $myrows = $this->table->get_participant_results($this->user_ID);
        if ( !empty($myrows) ) {
            $this->table->update_participant_name($this->profile_data['display_name'], $this->user_ID);
        }
    }

    private function check() {
        if ( empty(sanitize_email($this->postdata['email_addr'])) )
            return false;
        if ( empty(sanitize_text_field($this->postdata['fname'])) )
            return false;
        if ( empty(sanitize_text_field($this->postdata['fname'])) )
            return false;

        return true;
    }

}
