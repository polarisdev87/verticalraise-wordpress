<?php

/* Template Name: Ajax Payment V2 */

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
use \classes\app\donation_comments\Donation_Comments;
use classes\app\encryption\Encryption;
use classes\app\fundraiser\Fundraiser_Media;


if ( isset( $_POST['create_session'] ) ) {

	require_once( TEMPLATEPATH . '/stripe-php/config.php' );
	\Stripe\Stripe::setApiVersion("2019-12-03");

	$email             = $_POST['email'];
	$amount            = intval( $_POST['amount'] ) * 100;
	$fundraiser_id     = $_POST['fundraiser_id'];
	$fundraiser_title  = get_the_title( $fundraiser_id );
	$stripe_connect    = $_POST['stripe_connect'];
	$force_connect     = $_POST['force_connect'];
	$stripe_account_id = $_POST['stripe_account_id'];
	$fname             = $_POST['fname'];
	$lname             = $_POST['lname'];
	$media             = $_POST['media'];
	$uid               = $_POST['uid'];
	$nonce             = $_POST['nonce'];
	$our_fee           = intval( $_POST['our_fee'] );
	$rep_code          = floatval( $_POST['rep_code'] );

	$payment = new Payments();

	/**
	 * Validate the $_POST parameters.
	 */
	$is_valid = $payment->validate_params( $_POST );
	if ( $is_valid !== true ) {
		wp_send_json(array('message' => "Missing or incorrect fields: " . $is_valid), 500);
	}

	$fundraise_mediaObj = new Fundraiser_Media();
	$image_url          = $fundraise_mediaObj->get_fundraiser_logo( $fundraiser_id );
	//$image_url = str_replace("http://verticalraise-local.com", 'https://c26994ef.ngrok.io', $image_url); //TODO: REMOVE LINE

	$permalink = get_permalink( $fundraiser_id );
	$cancel_url = $permalink . 's/' . $uid;

	$session_info = array(
		'payment_method_types' => array( 'card' ),
		'customer_email'       => $email,
		'line_items'           => array(
			array(
				'name'        => 'Donation',
				'description' => $fundraiser_title,
				'images'      => array( $image_url ),
				'amount'      => $amount,
				'currency'    => 'usd',
				'quantity'    => 1,
			)
		),
		'submit_type'          => 'donate',
		'success_url'          => get_bloginfo( 'url' ) . '/ajax-payment-v2/?session_id={CHECKOUT_SESSION_ID}',
		'cancel_url'           => $cancel_url,
		'metadata'             => $_POST,
	);


	if ( $stripe_connect || $force_connect ) {

		if ( $stripe_connect ) {
			$fee = 0;
			if ($our_fee == 2) {
				if ($rep_code == 0) {
					$fee = 0;
				} else {
					$fee = (100 - $rep_code) / 100 * $amount;
				}
			}
			$amount = $amount - $fee;
		}

		$payment_intent_data = array(
			'payment_intent_data' => array(
				'transfer_data'        => array(
					'destination' => $stripe_account_id,
					'amount'      => $amount,
				),
				'statement_descriptor' => substr( $fundraiser_title, 0, 22 ),
			)
		);
		$session_info        = array_merge( $session_info, $payment_intent_data );
	}

	try {
		$session = \Stripe\Checkout\Session::create( $session_info );
		wp_send_json( [ 'id' => $session->id ], 200 );
	} catch ( \Exception $e ) {
		wp_send_json( [ 'message' => $e->getMessage() ], 500 );
	}


}

if ( isset( $_POST['update_payment_intent'] ) ) {

	require_once( TEMPLATEPATH . '/stripe-php/config.php' );
	\Stripe\Stripe::setApiVersion("2019-12-03");

	$payment_intent_id = $_POST['payment_intent_id'];


	$email             = $_POST['email'];
	$amount            = intval( $_POST['amount'] ) * 100;
	$fundraiser_id     = $_POST['fundraiser_id'];
	$fundraiser_title  = get_the_title( $fundraiser_id );
	$stripe_connect    = $_POST['stripe_connect'];
	$force_connect     = $_POST['force_connect'];
	$stripe_account_id = $_POST['stripe_account_id'];
	$fname             = $_POST['fname'];
	$lname             = $_POST['lname'];
	$media             = $_POST['media'];
	$uid               = $_POST['uid'];
	$nonce             = $_POST['nonce'];
	$our_fee           = intval( $_POST['our_fee'] );
	$rep_code          = floatval( $_POST['rep_code'] );

	$payment = new Payments();

	/**
	 * Validate the $_POST parameters.
	 */
	$is_valid = $payment->validate_params( $_POST );
	if ( $is_valid !== true ) {
		wp_send_json(array('message' => "Missing or incorrect fields: " . $is_valid), 500);
	}

	$payment_intent_info = array(
		'amount'               => $amount,
		'currency'             => 'usd',
		'payment_method_types' => array( 'card' ),
		'description'          => $fundraiser_title,
		'statement_descriptor' => substr( $fundraiser_title, 0, 22 ),
		'metadata'             => $_POST,
		'receipt_email'        => $email,
	);

	if ( $stripe_connect || $force_connect ) {

		if ( $stripe_connect ) {
			$fee = 0;
			if ($our_fee == 2) {
				if ($rep_code == 0) {
					$fee = 0;
				} else {
					$fee = (100 - $rep_code) / 100 * $amount;
				}
			}
			$amount = $amount - $fee;
		}

		$extra_data = array(
			'transfer_data'        => array(
				'amount'      => $amount,
			),
			'statement_descriptor' => substr( $fundraiser_title, 0, 22 ),
		);
		$payment_intent_info        = array_merge( $payment_intent_info, $extra_data );
	}

	try {

		$payment_intent = \Stripe\PaymentIntent::update(
			$payment_intent_id,
			$payment_intent_info
		);

		wp_send_json( [ 'status' => $payment_intent->client_secret, 'id' => $payment_intent->id ], 200 );


	} catch ( \Exception $e ) {
		wp_send_json( array( 'message' => $e->getMessage() ), 500 );
	}


}

/**
 * Check for stripe redirect.
 */
if ( isset( $_GET['session_id'] ) ) {

	require_once( TEMPLATEPATH . '/stripe-php/config.php' );
	\Stripe\Stripe::setApiVersion("2019-12-03");

	$session_id = $_GET['session_id'];
	$session = \Stripe\Checkout\Session::retrieve(
		$session_id
	);

	$pi = \Stripe\PaymentIntent::retrieve(
		$session->payment_intent
	);

	if ($pi->status !== 'succeeded') {
		wp_send_json(['message' => 'Error, payment not succeeded'], 400);
	}

	$email          = $session->metadata['email'];
	$fundraiser_id  = $session->metadata['fundraiser_id'];
	$nonce          = $session->metadata['nonce'];
	$amount         = $session->metadata['amount'];
	$fname          = $session->metadata['fname'];
	$lname          = $session->metadata['lname'];
	$stripe_connect = $session->metadata['stripe_connect'];
	$force_connect  = $session->metadata['force_connect'];
	$comment        = $session->metadata['comment'];
	$avatar         = $session->metadata['avatar_url'];
	$media          = $session->metadata['media'];
	$uid           =  $session->metadata['uid'];


	if ( wp_verify_nonce( $nonce, 'make - payment_ ' . $fundraiser_id . '_ ' ) ){

		$payment = new Payments();
		$params  = $payment->set_params( $session->metadata->toArray() );

		$payment_intent = \Stripe\PaymentIntent::retrieve(
			$session->payment_intent
		);

		if ( $payment_intent->charges->count() ) {
			$charge       = $payment_intent->charges->data[0];

			$charge_array = array(
				'transaction_id' => $charge->id,
				'amount'         => $amount,
			);

			$charge = array(
				'stripe_charge' => $charge_array,
				'params' => $params,
			);

			$encryption = new Encryption;

			// Generate the string to encode.
			$string = $charge['stripe_charge']['transaction_id'] . '-' . current_time( 'timestamp' );

			// Encrypt the transaction id.
			$transaction_id = $encryption->encrypt( $string );

			$url = get_bloginfo( 'url' ) . "/thank-you-payment/?fundraiser_id=$fundraiser_id" .
				"&fname=$fname&lname=$lname&email=$email&media=$media&uid=$uid&transaction_id=$transaction_id&amount=$amount";

			header( "Location: $url" );
			exit();
		}

	} else {
		wp_send_json(['error' => 'Expired or invalid information'], 400);
	}

}

/**
 * Check for payment intent for redirect.
 */
if ( isset( $_GET['payment_intent_id'] ) ) {

	require_once( TEMPLATEPATH . '/stripe-php/config.php' );
	\Stripe\Stripe::setApiVersion("2019-12-03");

	$payment_intent_id = $_GET['payment_intent_id'];

	$payment_intent = \Stripe\PaymentIntent::retrieve(
		$payment_intent_id
	);

	if ($payment_intent->status !== 'succeeded') {
		wp_send_json(['message' => 'Error, payment not succeeded'], 400);
	}

	$email          = $payment_intent->metadata['email'];
	$fundraiser_id  = $payment_intent->metadata['fundraiser_id'];
	$nonce          = $payment_intent->metadata['nonce'];
	$amount         = $payment_intent->metadata['amount'];
	$fname          = $payment_intent->metadata['fname'];
	$lname          = $payment_intent->metadata['lname'];
	$stripe_connect = $payment_intent->metadata['stripe_connect'];
	$force_connect  = $payment_intent->metadata['force_connect'];
	$comment        = $payment_intent->metadata['comment'];
	$avatar         = $payment_intent->metadata['avatar_url'];
	$media          = $payment_intent->metadata['media'];
	$uid           =  $payment_intent->metadata['uid'];


	if ( wp_verify_nonce( $nonce, 'make - payment_ ' . $fundraiser_id . '_ ' ) ){

		$payment = new Payments();
		$params  = $payment->set_params( $payment_intent->metadata->toArray() );

		if ( $payment_intent->charges->count() ) {
			$charge       = $payment_intent->charges->data[0];

			$charge_array = array(
				'transaction_id' => $charge->id,
				'amount'         => $amount,
			);

			$charge = array(
				'stripe_charge' => $charge_array,
				'params' => $params,
			);

			$encryption = new Encryption;

			// Generate the string to encode
			$string = $charge['stripe_charge']['transaction_id'] . '-' . current_time( "timestamp" );

			// Encrypt
			$transaction_id = $encryption->encrypt( $string );

			$url = get_bloginfo('url') . "/thank-you-payment/?fundraiser_id=$fundraiser_id" .
			       "&fname=$fname&lname=$lname&email=$email&media=$media&uid=$uid&transaction_id=$transaction_id&amount=$amount";

			header("Location: $url");
			exit();
		}

	} else {
		wp_send_json(['error' => 'Expired or invalid information'], 400);
	}

}
