<?php

/* Template Name: Participant Check Upload Ajax */

use classes\app\fundraiser\Fundraiser_Ended;
use classes\app\emails\Custom_Mail;
use \classes\models\mixed\Admins;

if ( is_user_logged_in() ) {

	$fundraiser_id  = $_POST['fundraiser_id'];
	$participant_id = $_POST['uid'];
	$base64_data    = $_POST['image_b64'];

	$uid = get_current_user_id();

	$fundraiser_end      = new Fundraiser_Ended( $fundraiser_id );
	$enddate             = $fundraiser_end->f_enddate();
	$end_date            = \Datetime::createFromFormat( 'U', $enddate );
	$fundraiser_end_date = $end_date->format( 'Y-m-d' );

	$admins = new Admins();
	$is_admin = $admins->is_fundraiser_admin_or_site_admin( $uid, $fundraiser_id );
	if ( $is_admin ) {
		$redirect_to = "/single-fundraiser/?fundraiser_id={$_POST['fundraiser_id']}";
	} else {
		$redirect_to = "/participant-fundraiser/?fundraiser_id={$_POST['fundraiser_id']}";
	}

	$participant_name = "";

	if ( $participant_id != 0 ) {
		$user_data        = get_userdata( $participant_id );
		$participant_name = $user_data->first_name . ' ' . $user_data->last_name;
	}

	$date        = new \Datetime();
	$upload_date = $date->format( 'Y-m-d H:i' );

	$permalink         = get_permalink( $fundraiser_id );
	$landing_page_link = $permalink . 'c/' . $participant_id;
	$fundraiser_name   = get_the_title( $fundraiser_id );

	$to        = 'support@verticalraise.com';
	$from      = 'no-reply@verticalraise.com';
	$subject   = "Mobile Check Uploaded for $fundraiser_name";
	$template  = 'check_uploaded';
	$from_name = 'VerticalRaise Support';

	$template_args = array(
		'LANDING_PAGE_LINK'   => $landing_page_link,
		'UPLOAD_DATE'         => $upload_date,
		'FUNDRAISER_NAME'     => $fundraiser_name,
		'FUNDRAISER_ID'       => $fundraiser_id,
		'PARTICIPANT_NAME'    => $participant_name,
		'USER_ID'             => $participant_id,
		'FUNDRAISER_END_DATE' => $fundraiser_end_date,
	);

	$attachment = array(
		'base64_data' => $base64_data,
		'mime_type'   => 'image/jpeg',
		'file_name'   => 'check.jpg',
	);

	$cm            = new Custom_Mail();
	$upload_status = $cm->send_api( $to, $from, null, $subject, $template, $template_args, $from_name, null, $attachment );

	if ( !$upload_status ) {
		$error = array(
			"message" => "Failed to upload check. Try again later",
			"redirect_to" => $redirect_to,
		);
		wp_send_json( $error, 400 );
	}

	$user_data        = get_userdata( $uid );
	$participant_name = $user_data->first_name . ' ' . $user_data->last_name;

	$to        = $user_data->user_email;
	$from      = 'support@verticalraise.com';
	$subject   = "Mobile Check Successfully Uploaded  $fundraiser_name";
	$template  = 'check_uploaded_confirmation';
	$from_name = 'VerticalRaise Support';



	$template_args = array(
		'PARTICIPANT_NAME' => $participant_name,
	);

	$cm                    = new \classes\app\emails\Custom_Mail();
	$confirmation_delivery = $cm->send_api( $to, $from, null, $subject, $template, $template_args, $from_name );

	$success = array(
		"message" => "Check Successfully Uploaded",
		"redirect_to" => $redirect_to,
	);

	wp_send_json( $success, 200 );

} else {
	$error = array(
		"message" => "User must be logged in.",
		"redirect_to" => "/",
	);
	wp_send_json( $error, 403 );
}
