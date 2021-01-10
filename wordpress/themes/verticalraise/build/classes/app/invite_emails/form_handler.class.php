<?php

namespace classes\app\invite_emails;

class Form_Handler
{
    
    /**
     * Check to see if there is a form submit for either of the invite forms.
     * @return bool
     */
    public function is_form_submit() {
        if ( isset($_POST['input_submit']) && $_POST['input_submit'] ) {
            return true;
        } else if ( isset($_POST['invite_submit']) && $_POST['invite_submit'] ) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Check if this is a $_POST['invite_submit'].
     */
    public function is_invite_submit() {
        if ( isset($_POST['invite_submit']) || isset($_POST['input_submit']) ) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Grab the list of email addresses from the incoming form submit.
     * @return array The list of emails.
     */
    public function get_the_emails() {
        
        // First Case
        if ( isset($_POST['invite_submit']) ) {
            $emails = array_unique($_POST['inviteemail']);
        }
        
        // Second case
        if ( isset($_POST['input_submit']) ) {
            $emails = array_unique(preg_split('/[;, \r\n]+/', $_POST['emails']));
        }
        
        return $emails;
    }
    
}