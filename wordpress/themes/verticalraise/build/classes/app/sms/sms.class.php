<?php

/**
 * SMS Wrapper to send text messages via the Twilio API
 */
namespace classes\app\sms;

use \classes\app\loaders\Notification_Template_Loader as Notification_Template_Loader;

class SMS
{

    private $template_loader; // Notification Template Loader class object
    private $client;          // Twilio object
    private $sms_path;        // Template path
    private $fromNumber;      // From number

    /**
     * Class Constructor.
     */

    public function __construct() {

        // Load Wefund4u Global Config
        include_once( get_template_directory() . '/config/config.php' );

        // Twilio
        require_once( get_template_directory() . "/twilio-php-master/Services/Twilio.php" );
        $this->client = new \Services_Twilio( _TWILIO_ACCOUNT_ID, _TWILIO_AUTH_TOKEN );

        // Set template path
        $this->sms_path = get_template_directory() . '/notifications/sms/templates/';

        // Set from number
        $this->fromNumber = _TWILIO_FROM_NUMBER;

        $this->template_loader = new Notification_Template_Loader( $this->sms_path );
    }

    /**
     * Send an sms message to a phone number.
     * @param int $number Phone number
     * @param string $from The person it's from
     */
    public function send( $to = null, $template = null, $template_args = null ) {


        // Sanitize the phone number
        $to = $this->sanitize( $to );

        // validate inputs
        $validated = $this->validate( $to, $template, $template_args );


        if ( $validated == true ) {

            // load template
            $template = $this->template_loader->load_template( $template, '.sms.html' );

            if ( $template != false ) {

                // load content
                $content = $this->template_loader->load_content( $template, $template_args );

                // send the sms
                $sent = $this->send_sms( $to, $content );

                return $sent;
            } else {
                return 'could not load template';
            }
        } else {
            return $validated;
        }
    }

    /**
     * Send an SMS message through Twilio.
     * @param string $phone The recipient's phone number.
     * @param string $message The messages to text the recipient.
     * @return mixed 'Success' on success or failure array including the phone number on failure.
     */
    private function send_sms( $phone, $message ) {

        try {
            $this->client->account->messages->sendMessage(
                    $this->fromNumber, // From a valid Twilio number
                    $phone, $message
            );



            return 'success';
        } catch ( \Exception $e ) {
            if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
                newrelic_notice_error( $e->getMessage(), $e );
            }

            // Phone number is not valid
            $result['message'] = 'failure';
            $result['phone']   = $phone;

            return $result;
        }
    }

    /**
     * Validate Params.
     */
    private function validate( $to = null, $template = null, $template_args = array() ) {
        // Check for empty values
        if ( empty( $to ) )
            return 'to empty';
//        if ( empty($from) ) return 'from empty';
        if ( empty( $template ) )
            return 'template empty';
        if ( empty( $template_args ) )
            return 'template_args empty';

        // Check for correct data types
        if ( is_string( $to ) == false )
            return 'to should be string';
//        if ( is_string($from) == false ) return 'from should be string';
        if ( is_string( $template ) == false )
            return 'template should be string';
        if ( is_array( $template_args ) == false )
            return 'template_args should be array';

        // Check for valid $to phone number
        //if ( is_email($to) == false) return 'phone number is not valid';

        return true;
    }

    /**
     * Sanitize phone number.
     */
    private function sanitize( $to ) {
        $replace = [ " ", "/", "-", "(", ")" ];

        foreach ( $replace as $r ) {
            $to = str_replace( $r, "", $to );
        }

        return $to;
    }

}
