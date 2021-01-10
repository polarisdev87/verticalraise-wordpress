<?php

/**
 * User Signup 
 */

namespace classes\app\user;

use classes\app\emails\Custom_Mail;
use classes\models\mixed\Signup_User;

class Signup
{

    private $postdata;                            // Post data

    /**
     * Class Constructor.
     */

    public function __construct() {
        $this->user_meta = new Signup_User();
        $this->mail      = new Custom_Mail();
    }

    /**
     * 
     * @param type $post
     * @return boolean|string
     */
    public function proccess_signup( $post ) {
        $this->postdata = $post;
        $email_id       = sanitize_email( $this->postdata['reg_email'] );
        $first_name     = sanitize_text_field( $this->postdata['fname'] );
        $last_name      = sanitize_text_field( $this->postdata['lname'] );
        if ( $this->check() ) {
            if ( email_exists( $this->postdata['reg_email'] ) ) {
                $result['status'] = false;
                $result['data']   = '<div class="errorMsg">Email ID already present. Please use different Email ID.</div>';
            } else {
                $data = array(
                    'email'        => $email_id,
                    'first_name'   => $first_name,
                    'last_name'    => $last_name,
                    'display_name' => $first_name . " " . $last_name,
                    'password'     => $this->postdata['password1']
                );

                $new_user_id = $this->user_meta->register_user_meta( $data );

                if ( is_wp_error( $new_user_id ) ) {
                    $errors = '';
                    foreach ( $new_user_id->errors as $item ) {
                        $errors .= '<div class="errorMsg">' . $item[0] . '</div>';
                    }

                    $result['data']   = $errors;
                    $result['status'] = false;
                } else {

                    $key = $this->user_meta->activation_key( $data );

                    // Set custom html email template params
                    $to      = $email_id;
                    $from    = _TRANSACTIONAL_FROM_EMAIL;
                    $subject = "Your Account with VerticalRaise.com";
                    $cc      = null;
                    $reply      = null;

                    // Set the template arguments to pass to the email template
                    $template_args = array(
                        'REG_EMAIL'              => $to,
                        'DISPLAY_NAME'           => $data['display_name'],
                        'LOGIN_URL'              => get_bloginfo( 'url' ) . "?login",
                        'SIGNATURE_EMAIL'        => _SIGNATURE_EMAIL,
                        'SIGNATURE_FAX_NUMBERS'  => _SIGNATURE_FAX_NUMBER,
                        'SIGNATURE_PHONE_NUMBER' => _SIGNATURE_OFFICE_PHONE_NUMBER,
                        'TEMPLATE_DIRECTORY'     => get_template_directory_uri()
                    );

                    /**
                     * Send the email.
                     */
                    try {
                        $sent = $this->mail->send_api( $to, $from, $cc, $subject, 'signup_user', $template_args );
                    }catch ( \Exception $e ) {
                        if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
                            newrelic_notice_error( $e->getMessage(), $e );
                        }
                    }

                    $login_data['user_login']    = $data['email'];
                    $login_data['user_password'] = $data['password'];
                    $login_data['remember']      = 'false';
                    wp_signon( $login_data, true );

                    $redirectUrl      = get_bloginfo( 'url' ) . "/my-account/?popup=1";
                    $result['status'] = true;
                    $result['data']   = $redirectUrl;
                }
            }
        } else {
            $result['status'] = false;
            $result['data']   = '<div class="errorMsg">Please check required fields.</div>';
        }

        return $result;
    }

    /**
     * Check post data
     * @return boolean
     */
    private function check() {
        if ( empty( sanitize_email( $this->postdata['reg_email'] ) ) )
            return false;
        if ( empty( sanitize_text_field( $this->postdata['fname'] ) ) )
            return false;
        if ( empty( sanitize_text_field( $this->postdata['fname'] ) ) )
            return false;

        return true;
    }

}
