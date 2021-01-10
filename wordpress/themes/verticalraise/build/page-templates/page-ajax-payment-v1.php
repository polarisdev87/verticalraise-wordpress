<?php

/* Template Name: Ajax Payment V1 */

use classes\app\emails\Custom_Mail;

/**
 * Handles payment processing via Ajax request
 * 1. Process Ajax $_POST request
 * 2. Verify WP Nonce
 * 3. Charge credit card & creates customer via Stripe
 * 4. Log sharing statistics for participant
 * 5. Flush cache for specific keys
 * 6. Dispatch email to supporter
 */
load_class( 'payment.class.php' );
load_class( 'payment_records.class.php' );
load_class( 'participant_records.class.php' );
load_class( 'goals.class.php' );

use classes\app\donation\Donations_Sum;
use classes\app\donation\Donations_Count;
use classes\app\donation_comments\Donation_Comments;
use classes\app\encryption\Encryption;
use classes\app\fundraiser\fundraiser_details;

$donation_comments = new Donation_Comments();

/**
 * Check for a Form Submit.
 */
if ( isset( $_POST ) ) {

    /* [stripeToken] => tok_1BECleFry9nn3Cd98BulBRdv, [stripeEmail] => robbmoran@gmail.com, [amount] => 35, [fname] => rob, [lname] => m, [email] => robbmoran@gmail.com, [fundraiser_id] => 49952, [uid] => 1 */

    /**
     * Check to see if the form submit is a Payment.
     */
    if ( isset( $_POST['amount'] ) ) {

        // Check for a nonce, fundraiser_id and uid $_POST parameters
        if ( empty( $_POST['nonce'] ) || empty( $_POST['fundraiser_id'] ) ) {
            echo "failure";
            exit();
        }

        /**
         * Security: Verify the Wordpress Nonce.
         */
        wp_verify_nonce( $_POST['nonce'], 'make-payment_' . $_POST['fundraiser_id'] . '_' );

        /**
         * Instantiate the Payments class.
         */
        $payment = new Payments();

        /**
         * Validate the $_POST parameters.
         */
        $is_valid = $payment->validate_params( $_POST );
        if ( $is_valid != true ) {
            echo 'failure'; // todo: add message
            exit;
		}

//        $charge = $payment->charge($payment->set_params($_POST)); // Charge the card
//        var_dump("DDDDDD",$charge); exit;

        /**
         * Charge the donators credit card using our Payment Class, Stripe Lib, and Stripe Elements.
         */
        try {

            /**
             * Attempt the charge through Stripe.
             */
            $charge = $payment->charge( $payment->set_params( $_POST ) ); // Charge the card
            // We return an array on successful charge

        } catch ( \Stripe\Exception\CardException $e ) {

	        if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
		        newrelic_notice_error( $e->getMessage(), $e );
	        }
	        $err = array(
		        'message' => $e->getMessage(),
	        );
	        wp_send_json( $err, 400 );

        } catch ( \Exception $e ) {

	        if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
		        newrelic_notice_error( $e->getMessage(), $e );
	        }
	        $err = array(
		        'message' => 'Server Error payment was not processed.',
	        );
	        wp_send_json( $err, 500 );

        }

        /**
         * If the charge is successful.
         */
        if ( !empty( $charge ) && is_array( $charge ) ) {

            try {
                /**
                 * Process the comment.
                 */
                $donation_comments->process( $charge['payment_id'], $charge['params']['fundraiser_id'], $_POST['comment'], $_POST['avatar_url'] );

                /**
                 * Instantiate the Participants Sharing Totals class.
                 */
                $participant_records = new Participant_Sharing_Totals();

                /**
                 * Instantiate the Payment Records class.
                 */
                $payment_records = new Payment_Records();

                // Set params
                $media         = $charge['params']['media'];
                $fundraiser_id = $charge['params']['fundraiser_id'];
                $uid           = $charge['params']['uid'];
                $amount        = $charge['stripe_charge']['amount'];

                // Media values we store donations for
                $allowed_media = [ 'sms', 'flyer' ];

                // Store the media type
                if ( !empty( $media ) && in_array( $media, $allowed_media ) ) {
                    $participant_records->adjust( $fundraiser_id, $uid, $media, $amount );
                }

                // Store the UID
                if ( !empty( $uid ) ) {
                    $participant_records->adjust( $fundraiser_id, $uid, 'supporters', 1 );
                    $participant_records->adjust( $fundraiser_id, $uid, 'total', $amount );
                }

                // Update Donations_sum
                if ( $amount != 0 ) {
                    $donations = new Donations_Sum();
                    $donations->increment_total( $fundraiser_id, $amount );

                    $donations_count = new Donations_Count();
                    $donations_count->increment_total( $fundraiser_id, 1 );
                }
            } catch ( Exception $e ) {
                if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
                    newrelic_notice_error( $e->getMessage(), $e );
                }
            }


            /* try {
              /**
             * Refactor the Fundraiser's Goal.
             */
            /* $goal = new Goals();
              // Set the goal info
              $goal_amount = $goal->get_goal($fundraiser_id);
              $fund_amount = $goal->get_amount($fundraiser_id);
              // Update Goal if necessary
              if ( $goal_amount <= $fund_amount ) {
              $goal->refactor_goal($fundraiser_id);
              $goal_amount = $goal->get_goal($fundraiser_id);
              }
              } catch(Exception $e) {
              if ( extension_loaded('newrelic') ) { // Ensure PHP agent is available
              newrelic_notice_error($e->getMessage(), $e);
              }
              } */

            try {
                /**
                 * Cache Update - just flush the cache for specific keys.
                 */
                delete_transient( 'get_donators_' . $fundraiser_id ); // Supporter List
                wp_cache_delete( 'get_amount_' . $fundraiser_id ); // Total Raised
                wp_cache_delete( 'get_num_supporters_' . $fundraiser_id ); // Number of Supporters
            } catch ( Exception $e ) {
                if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
                    newrelic_notice_error( $e->getMessage(), $e );
                }
            }

            /**
             * Set Permalinks
             */
            $permalink = get_permalink( $fundraiser_id );

            if ( is_user_logged_in() ) {
                // If user is logged in
                $permalink_facebook = $permalink . 'f/' . get_current_user_id();
                $permalink_twitter  = $permalink . 't/' . get_current_user_id();
                $permalink_email    = $permalink . 'email/' . get_current_user_id();
            } elseif ( isset( $uid ) ) {
                // If there is a uid attache dto the $_POST
                $permalink_facebook = $permalink . 'f/' . $uid;
                $permalink_twitter  = $permalink . 't/' . $uid;
                $permalink_email    = $permalink . 'email/' . $uid;
            } else {
                // Otherwise, just the general permalink
                $permalink_facebook = $permalink;
                $permalink_twitter  = $permalink;
                $permalink_email    = $permalink;
            }

            /**
             * Set Participant Pay
             */
            if ( !empty( $uid ) ) {
                $user_info       = get_userdata( $uid );
                $participant_pay = $user_info->display_name;
                do_action('send_email_to_participant', $charge['params']);
            } else {
                $participant_pay = get_post_meta( $fundraiser_id, 'team_name', true );
            }

            /**
             * Email the supporter
             */
            try {
                /**
                 * Instantiate the CustomMail class.
                 */
                $mail = new Custom_Mail();

                // Set custom html email template params
                $to      = $charge['params']['email'];
                $from    = _ADMIN_TO_EMAIL;
                $subject = 'Donation Receipt';
                $cc      = null;
                $reply   = null;

                $image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $fundraiser_id ), 'fundraiser-logo-thumb' );
                $image_url = $image_url[0];

                $descriptor = (strlen( get_the_title( $fundraiser_id ) ) > 22) ?
                        substr( get_the_title( $fundraiser_id ), 0, 18 ) . '...' :
                        substr( get_the_title( $fundraiser_id ), 0, 22 );

                if ( ! ( $_POST['stripe_connect'] == '1' || $_POST['force_connect'] == '1' ) ) {
                $descriptor = _CHECK_BY_MAIL_PAYEE;
                }

                $full_name = $_POST['fname'] . " " . $_POST['lname'];

                $tax_id = get_post_meta( $fundraiser_id, 'tax_id', true );
                $tax_id = apply_filters( 'vr_format_tax_id', $tax_id );

                $template_args = array(
                    'FULL_NAME'          => $full_name,
                    'AMOUNT'             => $amount,
                    'PERMALINK'          => $permalink_email,
                    'FUNDLOGO'           => $image_url,
                    'FUNDRAISER_TITLE'   => get_the_title( $fundraiser_id ),
                    'STATEMENT_TITLE'    => $descriptor,
                    'PARTICIPANT_PAY'    => $participant_pay,
                    'CHECK_PAY'          => get_post_meta( $fundraiser_id, 'check_pay', true ),
                    'FACEBOOK_URL'       => 'https://www.facebook.com/dialog/feed?app_id=' . _FACEBOOK_CLIENT_ID . '&display=popup&caption=' . urlencode( get_the_title( $fundraiser_id ) ) . '&link=' . urlencode( $permalink_facebook ) . '&redirect_uri=' . urlencode( $permalink_facebook ),
                    'ADMIN_EMAIL'        => _SIGNATURE_EMAIL,
                    'BLOG_NAME'          => get_bloginfo( 'name' ),
                    'TEMPLATE_DIRECTORY' => get_template_directory_uri(),
                    'PERMALINK_TWITTER'  => $permalink_twitter,
                    'CYEAR'              => date('Y'),
                    'TAX_ID'             => $tax_id,
                );

                /**
                 * Send the email.
                 */
                $sent = $mail->send_api( $to, $from, $cc, $subject, 'payment', $template_args );

            } catch ( Exception $e ) {
                if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
                    newrelic_notice_error( $e->getMessage(), $e );
                }
            }

            $encryption = new Encryption;

            // Generate the string to encode
            $string = $charge['stripe_charge']['transaction_id'] . '-' . current_time( "timestamp" );

            // Encrypt
            $transaction_id = $encryption->encrypt( $string );

            echo json_encode(['message' => 'success', 'message2' => $transaction_id]);
            //echo 'success';
            exit();
        }
    } else {
        echo 'failure';
        exit();
    }
} else {
    echo 'failure_type';
    exit();
}
