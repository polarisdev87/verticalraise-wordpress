<?php

namespace classes\app\donation;

use classes\app\emails\Custom_Mail;
use classes\models\tables\Donations;
use classes\models\tables\Donation_Comments;
use classes\models\tables\Participant_Fundraiser_Details;
use classes\app\donation\Donations_Count;
use classes\app\donation\Donations_Sum;

load_class( 'participant_records.class.php' );

class Donation {

	public static function save_e_check_donation() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json( [ 'message' => "Not allowed." ], 500 );
		}

		$f_id            = $_POST['f_id'];
		$uid             = $_POST['uid'];
		$donation_amount = floatval( $_POST['donation_amount'] );
		$donor_comment   = $_POST['donor_comment'];
		$email           = $_POST['donor_email'];
		$full_name       = $_POST['donor_name'];

		$args      = array(
			"f_id"            => $f_id,
			"uid"             => $uid,
			"anonymous"       => ( $_POST['anonymous'] != "" ) ? 1 : 0,
			"donor_name"      => $_POST['donor_name'],
			"donor_email"     => $email,
			"donation_amount" => $donation_amount,
			"donation_type"   => $_POST['donation_type'],
		);
		$donations = new Donations();
		$result    = $donations->insert_donation_check( $args );
		if ( ! $result ) {
			wp_send_json( [ "message" => "Error: Donation was not saved" ], 400 );
		}

		if ( ! empty( $email ) ) {
			Donation::send_donation_receipt( array(
				"donor_email"     => $email,
				"donor_full_name" => $full_name,
				"donation_amount" => $donation_amount,
				"fundraiser_id"   => $f_id,
				"user_id"         => $uid,
			) );
		}
		if ( strlen( $donor_comment ) ) {
			$donations_comments = new Donation_Comments();
			$comment            = (object) array(
				'd_id'       => $result,
				'f_id'       => $f_id,
				'comment'    => $donor_comment,
				'avatar_url' => "",
			);
			$donations_comments->insert( $comment );
		}

		$participant_records = new \Participant_Sharing_Totals();

		if ( ! empty( $uid ) ) {
			$participant_records->adjust( $f_id, $uid, 'supporters', 1 );
			$participant_records->adjust( $f_id, $uid, 'total', $donation_amount );
		}

		if ( $donation_amount > 0 ) {
			$donations_sum = new Donations_Sum();
			$donations_sum->increment_total( $f_id, $donation_amount );

			$donations_count = new Donations_Count();
			$donations_count->increment_total( $f_id, 1 );
		}

		delete_transient( 'get_donators_' . $f_id ); // Supporter List
		wp_cache_delete( 'get_amount_' . $f_id ); // Total Raised
		wp_cache_delete( 'get_num_supporters_' . $f_id ); // Number of Supporters

		wp_send_json( [ "message" => "Donation saved." ], 200 );

	}


	public static function change_donor_name() {

		$d_id       = $_POST['d_id'];
		$donor_name = $_POST['donor_name'];
		if ( ! intval( $d_id ) || strlen( $donor_name ) < 3 ) {
			wp_send_json( [ 'message' => "Invalid request" ], 400 );
		}


		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json( [ 'message' => "Not allowed." ], 500 );
		}

		$donations = new Donations();
		$success   = $donations->change_donor_name( $d_id, $donor_name );
		if ( $success ) {
			wp_send_json( array( "message" => "ok" ), 200 );
		} else {
			wp_send_json( array( 'message' => "failed to change name" ), 400 );
		}
	}

	public static function get_fundraiser_participants() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json( [ 'message' => 'Not allowed.' ], 500 );
		}

		$donations = new Donations();
		$f_id      = $donations->get_fundraiser_id( $_POST['d_id'] );

		$fundraiser_participants = new Participant_Fundraiser_Details();
		$participants            = $fundraiser_participants->get_participants( $f_id );

		if ( $participants ) {
			wp_send_json( $participants, 200 );
		} else {
			wp_send_json( array( 'message' => "failed to retrieve" ), 400 );
		}
	}

	public static function change_donation_recipient() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json( [ 'message' => 'Not allowed.' ], 500 );
		}

		$donations = new Donations();
		$status    = $donations->change_recipient( $_POST['d_id'], $_POST['uid'] );

		if ( $status ) {
			wp_send_json( array( "message" => "Recipient has changed" ), 200 );
		} else {
			wp_send_json( array( "message" => "error" ), 400 );
		}

	}

	public static function delete_donation() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json( [ 'message' => 'Not allowed.' ], 500 );
		}

		$donations = new Donations();
		$status    = $donations->set_donation_to_deleted( $_POST['d_id'] );
		if ( $status ) {
			wp_send_json( [ 'message' => "Donation deleted" ], 200 );
		} else {
			wp_send_json( [ 'message' => "Cannot delete donation. Something went wrong" ], 400 );
		}
	}

	/**
	 * @param $args
	 *      donor_email
	 *      donor_full_name
	 *      donation_amount
	 *      fundraiser_id
	 *      user_id
	 */
	public static function send_donation_receipt($args){
		/**
		 * Email the supporter
		 */
		try {

			$fundraiser_id = $args['fundraiser_id'];
			$user_id =  $args['user_id'];

			/**
			 * Instantiate the CustomMail class.
			 */
			$mail = new Custom_Mail();

			// Set custom html email template params
			$to      = $args['donor_email'];
			$from    = _ADMIN_TO_EMAIL;
			$subject = 'Donation Receipt';
			$cc      = null;
			$reply   = null;

			$image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $fundraiser_id ), 'fundraiser-logo-thumb' );
			$image_url = $image_url[0];

			$descriptor = (strlen( get_the_title( $fundraiser_id ) ) > 22) ?
				substr( get_the_title( $fundraiser_id ), 0, 18 ) . '...' :
				substr( get_the_title( $fundraiser_id ), 0, 22 );


			$tax_id = get_post_meta( $fundraiser_id, 'tax_id', true );
			$tax_id = apply_filters( 'vr_format_tax_id', $tax_id );


			/**
			 * Set Participant Pay
			 */
			if ( !empty( $user_id ) ) {
				$user_info       = get_userdata( $user_id );
				$participant_pay = $user_info->display_name;
				do_action('send_email_to_participant', array('fundraiser_title' => get_the_title( $fundraiser_id) , 'uid' => $user_id ));
			} else {
				$participant_pay = get_post_meta( $fundraiser_id, 'team_name', true );
			}

			$permalink = get_permalink( $fundraiser_id);
			if ( !empty( $user_id ) ) {
				// If there is a uid attache dto the $_POST
				$permalink_facebook = $permalink . 'f/' . $user_id;
				$permalink_twitter  = $permalink . 't/' . $user_id;
				$permalink_email    = $permalink . 'email/' . $user_id;
			} else {
				// Otherwise, just the general permalink
				$permalink_facebook = $permalink;
				$permalink_twitter  = $permalink;
				$permalink_email    = $permalink;
			}
			$template_args = array(
				'FULL_NAME'          => $args['donor_full_name'],
				'AMOUNT'             => $args['donation_amount'],
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

		} catch ( \Exception $e ) {
			error_log($e->getMessage());
			if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
				newrelic_notice_error( $e->getMessage(), $e );
			}
		}
	}

}