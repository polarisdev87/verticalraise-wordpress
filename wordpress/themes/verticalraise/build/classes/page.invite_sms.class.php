<?php

load_class( 'invite_sms.class.php' );

/**
 * Template functions and process form
 */
class Page_Invite_SMS extends Invite_SMS
{

    /**
     * Class constructor.
     * @param int $user_ID
     * @param int $fundraiser_ID
     */
    public function __construct( $user_ID, $fundraiser_ID, $sharing_type = null, $share_type = 0 ) {
        parent::__construct( $user_ID, $fundraiser_ID, $sharing_type, $share_type );
    }

    /**
     * Send the invite email when the form is submitted.
     */
    public function process_form() {
        if ( !empty( $_POST ) ) {
            // Check security
            $this->security();
            // Process form
            $results = $this->send();
        } else {
            $results = null;
        }

        return $results;
    }

    /**
     * Display the contact import button.
     * @return The contact import button     
     */
    public function contact_import_button() {
        $this->contact_import->contact_import_button();
    }
    
    /**
     * Display the copy message button.
     * @return The copy message button 
     */
    public function copy_message_button() {
        $this->contact_import->copy_message_button();
    }

    /**
     * Output invalid numbers.
     * @param array $invalid_numbers
     * @return html
     */
    public function show_invalid_numbers( $invalid_numbers ) {
        if ( !empty( $invalid_numbers ) ) {
            echo '<p style="color: red; font-weight:400">WARNING!<br/>The number in red were not delivered because they were invalid. You may correct them and re-enter above. </p>';
            echo '<p style="color: #f91717;">';
            foreach ( $invalid_numbers as $invalid_number ) {
                echo '<strong class="invalidEmail">' . $invalid_number . '</strong>';
            }
            echo '</p>';
        }
    }

    /**
     * Generate a nonce to identify the user if the user submits the form while signed out.
     * @param  int   $user_ID 
     * @return mixed The nonce or false
     */
    public function generate_user_nonce( $user_ID ) {
        if ( empty( $user_ID ) ) {
            return false;
        }

        // Generate the string to encode
        $string = $user_ID . '_' . current_time( "timestamp" );

        // Encrypt
        $nonce = $this->encryption->encrypt( $string );

        return $nonce;
    }

    /**
     * Generate form nonce.
     * @return string The nonce
     */
    public function generate_nonce() {
        return wp_create_nonce( 'sms-submit' );
    }

    /**
     * Check for a form nonce to make sure the $_POST form submission is coming from our website.
     * @return void
     */
    private function security() {
        $nonce = (!empty( $_POST['_form_nonce'] ) ) ? $_POST['_form_nonce'] : false;

        // Nonce not valid
        if ( !wp_verify_nonce( $nonce, 'sms-submit' ) || empty( $nonce ) ) {
            header( 'Location: ' . get_site_url() );
        }
    }

}
