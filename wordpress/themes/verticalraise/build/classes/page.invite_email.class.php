<?php

use \classes\app\invite_emails\Invite_Emails;
use \classes\app\email_queue\Bulk;
use \classes\app\email_input\Bulk as Bulk_Input;

/**
 * Template functions and process form
 */
class Page_Invite_Emails extends Invite_Emails
{

    /**
     * Class constructor.
     * @param int $user_ID
     * @param int $fundraiser_ID
     */
    public function __construct( $user_ID, $fundraiser_ID ) {

        // Construct the parent class
        parent::__construct( $user_ID, $fundraiser_ID );

        $this->user_id       = $user_ID;
        $this->fundraiser_id = $fundraiser_ID;

        $this->bulk       = new Bulk();
        $this->bulk_input = new Bulk_Input();
    }

    /**
     * Send the invite email when the form is submitted.
     */
    public function process_form() {

        if ( !empty( $_POST ) ) {

            // Check security
            $this->security();

            // Process form
            $results = $this->process_input();

            // Results to store
            $valid_emails = $results['valid_emails'];
            $type         = $results['template_type'];
            
            // Check if user is logged in
            if ( is_user_logged_in() ) {
                $logged_in = 1;
            } else {
                $logged_in = 0;
            }
            
            if ( isset($_POST['parent']) && $_POST['parent'] == '1') {
                $parent = 1;
            } else {
                $parent = 0;
            }
            
            if ( isset($_GET['page']) && $_GET['page'] == 'thankyou') {
                $share_type = 1;
            } else {
                $share_type = 0;
            }
            
            // Get logged in user id
            $logged_in_u_id = get_current_user_id();

            // Queue Emails
            if ( !empty( $valid_emails ) ) {
                $this->bulk->queue( $valid_emails, $this->user_id, $this->fundraiser_id, $type, $logged_in, $logged_in_u_id, $parent, $share_type );
                $this->bulk_input->add( $valid_emails, $this->user_id, $this->fundraiser_id, $type );
            }
        } else {
            $results = null;
        }

        return $results;
    }

    /**
     * If there are invalid emails display them to the user.
     * @param  int   $nonvalid          Simple flag if we have invalid emails or not.
     * @param  array $invalidateAddress The invalid email addresses.
     * @return string Ob cleaned html.
     */
    public function invalid_emails( $nonvalid, $invalidateAddress ) {
        if ( !empty( $invalidateAddress ) ) {
            echo $this->invalid_emails_output( $nonvalid, $invalidateAddress );
        }
    }

    /**
     * Output the invalid emails.
     * @param  int   $nonvalid          Simple flag if we have invalid emails or not.
     * @param  array $invalidateAddress The invalid email addresses.
     * @return string Ob cleaned html.
     */
    public function invalid_emails_output( $nonvalid, $invalidateAddress ) {
        ob_start();
        //if ( $nonvalid != 0 ) {
            if ( !empty( $invalidateAddress ) && is_array( $invalidateAddress ) ) {

                $warning     = "WARNING!";
                $invalid_emails_count = count( $invalidateAddress );
                if ( $invalid_emails_count > 1 ) {
                    $description = "$invalid_emails_count email addresses were invalid/low quality. Email was not delivered to:";
                } else {
                    $description = "$invalid_emails_count email address was invalid/low quality. Email was not delivered to:";
                }

                echo "<p style='color: red;font-weight:400'>{$warning}<br>{$description}</p>";
                if ( !empty( $invalidateAddress ) && is_array( $invalidateAddress ) ) {
                    echo "<p style='color: #f91717;height:100px;overflow:auto'>";
                    foreach ( $invalidateAddress as $address ) {
                        if ( !empty( $address ) ) {
                            echo "<strong class='invalidEmail'>" . $address . "</strong><br>";
                        }
                    }
                    echo "</p>";
                }
            }
        //}
        $contents = ob_get_clean();
        return $contents;
    }

    /*
     *
     */

    public function duplicated_emails( $nonvalid, $duplicated_emails ) {
        if ( !empty( $duplicated_emails ) ) {
            echo $this->duplicated_emails_output( $nonvalid, $duplicated_emails );
        }
    }

    /**
     * Output the invalid emails.
     * @param  int   $nonvalid          Simple flag if we have invalid emails or not.
     * @param  array $duplicated_emails The invalid email addresses.
     * @return string Ob cleaned html.
     */
    public function duplicated_emails_output( $nonvalid, $duplicated_emails ) {
        ob_start();
        //if ( $nonvalid != 0 ) {
            if ( !empty( $duplicated_emails ) && is_array( $duplicated_emails ) ) {

                $warning     = "WARNING!";
                $duplicated_emails_count = count( $duplicated_emails );

                $description = "$duplicated_emails_count emails were duplicates and already sent through this fundraiser. The following emails were not delivered:";

                echo "<p style='color: red;font-weight:400'>{$warning}<br>{$description}</p>";
                if ( !empty( $duplicated_emails ) && is_array( $duplicated_emails ) ) {
                    echo "<p style='color: #f91717;height:100px;overflow:auto'>";
                    foreach ( $duplicated_emails as $address ) {
                        if ( !empty( $address ) ) {
                            echo "<strong class='invalidEmail'>" . $address . "</strong><br>";
                        }
                    }
                    echo "</p>";
                    echo "<br>";
                }
            }
        //}
        $contents = ob_get_clean();
        return $contents;
    }

    /*
     *
     */

    public function success_emails( $nonvalid, $valid_emails ) {
        if ( !empty( $valid_emails ) ) {
            echo $this->success_emails_output( $nonvalid, $valid_emails );
        }
    }

    /**
     * Output the invalid emails.
     * @param  int   $nonvalid          Simple flag if we have invalid emails or not.
     * @param  array $valid_emails The invalid email addresses.
     * @return string Ob cleaned html.
     */
    public function success_emails_output( $nonvalid, $valid_emails ) {
        ob_start();
        //if ( $nonvalid == 0 ) {
        if ( !empty( $valid_emails ) && is_array( $valid_emails ) ) {

            $success     = "SUCCESS!";
            $valid_emails_count = count( $valid_emails );
            if ( $valid_emails_count > 1 ) {
                $description = "The email was successfully sent to $valid_emails_count addresses";
            } else {
                $description = "The email was successfully sent to $valid_emails_count address";
            }
            echo "<p style='color: #7de078'><br>{$description}<br></p>";

        }
        //}
        $contents = ob_get_clean();
        return $contents;
    }

    /**
     * Generate a nonce to identify the user in the user submits the form while signed out.
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
        return wp_create_nonce( 'email-submit' );
    }

    /**
     * Check for a form nonce to make sure the $_POST form submission is coming from our website.
     * @return void
     */
    private function security() {
        $nonce = (!empty( $_POST['_form_nonce'] ) ) ? $_POST['_form_nonce'] : false;

        // Nonce not valid
        if ( !wp_verify_nonce( $nonce, 'email-submit' ) || empty( $nonce ) ) {
            header( 'Location: ' . get_site_url() );
        }
    }

}
