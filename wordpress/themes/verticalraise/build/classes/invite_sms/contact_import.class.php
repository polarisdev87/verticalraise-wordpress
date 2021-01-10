<?php

use \classes\app\loaders\Notification_Template_Loader as Notification_Template_Loader;

class Contact_Import
{

    private $template_type;
    private $message_args;
    private $template_directory;
    private $sms_path;
    private $template_loader;

    /**
     * Class Constructor.
     */
    public function __construct( $template_type, $message_args, $sharing_type ) {

        // Template type
        $this->template_type = $template_type;

        // Message args
        $this->message_args = $message_args;
        
        // Sharing Type
        $this->sharing_type = $sharing_type;

        // Template directory
        $this->template_directory = get_template_directory_uri();

        // Set template path
        $this->sms_path = get_template_directory() . '/notifications/sms/templates/';

        $this->template_loader = new Notification_Template_Loader( $this->sms_path );
    }

    /**
     * Output the contact import button.
     * @return html
     */
    public function contact_import_button() {
        echo $this->get_import_button();
    }
    
    /**
     * Output the copy message button.
     * @return html
     */
    public function copy_message_button() {
        echo $this->get_copy_message_button();
    }
    
    /**
     * Generate the message and button code.
     * @return html
     */
    private function get_copy_message_button() {
        // Determine the message
        $message     = $this->get_message();
        $button_code = $this->get_copy_button_code( $message );

        return $button_code;
    }

    /**
     * Generate the message and button code.
     * @return html
     */
    private function get_import_button() {
        // Determine the message
        $message     = $this->get_link_start() . $this->get_message();
        $button_code = $this->get_button_code( $message );

        return $button_code;
    }
    
    /**
     * Return the copy button code.
     * @return html
     */
    private function get_copy_button_code( $message ) {

        $button_code = "<button class=\"copy-button\" data-clipboard-text=\"{$message}\"><i class=\"fa fa-link\"></i> <span>Copy Link</span></button>";
        
        return $button_code;
    }

    /**
     * Return the button code.
     * @return html
     */
    private function get_button_code( $message ) {
        global $template;

        $page_name = basename( $template );
        if ( $page_name == "page-single-fundraiser.php" || $page_name == "page-participant-fundraiser.php" || $page_name == "single-fundraiser.php" ) {
            $button_code = "<a href=\"{$message}\"  style=\"background-color: #46ce53;\"><i class=\"fa fa-mobile\" aria-hidden=\"true\"></i> Text</a>";
        } else {
            $button_code = "<div style='text-align: center;margin-bottom: 20px; margin-top: 15px;' class='message-click-button'><p style=\"text-align: center;margin-bottom: 30px; text-decoration: underline; color: #fff;\"><a href=\"{$message}\">Click here to Invite Contacts by Text Message</a></p><a href=\"{$message}\"><img src=\"{$this->template_directory}/assets/images/share3.png\"></a></div>";
        }
        return $button_code;
    }

    /**
     * Get message for the SMS message body.
     * @return The message
     */
    private function get_message() {
        $template_args = $this->message_args;

        $template = $this->template_loader->load_template( $this->template_type, '.sms.html' );
        $content  = $this->template_loader->load_content( $template, $template_args );

        return $content;
    }

    /**
     * SMS link start
     * @return The link start
     */
    private function get_link_start() {
        if ( isIphone() ) {
            if ( get_browser_version() >= 8 ) {
                $link_start = 'sms:&body=';
            } else {
                $link_start = 'sms:&body=';
            }
        } else {
            //$link_start = 'sms://;?&body=';
            $link_start = 'sms:?body=';
        }

        return $link_start;
    }

}
