<?php

/**
 * Template Name: Webhook
 */

load_class( 'paymentv3.class.php' );
load_class( 'payment_records.class.php' );
load_class( 'participant_records.class.php' );
load_class( 'goals.class.php' );

use classes\app\donation\Donations_Sum;
use classes\app\donation\Donations_Count;
use \classes\app\donation_comments\Donation_Comments;
use classes\app\emails\Custom_Mail;
use classes\app\encryption\Encryption;
use classes\app\fundraiser\Fundraiser_Media;


$data  = file_get_contents( 'php://input' );
$event = json_decode( $data );

if ( is_object( $event ) ) {

	switch ( $event->type ) {
		case 'checkout.session.completed':
			//error_log($data);
			if ( isset( $event->data->object->id ) ) {
				$payment_intent_id = $event->data->object->payment_intent;
				require_once TEMPLATEPATH . '/stripe-php/config.php';
				\Stripe\Stripe::setApiVersion( '2019-12-03' );
				$payment_intent = \Stripe\PaymentIntent::retrieve(
					$payment_intent_id
				);

				if ( $payment_intent->status === 'succeeded' && $payment_intent->charges->count() ) {

					$session = \Stripe\Checkout\Session::retrieve(
						$event->data->object->id
					);

					$charge  = $payment_intent->charges->data[0];
					$payment = new PaymentsV3();
					$result  = $payment->get_payment_by_transaction_id( $charge->id );
					if ( intval($result) === 0 ) {
						//process

						$email          = $charge->billing_details->email;
						$name           = $charge->billing_details->name;
						$fundraiser_id  = $session->metadata->fundraiser_id;
						$nonce          = $session->metadata->nonce;
						$amount         = $session->metadata->amount;
						$stripe_connect = $session->metadata->stripe_connect;
						$force_connect  = $session->metadata->force_connect;
						$comment        = $session->metadata->comment;
						$avatar         = $session->metadata->avatar_url;


						if ( isset( $charge->payment_method_details->card->wallet->type ) ) {
							$charge_type = $charge->payment_method_details->card->wallet->type;
						} else {
							$charge_type = 'card';
						}

						$charge_array = array(
							'transaction_id' => $charge->id,
							'amount'         => $amount,
							'charge_type'    => $charge_type,
						);

						$params     = $payment->set_params( $session->metadata->toArray() );
						$params['full_name'] = $name;
						$params['email'] = $email;
						$payment_id = $payment->insert_payment( $charge_array, $params );

						$charge = array(
							'stripe_charge' => $charge_array,
							'params'        => $params,
							'payment_id'    => $payment_id,
						);

						try {
							/**
							 * Process the comment.
							 */
							$donation_comments = new Donation_Comments();
							$donation_comments->process( $charge['payment_id'], $charge['params']['fundraiser_id'], $comment, $avatar );

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

							// Store the UID.
							if ( !empty( $uid ) ) {
								$participant_records->adjust( $fundraiser_id, $uid, 'supporters', 1 );
								$participant_records->adjust( $fundraiser_id, $uid, 'total', $amount );
							}

							// Update Donations_sum.
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

							if ( ! ( $stripe_connect == '1' || $force_connect == '1' ) ) {
								$descriptor = _CHECK_BY_MAIL_PAYEE;
							}

							$tax_id = get_post_meta( $fundraiser_id, 'tax_id', true );
							$tax_id = apply_filters( 'vr_format_tax_id', $tax_id );

							$template_args = array(
								'FULL_NAME'          => $name,
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

					} else {
						error_log("Charge id: is already processed");
					}
				}

				}
			break;
		case "payment_intent.succeeded":
			//error_log($data);
			if ( isset($event->data->object->id) ) {
				require_once( TEMPLATEPATH . '/stripe-php/config.php' );
				\Stripe\Stripe::setApiVersion("2019-12-03");

				$session = \Stripe\Checkout\Session::all( array(
					'limit'          => 1,
					'payment_intent' => $event->data->object->id
				) );
				if ( count( $session->data ) ) { // if the pi is from a checkout session exit
					//error_log("payment intent ". $event->data->object->id . "Already processed in session");
					break;
				}

				$payment_intent = \Stripe\PaymentIntent::retrieve(
					$event->data->object->id
				);

				if ( $payment_intent->status === 'succeeded' && count( $payment_intent->charges->data ) ) {
					$charge = $payment_intent->charges->data[0];
					$payment = new PaymentsV3();
					$result  = $payment->get_payment_by_transaction_id( $charge->id );
					if ( intval( $result ) == 0 ){
						//process
						$email          = $charge->billing_details->email;
						$name           = $charge->billing_details->name;
						$fundraiser_id  = $payment_intent->metadata->fundraiser_id;
						$nonce          = $payment_intent->metadata->nonce;
						$amount         = $payment_intent->metadata->amount;
						$stripe_connect = $payment_intent->metadata->stripe_connect;
						$force_connect  = $payment_intent->metadata->force_connect;
						$comment        = $payment_intent->metadata->comment;
						$avatar         = $payment_intent->metadata->avatar_url;

						if ( isset( $charge->payment_method_details->card->wallet->type ) ) {
							$charge_type = $charge->payment_method_details->card->wallet->type;
						} else {
							$charge_type = 'card';
						}

						$charge_array = array(
							'transaction_id' => $charge->id,
							'amount'         => $amount,
							'charge_type'    => $charge_type,
						);

						$params  = $payment->set_params( $payment_intent->metadata->toArray() );
						$params['full_name'] = $name;
						$params['email'] = $email;
						$payment_id = $payment->insert_payment( $charge_array, $params );
						$charge = array(
							'stripe_charge' => $charge_array,
							'params' => $params,
							'payment_id' => $payment_id,
						);

						try {
							/**
							 * Process the comment.
							 */
							$donation_comments = new Donation_Comments();
							$donation_comments->process( $charge['payment_id'], $charge['params']['fundraiser_id'], $comment, $avatar );

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
							// If user is logged in.
							$permalink_facebook = $permalink . 'f/' . get_current_user_id();
							$permalink_twitter  = $permalink . 't/' . get_current_user_id();
							$permalink_email    = $permalink . 'email/' . get_current_user_id();
						} elseif ( isset( $uid ) ) {
							// If there is a uid attached to the $_POST.
							$permalink_facebook = $permalink . 'f/' . $uid;
							$permalink_twitter  = $permalink . 't/' . $uid;
							$permalink_email    = $permalink . 'email/' . $uid;
						} else {
							// Otherwise, just the general permalink.
							$permalink_facebook = $permalink;
							$permalink_twitter  = $permalink;
							$permalink_email    = $permalink;
						}

						/**
						 * Set Participant Pay
						 */
						if ( ! empty( $uid ) ) {
							$user_info       = get_userdata( $uid );
							$participant_pay = $user_info->display_name;
							do_action( 'send_email_to_participant', $charge['params'] );
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

							if ( ! ( $stripe_connect == '1' || $force_connect == '1' ) ) {
								$descriptor = _CHECK_BY_MAIL_PAYEE;
							}

							$tax_id = get_post_meta( $fundraiser_id, 'tax_id', true );
							$tax_id = apply_filters( 'vr_format_tax_id', $tax_id );

							$template_args = array(
								'FULL_NAME'          => $name,
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

					} else {
						error_log( "Charge id: is already processed" );
					}
				}

			}

			break;
		default:
			break;
	}
}
