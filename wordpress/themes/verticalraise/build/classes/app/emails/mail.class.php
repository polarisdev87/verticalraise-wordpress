<?php

namespace classes\app\emails;

use \classes\app\loaders\Notification_Template_Loader as Notification_Template_Loader;
use \classes\app\emails\SendGrid_Log;
use SendGrid\Mail\Attachment;
use \classes\app\fundraiser\Fundraiser_Ended;     // Fundraiser Ended Class Object

/**
 * Send custom emails using predefined html email templates
 */
class Custom_Mail
{

    private $template_loader;      // Notifications Template Loader class object
    private $emails_path;          // The path to the html email templates
    private $sendgrid_api_path;    // The SendGrid API Path
    private $sendgrid_api_key;     // The SendGrid API Key
    private $sendgrid_log;         // The SendGrid Log class object

    /**
     * Class constructor
     */

    public function __construct() {
        $this->emails_path       = get_template_directory() . '/notifications/email/templates/';
        $this->sendgrid_api_path = get_template_directory() . '/sendgrid-php-master/vendor/autoload.php';
        $this->sendgrid_api_key  = _SENDGRID_APIKEY;
        $this->sendgrid_log      = new SendGrid_Log;

        $this->template_loader   = new Notification_Template_Loader( $this->emails_path );
    }

    /**
     * Send the email using WP.
     *
     * @param string $to             The e-mail address to send to
     * @param string $from           The e-mail address to send from
     * @param string $cc             The e-mail address to CC
     * @param string $reply          The e-mail address to reply email
     * @param string $subject        The subject of the e-mail
     * @param string $template       The email template to use
     * @param array  $template_args  The template data to pass to the template to form the e-mail content
     * @param string $from_name      The from name that the e-mail sends from
     * 
     * @return string Success or failure message
     */
    public function send( $to = null, $from = null, $cc = null, $reply = null, $subject = null, $template = null, $template_args = null, $from_name = null ) {

        // Validate all arguments inputted into the function
        $validated = $this->validate( $to, $from, $cc, $subject, $template, $template_args );


        if ( $validated != true ) {
            return "could not validate the arguments: {$validated}";
        }

        // Load the template
        $template = $this->template_loader->load_template( $template, '.email.html' );

        if ( $template != false ) {

            // Load content
            $content = $this->template_loader->load_content( $template, $template_args );

            // Set headers
            $headers = $this->set_headers( $from, $cc, $reply, $from_name );

            // Send the email
            $sent = $this->send_email( $to, $from, $subject, $content, $headers );

            return $sent;
        } else {
            return 'could not load template';
        }
    }

	/**
	 * Send the email using the SendGrid API.
	 *
	 * @param string $to The e-mail address to send to
	 * @param string $from The e-mail address to send from
	 * @param string $cc The e-mail address to CC
	 * @param string $subject The subject of the e-mail
	 * @param string $template The email template to use
	 * @param array $template_args The template data to pass to the template to form the e-mail content
	 * @param string $from_name The from name that the e-mail sends from
	 * @param string $plain_template The plain text template to use
	 * @param array $attachment Array with keys data and mime
	 *
	 * @return string Success or failure message
	 * @throws \Exception
	 */
    public function send_api( $to = null, $from = null, $cc = null, $subject = null, $template = null, $template_args = null, $from_name = null, $plain_template = null, $attachment = null ) {
        $plain_content = '';
        // Validate all arguments inputted into the function
        $validated = $this->validate( $to, $from, $cc, $subject, $template, $template_args );

        if ( $validated != true ) {
            return "could not validate the arguments: {$validated}";
        }

        // Load the html template
        $template = $this->template_loader->load_template( $template, '.email.html' );

        if ( $template != false ) {

            // Load the html content
            $html_content = $this->template_loader->load_content( $template, $template_args );

            if ( $plain_template ) {

                $plain_content = '';

                // Load the plain template
                $plain_template = $this->template_loader->load_template( $plain_template, '.plain_email.html' );

                // Load the plain content
                if ( $plain_template != false ) {
                    $plain_content = $this->template_loader->load_content( $plain_template, $template_args );
                }
            }

            // Set the headers
            $headers = $this->set_headers( $from, $cc, null, $from_name );

            // Send the email
            return $this->send_email_api( $to, $from, $cc, $subject, $html_content, $plain_content, $from_name, $headers , $attachment);
        } else {
            return 'missing content';
        }
    }

    /**
     * Use wp_mail function to send the email.
     * @param string $to      To e-mail address
     * @param string $from    The from e-mail address (does not get used)
     * @param string $subject The subject
     * @param string $content The e-mail content
     * @param array  $headers The e-mail headers
     */
    private function send_email( $to, $from, $subject, $content, $headers ) {
        try {
            return wp_mail( $to, $subject, $content, $headers );
        } catch ( Exception $e ) {
            if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
                newrelic_notice_error( $e->getMessage(), $e );
            }

            return 'failure';
        }
    }

	/**
	 * Use SendGrid API to send the email.
	 *
	 * @param string $to To e-mail address
	 * @param string $from From e-email address
	 * @param string $cc CC e-mail address
	 * @param string $subject Subject
	 * @param string $html_content Html content
	 * @param string $plain_content Plain text content
	 * @param $from_name
	 * @param string $headers Email headers
	 * @param $attachment
	 *
	 * @return boolean
	 * @throws \SendGrid\Mail\TypeException
	 */
    private function send_email_api( $to, $from, $cc, $subject, $html_content, $plain_content, $from_name, $headers , $attachment = false ) {

            // Instantiate Objects & Setup Params
            require_once( $this->sendgrid_api_path );

            $email = new \SendGrid\Mail\Mail();
            if ( isset ( $GLOBALS['phpunit_test_running'] ) ){
                $email->enableSandBoxMode();
            }
            $email->setFrom( $from, $from_name );
            $email->setSubject( $subject );

            $email->addTo( $to );

            if ( $cc ) {
                $email->addCc( $cc );
            }

            if ( $plain_content ) {
                $plainTextContent = new \SendGrid\Mail\PlainTextContent( $plain_content );
                $email->addContent( $plainTextContent );
            }

            $htmlContent = new \SendGrid\Mail\HtmlContent( $html_content );
            $email->addContent( $htmlContent );

            if ( $attachment && isset($attachment['base64_data']) && isset($attachment['mime_type']) && isset($attachment['file_name']) ) {
	            $email->addAttachment(new Attachment( $attachment['base64_data'], $attachment['mime_type'] , $attachment['file_name']));
            }

            // Set API Key
            $sendgrid = new \SendGrid($this->sendgrid_api_key);

            // Send the email
            $response = $sendgrid->send( $email );

            $statusCode = $response->statusCode();
            $body = $response->body();
            $log = $this->sendgrid_log->log( $to, $statusCode );

            if( $statusCode != 202 && $statusCode != 200 ) {
                throw new \Exception( "[SendGrid API] Email delivery has failed. Status Code: $statusCode and Body: $body", $statusCode );
            }

            return true;

    }


	 /**
     * @param $to
     * @param $from
     * @param $cc
     * @param $subject
     * @param $html_content
     * @param $plain_content
     * @param $from_name
     * @param $headers
     * @return bool
     * @throws \SendGrid\Mail\TypeException
     */
    public function test_send_email_api($to, $from, $cc, $subject, $html_content, $plain_content, $from_name, $headers){
        return $this->send_email_api($to, $from, $cc, $subject, $html_content, $plain_content, $from_name, $headers);
    }

    /**
     * Set the headers for the email.
     *
     * @param  string $from      The from e-mail address
     * @param  string $from_name The from name
     *
     * @return string $headers   The email headers
     */
    private function set_headers( $from, $cc = null, $reply = null, $from_name = null ) {

        if ( !empty( $from_name ) ) {
            $headers = "From: " . $from_name . " <" . $from . ">\r\n";
        } else {
            $headers = "From: Vertical Raise <" . $from . ">\r\n";
        }

        if ( !empty( $cc ) )
            $headers .= "CC: " . $cc . "\r\n";
        if ( !empty( $reply ) )
            $headers .= "Reply-To: " . $reply . "\r\n";
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        return $headers;
    }

    /**
     * Validate params.
     *
     * @param string $to            The e-mail address to send to
     * @param string $from          The e-mail address to send from
     * @param string $cc            The e-mail address to cc
     * @param string $subject       The subject of the email
     * @param string $template      The name of the email template being loaded
     * @param array  $template_args The data values being passed into the template for a string replace
     *
     * @return mixed Either a string describing the error, or true to let the program know all values passed.
     */
    private function validate( $to = null, $from = null, $cc = null, $subject = null, $template = null, $template_args = null ) {

        // Check for empty values
        if ( empty( $to ) )
            return 'to empty';
        if ( empty( $from ) )
            return 'from empty';
        if ( empty( $subject ) )
            return 'subject empty';
        if ( empty( $template ) )
            return 'template empty';
        if ( empty( $template_args ) )
            return 'template_args empty';

        // Check for correct data types
        if ( is_string( $to ) == false )
            return 'to should be string';
        if ( is_string( $from ) == false )
            return 'from should be string';
        if ( is_string( $subject ) == false )
            return 'subject should be string';
        if ( is_string( $template ) == false )
            return 'template should be string';
        if ( is_array( $template_args ) == false )
            return 'template_args should be array';

        // Check for valid $to email
        if ( is_email( $to ) == false )
            return 'email is not valid';

        // Check for a valid $cc email
        if ( !empty( $cc ) ) {
            if ( is_email( $cc ) == false )
                return 'cc email is not valid';
        }

        return true;
    }

}
