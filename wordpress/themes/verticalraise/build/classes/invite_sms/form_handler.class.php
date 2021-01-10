<?php

class Form_Handler
{
    
    /**
     * Check to see if there is a form submit for either of the invite forms.
     * @return bool
     */
    public function form_submit() {
        if ( isset($_POST['invite_submit']) ) {
            return true;
        } else if ( isset($_POST['invite_submit1']) ) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Check if this is a $_POST['invite_submit'].
     * @return bool
     */
    public function invite_submit() {
        if ( isset($_POST['invite_submit']) || isset($_POST['input_submit']) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if this is a 'Spread the Word' invite.
     * @return bool
     */
    public function is_spread_the_word() {
        if ( isset($_GET['display_type']) && $_GET['display_type'] == 'single' ) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Check if this is an 'Invite Wizard' invite.
     * @return bool
     */
    public function is_invite_wizard() {
        if ( empty($_GET['display_type']) ) {
            return true;
        } else if ( isset($_GET['display_type']) && $_GET['display_type'] != 'single' ) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Check if this is a parent sending the invite.
     * @return bool
     */
    public function is_parent() {
        if ( !empty($_GET['parent']) ) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Check if this is a single entry input submission.
     * @return bool
     */
    public function is_single_entry() {
         if ( isset($_POST['invite_submit1']) && !empty($_POST['invitesms']) ) {
             return true;
         } else {
             return false;
         }
    }
    
    /**
     * Check if this is a text area submission.
     * @return bool
     */
    public function is_text_area() {
        if ( isset($_POST['invite_submit']) && !empty($_POST['numbers']) ) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Check if the user's device is a mobile device.
     * @return bool
     */
    public function is_mobile() {
        if ( is_mobile_new() == true ) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Return the submitted form numbers.
     * @return array The phone numbers
     */
    public function get_numbers() {
        if ( $this->is_text_area() == true ) {
            return $this->get_phone_numbers();
        }
        
        if ( $this->is_single_entry() == true ) {
            return $this->get_phone_numbers2();
        }
        
    }
    
    /**
     * Return the submitted phone numbers entered in text area.
     * @return array The phone numbers
     */
    public function get_phone_numbers() {
        $numbers = preg_split('/[;, \r\n]+/', $_POST['numbers']);
        return array_unique($numbers);
    }
    
    /**
     * Return the submitted phone numbers entered line by line.
     * @return array The phone numbers
     */
    public function get_phone_numbers2() {
        $_numbers = $_POST['invitesms'];

        foreach ( $_numbers as $_number ) {
            $numbers[] = $_number;
        }
        
        return array_unique($numbers);
    }
}