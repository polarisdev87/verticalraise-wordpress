<?php

namespace classes\app\invite_emails;

class Email_Template
{

    /**
     * Get the type of email to send.
     * @param bool   $is_admin
     * @param string $type
     * @return int
     */
    public function get_template($is_admin = false, $type) {
        if ( $type == 'spread_the_word' ) {
            $type = 1;
        } else if ( $type == 'invite_wizard' && $is_admin == true ) {
            // If the user is an admin or secondary admin, send this message.
            $type = 3; 
        } else if ( $type == 'invite_wizard') {
            // If the user is just a regular participant, send this message.
            $type = 2;
        }
        
        return $type;
    }


    
}