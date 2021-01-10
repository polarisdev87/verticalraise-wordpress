<?php

namespace classes\app\invite_emails;

class Type
{

    /**
     * Get the type of email to send.
     * @return string The type
     */
    public function get_type() {
        
        // First Case
        if ( $this->is_spread_the_word() ) {
            return 'spread_the_word';
        }
        
        // Second Case
        if ( $this->is_invite_wizard() ) {
            return 'invite_wizard';
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
    
}