<?php

/**
 * Login Class - The login class handles the login and activation of users
 */
// Load Mail Class
use classes\app\emails\Custom_Mail;

class Login
{

    /**
     * Construct.
     */
    public function __construct() {
        $this->encryptKey = '644CBEF595BC9';
    }

    /**
     * Activate the new user.
     */
    public function activate() {

        // Check if there is a request to activate a new user
        if ( isset( $_GET['new_activation'] ) && !empty( $_GET['new_activation'] ) ) {

            $email = esc_sql( $this->decrypt( $_GET['new_activation'] ) );

            global $wpdb;

            // Look up the user_activation_key
            $key = $wpdb->get_var( $wpdb->prepare( "SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $email ) );

            // If there is a key
            if ( !empty( $key ) ) {

                /**
                 * Send the user an activation email.
                 */
                // Instantiate Class
                $mail = new Custom_Mail();

                // Setup Vars for the email
                $to      = $email; // user's email address
                $from    = _TRANSACTIONAL_FROM_EMAIL;
                $cc      = null;
                $reply   = null;
                $subject = "Activate your account";

                $url = get_site_url() . '/activation?key=' . $key;

                $template_args = [ 'URL' => $url ];

                // Send the emaail
                $mail->send_api( $to, $from, $cc, $subject, 'activation', $template_args );

                // Redirect the user to the thank you page
                header( "Location: " . get_bloginfo( 'url' ) . "/thank-you/?action=" . $this->encrypt( 'registration' ) );
                exit();
            } else {
                $errorCode = 6;
                return $errorCode;
            }
        }
    }

    /**
     * Login
     */
    public function login() {

        // Check for a login attempt
        if ( isset( $_POST['logina'] ) ) {

            $result['error'] = true;

            if ( isset( $_POST['login_email'] ) ) {
                // Check if this email exists
                $check_email = email_exists( trim( $_POST['login_email'] ) );

                //echo "check_email: {$check_email}<br>";
                //print_r($check_email);

                if ( $check_email != false ) {
                    // The function will have returned a User ID
                    $user_id = $check_email;

                    $username = esc_sql( $_POST['login_email'] );
                    $pwd      = esc_sql( $_POST['password'] );

                    // Check if the username exists?
                    //$user_status = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->users WHERE user_login = %s", $username));
                    // Get the user details for the supplied User ID
                    $user_status = get_user_by( 'id', $user_id );
//                    var_dump($user_status->ID);

                    if ( !empty( $user_status ) ) {

                        // If regular user (user_status == 2)
                        //if ( $user_status->user_status == 2 ) {

                        $login_data                  = array();
                        $login_data['user_login']    = $username;
                        $login_data['user_password'] = $pwd;

                        if ( $_POST['rememberme'] ) {
                            $login_data['remember'] = 'true';
                        } else {
                            $login_data['remember'] = 'false';
                        }

                        // Check the login tries
                        $login_count = $this->get_locked_counter();

                        //echo "login count: {$login_count}";
                        // If they can keep trying
                        if ( $login_count < 9 ) {

                            //echo "login count is less than 2";

                            /**
                             * Perform the user signon.
                             */
                            $user_verify = wp_signon( $login_data, true );

                            if ( is_wp_error( $user_verify ) ) {
                                // Updated the locked counter
                                $this->update_locked_counter();

                                $result['param'] = 3;
                                return $result;
                            } else {

                                /*                                 * $user_status -> ID
                                 * Load Class.
                                 */
//                                global $user_ID;
                                $campaign_participations = json_decode( get_user_meta( $user_status->ID, 'campaign_participations', true ) );
                                $campaign_sadmin         = json_decode( get_user_meta( $user_status->ID, 'campaign_sadmin', true ) );
                                if ( !empty( $campaign_participations ) || !empty( $campaign_sadmin ) ) {
                                    $fundraiser_query1        = new stdClass();
                                    $fundraiser_query2        = new stdClass();
                                    $fundraiser_query3        = new stdClass();
                                    $fundraiser_query1->posts = array();
                                    $fundraiser_query2->posts = array();
                                    $fundraiser_query3->posts = array();

                                    $args1             = array(
                                        'post_type'      => 'fundraiser',
                                        'post_status'    => array( 'pending', 'publish' ),
                                        'posts_per_page' => -1,
                                        'author'         => $user_status->ID
                                    );
                                    $fundraiser_query1 = new WP_Query( $args1 );
                                    if ( !empty( $campaign_participations ) ) {
                                        $args2             = array(
                                            'post_type'      => 'fundraiser',
                                            'post_status'    => array( 'publish', 'pending' ),
                                            'posts_per_page' => -1,
                                            'post__in'       => $campaign_participations
                                        );
                                        $fundraiser_query2 = new WP_Query( $args2 );
                                    }
                                    if ( !empty( $campaign_sadmin ) ) {
                                        $args3             = array(
                                            'post_type'      => 'fundraiser',
                                            'post_status'    => array( 'publish', 'pending' ),
                                            'posts_per_page' => -1,
                                            'post__in'       => $campaign_sadmin
                                        );
                                        $fundraiser_query3 = new WP_Query( $args3 );
                                    }

                                    $fundraiser_query = new WP_Query();

                                    if ( empty( $campaign_participations ) ) {
                                        $fundraiser_query->posts = array_merge( $fundraiser_query1->posts, $fundraiser_query3->posts );
                                    } elseif ( empty( $campaign_sadmin ) ) {
                                        $fundraiser_query->posts = array_merge( $fundraiser_query1->posts, $fundraiser_query2->posts );
                                    } elseif ( !empty( $campaign_participations ) && !empty( $campaign_sadmin ) ) {
                                        $fundraiser_query->posts = array_merge( $fundraiser_query1->posts, $fundraiser_query2->posts, $fundraiser_query3->posts );
                                    } else {
                                        $fundraiser_query->posts = array_merge( $fundraiser_query1->posts );
                                    }

                                    $fundraiser_query->post_count = count( $fundraiser_query->posts );
                                } else {
                                    $args             = array(
                                        'post_type'      => 'fundraiser',
                                        'post_status'    => array( 'pending', 'publish' ),
                                        'posts_per_page' => -1,
                                        'author'         => $user_status->ID
                                    );
                                    $fundraiser_query = new WP_Query( $args );
                                }

                                if ( $fundraiser_query->have_posts() ) :
                                    $old      = array();
                                    $current  = array();
                                    $upcoming = array();
                                    while ( $fundraiser_query->have_posts() ) : $fundraiser_query->the_post();
                                        $format_in    = 'Ymd';
                                        $start_date   = get_post_meta( get_the_ID(), 'start_date', true );
                                        $start_date   = DateTime ::createFromFormat( $format_in, $start_date );
                                        $end_date     = get_post_meta( get_the_ID(), 'end_date', true );
                                        $end_date     = DateTime ::createFromFormat( $format_in, $end_date );
                                        $current_date = new DateTime();
                                        if ( $current_date >= $start_date && $current_date < $end_date ) {
                                            array_push( $current, get_the_ID() );
                                        }
                                    endwhile;
                                endif;
                                wp_reset_postdata();
                                /**
                                 * Perform URL redirect.
                                 */
                                $loginStatus = get_user_meta( $user_status->ID, 'first_login', true );

                                if ( $loginStatus == "" ) {
                                    update_user_meta( $user_status->ID, 'first_login', 1 );
                                }

                                if ( isset( $current ) && count( $current ) == 1 ) {
                                    //echo "try to redirect";
                                    //exit();
                                    //
                                    $result['error'] = false;
                                    $result['param'] = get_site_url() . '/participant-fundraiser/?fundraiser_id=' . $current[0];
//                                    $result['param'] = get_site_url() . '/my-account';

                                    return $result;
                                    // header( 'Location: ' . get_site_url() . '/participant-fundraiser/?fundraiser_id=' . $current[0] );
                                } else {
                                    //echo "try to redirect 2";
                                    //exit();
                                    $result['error'] = false;
                                    $result['param'] = get_site_url() . '/my-account';

                                    return $result;
                                    // header( 'Location: ' . get_site_url() . '/my-account' );
                                }
                                exit();
                            }
                        } else {

                            $result['param'] = 4;
                            return $result;
                        }
                        /* } else {
                          return 1; // invalid login details
                          } */
                    } else {

                        $result['param'] = 5;
                        return $result;
                    }

                    $result['param'] = 7;
                    return $result;
                } else {

                    $result['param'] = 6;
                    return $result;
                }
            }

            return 'no errors';
        }
    }

    /**
     * Output an error message if it exists.
     * @param  int $code The error code
     * @return html Error message wrapped in html
     */
    public function output_errors( $code ) {
        // Determine the error message
        $message = $this->error_handling( $code );

        // Return html to display the error
        return $this->output_error( $message );
    }

    /**
     * Error handler
     * @param  int $error_code
     * @return mixed Error message or null
     */
    private function error_handling( $error_code = '' ) {

        // Encrypt the email_id
        $email_id = ( isset( $_POST['login_email'] ) ) ? $this->encrypt( $_POST['login_email'] ) : null;

        $blog_url = get_site_url();

        $message = null;

        switch ( $error_code ) {
            case 5:
                $message = "Email address or username not found.";
                break;
            case 1:
                $message = "You have not activated your account. Please check your email inbox to activate account. <a href='{$blog_url} /login/?new_activation={$email_id}'>Click here to send new acivation link</a>";
                break;
            case 2:
                $message = "Verification failed. Please try again.";
                break;
            case 3:
                $message = "Incorrect password. Please try again.";
                break;
            case 4:
                $message = "You have had your 10 failed attempts at logging in and now are banned for 10 minutes. Please try again after 10 minutes!";
                break;
            case 6:
                $message = "No user found.";
                break;
            case 7:
                $message = "An error occured logging you in.";
            default:
                $message = null;
                break;
        }

        return $message;
    }

    /**
     * Output the error message.
     * @param  string $message The error message
     * @return string The html error message to display
     */
    private function output_error( $message = null ) {

        // No error message
        if ( $message == null ) {
            return '';
        } else {
            // Output the error message
            $output = '<div class="errorMsg">';
            $output .= $message;
            $output .= '</div>';

            return $output;
        }
    }

    /**
     * Encrypt a string using base64_encode and a key.
     * @param  string $data Incoming string
     * @return string $val Encrypted version of the string
     */
    private function encrypt( $data ) {
        $final_data = $this->encryptKey . '|' . $data;
        $val        = base64_encode( base64_encode( base64_encode( $final_data ) ) );

        return $val;
    }

    /**
     * Encrypt a string using base64_encode and a key.
     * @param  string $data Incoming string
     * @return string $val Encrypted version of the string
     */
    private function decrypt( $data ) {
        $val        = base64_decode( base64_decode( base64_decode( $data ) ) );
        $final_data = explode( '|', $val );
        return $final_data[1];
    }

    /**
     * Get the locked counter record for the user's ip address.
     * @return mixed
     */
    private function get_locked_counter() {
        global $wpdb;

        $lock_date  = date( 'Y-m-d H:i:s' );
        $ip         = $_SERVER['REMOTE_ADDR'];
        $table_name = $wpdb->prefix . 'ip_lock';

        $query  = "SELECT * FROM `{$table_name}` WHERE ip = '{$ip}'";
        $result = $wpdb->get_row( $query );


        if ( !empty( $result ) ) {
            $start_date  = new DateTime( $result->locking_time );
            $since_start = $start_date->diff( new DateTime( $lock_date ) );
            $total_min   = $since_start->i;

            if ( $total_min > 3 ) {
                $wpdb->query( "UPDATE `{$table_name}` SET attempts = 0 WHERE ip = '{$ip}'" );
                return 0;
            } else {
                return $result->attempts;
            }
        } else {
            return 0;
        }
    }

    /**
     * Update the locked counter record for the user's ip address.
     * @return mixed
     */
    private function update_locked_counter() {
        global $wpdb;

        $lock_date = date( 'Y-m-d H:i:s' );
        $ip        = $_SERVER['REMOTE_ADDR'];

        $table_name = $wpdb->prefix . 'ip_lock';
        $result     = $wpdb->get_row( "SELECT * FROM `{$table_name}` WHERE ip = '{$ip}'" );
        if ( !empty( $result ) ) {

            $attempts = $result->attempts + 1;

            if ( $result->ip == $ip ) {
                $query = "UPDATE `{$table_name}` SET locking_time = '{$lock_date}', attempts = '{$attempts}' WHERE ip = '{$ip}'";
            } else {
                $query = "INSERT INTO `{$table_name}` (ip, locking_time, attempts) VALUES('{$ip}', '{$lock_date}', '{$attempts}')";
            }
        } else {
            $query = "INSERT INTO `{$table_name}` (ip, locking_time, attempts) VALUES('{$ip}', '{$lock_date}', '0')";
        }

        return $wpdb->query( $query );
    }

}
