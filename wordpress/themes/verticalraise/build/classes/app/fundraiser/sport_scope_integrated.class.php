<?php

namespace classes\app\fundraiser;


class Sport_Scope_Integrated {

	public static function on_publish_pending_fundraiser( $post ) {

		$fundraiser_id           = $post->ID;
		$sport_scope_integration = get_post_meta( $fundraiser_id, 'sport_scope_integrated', true );
		if ( $sport_scope_integration === "1" ) {

			$custom_mail = new \classes\app\emails\Custom_Mail();

			$to      = SPORT_SCOPE_FUNDRAISER_APPROVED_EMAIL;
			$from    = "support@verticalraise.com";
			$cc      = null;
			$subject = "SS integrated Fundraiser";
			$from_name = "Support";

			$template      = "sport_scope_integrated_fundraiser_approved";
			$template_args = array(
				'FUNDRAISER_URL'  => get_bloginfo( 'url' ) . "/fundraiser/" . $post->post_name,
				'FUNDRAISER_NAME' => $post->post_title,
				'MAILING_ADDRESS' => get_post_meta( $fundraiser_id, 'mailing_address', true ),
				'STATE'           => get_post_meta( $fundraiser_id, 'state', true ),
			);

			$success = $custom_mail->send_api( $to, $from, $cc, $subject, $template, $template_args, $from_name );
			if ( ! $success ) {
				if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
					newrelic_notice_error( "Failed to delivery email of approved Sport Scope Integration Fundraiser ID: $fundraiser_id ", $success );
				}
			}
		}

	}

	public static function on_edit_sport_scope_integrated_fundraiser( $f_id ) {

		$post = get_post($f_id);
		if(!$post){
			return false;
		}

		$fundraiser_id           = $post->ID;
		$sport_scope_integration = get_post_meta( $fundraiser_id, 'sport_scope_integrated', true );
		if ( $sport_scope_integration === "1" ) {

			$custom_mail = new \classes\app\emails\Custom_Mail();

			$to      = SPORT_SCOPE_FUNDRAISER_APPROVED_EMAIL;
			$from    = "support@verticalraise.com";
			$cc      = null;
			$subject = "SS integrated Fundraiser";
			$from_name = "Support";

			$template      = "sport_scope_integrated_fundraiser_approved";
			$template_args = array(
				'FUNDRAISER_URL'  => get_bloginfo( 'url' ) . "/fundraiser/" . $post->post_name,
				'FUNDRAISER_NAME' => $post->post_title,
				'MAILING_ADDRESS' => get_post_meta( $fundraiser_id, 'mailing_address', true ),
				'STATE'           => get_post_meta( $fundraiser_id, 'state', true ),
			);

			$success = $custom_mail->send_api( $to, $from, $cc, $subject, $template, $template_args, $from_name );
			if ( ! $success ) {
				if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
					newrelic_notice_error( "Failed to delivery email of approved Sport Scope Integration Fundraiser ID: $fundraiser_id ", $success );
				}
			}
		}

	}

	public static function check_17_days_left() {
		global $wpdb;
		$results = $wpdb->get_results( "SELECT m.post_id FROM wp_postmeta m WHERE m.meta_key = 'end_date'
			AND CAST(m.meta_value as DATE) = date(adddate(now(), 10))
				HAVING m.post_id IN
	            (
	                SELECT m.post_id FROM wp_postmeta m WHERE m.meta_key = 'sport_scope_integrated'
	                AND m.meta_value = '1'
	            )
		" );

		foreach ($results as $result){
			$fundraiser_id = $result->post_id;
			$post = get_post($fundraiser_id);

			$query = $wpdb->prepare( " SELECT `email` FROM `email_input` WHERE f_id = %d ", array( $fundraiser_id ) );
			$email_results = $wpdb->get_results( $query );
			$email_list = "";
			foreach ($email_results as $email_result){
				$email_list = $email_list . $email_result->email . " <br/>";
			}

			$custom_mail = new \classes\app\emails\Custom_Mail();

			$to      = SPORT_SCOPE_FUNDRAISER_17_DAYS_LEFT;
			$from    = "support@verticalraise.com";
			$cc      = null;
			$subject = $post->post_title . " Email List";
			$from_name = "Support";

			$template      = "sport_scope_integrated_fundraiser_17_days_left";
			$template_args = array(
				'FUNDRAISER_URL'  => get_bloginfo( 'url' ) . '/fundraiser/' . $post->post_name,
				'FUNDRAISER_NAME' => $post->post_title,
				'MAILING_ADDRESS' => get_post_meta( $fundraiser_id, 'mailing_address', true ),
				'STATE'           => get_post_meta( $fundraiser_id, 'state', true ),
				'MAIL_LIST'       =>   $email_list
			);

			$success = $custom_mail->send_api( $to, $from, $cc, $subject, $template, $template_args, $from_name );
			if ( ! $success ) {
				if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
					newrelic_notice_error( "Failed to delivery email of 17 days left email list for Sport Scope Integration Fundraiser ID: $fundraiser_id", $success );
				}
			}

		}

	}

}
