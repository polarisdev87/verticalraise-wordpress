<?php

namespace classes\app\emails;

use \classes\app\emails\typo_trap\TypoTrap;

class Email_Utils
{

    private $email_validator;

    public function __construct() {
        $this->typo_trap = new TypoTrap();
    }

    /**
     * Check if the email is a valid email.
     * @param  string $email The email to check
     * @return bool
     */
    public function is_email_valid( $email ) {
        // Check if the email is properly formatted
        if ( $this->is_email_address_valid( $email ) ) {
            // Check if the email is a typo
            if ( $this->typo_trap->check( $email ) ) {
                return false;
            }

            // No problems with validation
            return true;
        }

        return false;
    }

    /**
     * Check if the email is a valid email.
     * @param  string $email The email to check
     * @return bool
     */
    public function is_email_address_valid( $email ) {
        $php_valid = $this->is_email_address_valid_php( $email );

        if ( $php_valid ) {
            return true;
        }

        return false;
    }

    /**
     * Check if the email is a valid email using PHP's filter var.
     * @param  string $email The email to check
     * @return bool
     */
    public function is_email_address_valid_php( $email ) {
        if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if the email domain exists.
     * @param  string $email The email to check
     * @return bool
     */
    public function is_email_domain_valid( $email ) {

        if ( _IS_LOCAL_DEV ) {
            return true;
        }

        // Take a given email address and split it into the username and domain.
        list($user, $domain) = preg_split( "/@/", $email );

        if ( checkdnsrr( $domain . '.', "MX" ) ) { // Need the . to qualify the host, and speed it up.
            return true;
        } else {
            return false;
        }
    }

    /**
     * Format the email.
     * @param  string $email
     * @return string $email
     */
    public function format_email( $email ) {
        $email = trim( strtolower( $email ) );
        return $email;
    }

    /**
     * Convert the emails to all lower case.
     * @param  array $emails Array of email addresses
     * @return array $_emails Array of email addresses
     */
    public function emails_to_lower( $emails ) {
        // Convert the emails to all lowercase
        foreach ( $emails as $email ) {
            if ( !empty( $email ) ) {
                $_emails[] = $this->format_email( $email );
            }
        }

        return $_emails;
    }

    /**
     * Remove list of emails from the active email array.
     *
     * @param  array $emails_array   An array of our submitted email addresses.
     * @param  array $invalid_emails An array of our invalid submitted email addresses.
     *
     * @return array An array of our active emails with invalid emails removed.
     */
    private function remove_emails( $emails_array, $invalid_emails ) {

        // Remove invalid emails from $emails_array
        if ( !empty( $invalid_emails ) ) {
            foreach ( $invalid_emails as $invalid_email ) {
                $key = array_search( strtolower( $invalid_email ), $emails_array );
                unset( $emails_array[$key] );
            }
        }

        return $emails_array;
    }

}
