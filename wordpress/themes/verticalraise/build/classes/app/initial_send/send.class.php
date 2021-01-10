<?php

namespace classes\app\initial_send;

use \classes\app\initial_send\Opening_Line;
use \classes\app\initial_send\Avatar;
use \classes\app\emails\Email_Utils;
use \classes\app\emails\Custom_Mail;
use \classes\app\debug\Debug;
use \classes\app\initial_send\Build_Email as Build_Email;

use \classes\app\utm\UTM;
class Send
{
    
    private $template_name = 'invite_email';

    /**
     * Process form.
     * @param string $email The email address
     * @param object $fundraiser The fundraiser content object
     * @param object $user The user content object
     */
    public function send( $email, $fundraiser, $user, $content, $type, $parent, $share_type ) {
        
        //throw new \Exception(json_encode($user));
        try {
            
            $to = $email;
            $cc = null;

            $debug        = new Debug( false );
            $avatar       = new Avatar();

            if ( !$user->your_name ) {
                $user->your_name = $fundraiser->title;
            }
            $url          = $user->click_url . "/" . $to;
            $utm          = new UTM;
            if ( $parent == 1 ){
                $utm_link = $utm->createUTMLink($url, 'Parent_Email_Invite');
            }else{
                if ( $type == 1 ){
                    if ( $share_type == 1 ){
                        $utm_link = $utm->createUTMLink($url, 'Thank_You_Email_Share');
                    }else{
                        $utm_link = $utm->createUTMLink($url, 'Email_Share');
                    } 
                }elseif( $type == 2 || $type == 3){
                    $utm_link = $utm->createUTMLink($url, 'Email_Invite');
                }
            }
            
//            var_dump($utm_link);exit;
            
            
            $template_args = [
                'AVATAR'         => $avatar->get_avatar($user, $fundraiser, $type),
                'FROM'           => $user->from_name,
                'URL'            => $utm_link,
                'FUNDRAISER'     => $fundraiser->title,
                'FROM_NAME'      => $user->from_name,
                'YOUR_NAME'      => $user->your_name,
                'BASE_URI'       => get_template_directory_uri(),
                'BACK_IMG_TOP'   => get_template_directory_uri() . '/assets/images/background-copy1.png',
                'BACK_IMG_LOGO'  => get_template_directory_uri() . '/assets/images/logo-background.png',
                'FUNDRAISER_MSG' => $fundraiser->message,
                'OPENING_LINE'   => $content->opening_line,
                'CYEAR'          => $content->copyright_year,
            ];
            
            /**
             * Send the email.
             */
            $sent = $this->send_mail( $to, $user->from, $cc, $user->subject, $this->template_name, $template_args, $user->from_name, $this->template_name );

            if ( $sent != 202) {
                $debug->debug( $sent, '(error thrown) sent' );
                $error_string = 'Could not send: template_args: ' . json_encode( $template_args );

                $data['results'] = false;
                $data['errors']  = $error_string;
            } else {
                $data['results'] = true;
            }
        } catch ( \Exception $e ) {
            if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
                newrelic_notice_error( $e->getMessage(), $e );
            }
            $data['errors'] = $e->getMessage();
            $data['results'] = false;
        }

        return $data;
    }

    /**
     * Send the email.
     */
    private function send_mail( $to, $from, $cc, $subject, $type, $template_args, $from_name, $plain ) {
        $custom_mail = new Custom_Mail();
        return $custom_mail->send_api( $to, $from, $cc, $subject, $type, $template_args, $from_name, $plain );
    }

}
