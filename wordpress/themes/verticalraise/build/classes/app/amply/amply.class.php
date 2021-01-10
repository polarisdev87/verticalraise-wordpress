<?php

namespace classes\app\amply;

use classes\app\fundraiser\Fundraiser_Media;
use classes\models\tables\GiveAmply;

class Amply {

	// Prod credentials.
	static $create_donation_url     = 'https://giveamply.com/api/v1/donation';
	static $create_organization_url = 'https://giveamply.com/api/v1/organization';

	static $api_key_vrp = "868f11b276bec928d88fca45369d00346147ac19";
	static $api_key_vrn = "5945745dbebf85075e04d7d127fd55f4363d0ee7";

	// Dev Credentials.
	static $create_donation_url_dev     = "https://sandbox.giveamply.com/api/v1/donation";
	static $create_organization_url_dev = "https://sandbox.giveamply.com/api/v1/organization";

	static $api_key_vrp_dev = "6981c9d8d0ac7759a7174a010a69d7c20649b5b7";
	static $api_key_vrn_dev = "95adfadb98d3475fd90f8c3dd8d5052e2f4828de";

	public static function create_donation( $email, $name, $company, $amount, $tr_id, $f_id ) {

		$table = new GiveAmply();

		// Production.
		$url = self::$create_donation_url;
		$key = self::$api_key_vrp;

		// Dev.
		if ( server_type() === "dev" ) {
			$url = self::$create_donation_url_dev;
			$key = self::$api_key_vrp_dev;
		}

		$payee_name   = get_post_meta( $f_id, "check_pay", true );
		$tax_id       = get_post_meta( $f_id, "tax_id", true );
		$amply_org_id = $table->get_org_id($f_id);

		if ( ! $amply_org_id ) {
			return;
		}

		$fundraiser_title = get_the_title( $f_id );
		$campaign_msg = get_post_meta( $f_id, "campaign_msg", true );
		$f_image   = new Fundraiser_Media();
		$image_url = $f_image->get_fundraiser_logo( $f_id );

		$ch = curl_init( $url );

		$chapter = 0;
		// If Chapter
		//if ( empty( $tax_id ) ) {
			$tax_id = '85-3237286'; // use vr tax id
			$chapter = 1;
		//}

		$donation_data = array(
			"organization_id" => $amply_org_id,
			"donor"           => array(
				"email" => $email,
				"name"  => $name,
			),
			"company"         => array(
				"name" => $company
			),
			"amount"          => $amount,
			"transaction_id"  => $tr_id,
			"custom"          => array(
				"payee_name"      => $payee_name,
				"payee_tax_id"    => $tax_id,
				"fundraiser_name" => $fundraiser_title,
				"fund_name" 	  => $fundraiser_title,
				"fund_ein" 		  => $tax_id,
				"fund_mission" 	  => $campaign_msg,
				"fund_logo"		  => $image_url,
			),
		);

		if ( $chapter == 1 ) {
			$donation_data['ein'] = $tax_id;
			$doantion_data['organization_name'] = $fundraiser_title;
		}

		$donation_json = json_encode( $donation_data );

		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
				'Content-Type:application/json',
				"X-Authorization:$key",
				'Accept:application/json'
			)
		);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $donation_json );

		$result      = curl_exec( $ch );
		$status_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

		curl_close( $ch );

		if ( ! $status_code === 200 ) {
			error_log( $result );
			if ( extension_loaded( 'newrelic' ) ) {
				newrelic_notice_error( $result );
			}
		}

	}


	public static function create_organization( $post, $show = false ) {

		$giveAmply_table = new GiveAmply();

		$f_id = $post->ID;

		$amply_org_id = $giveAmply_table->get_org_id($f_id);

		// Already has amply organization id
		if ( ! empty( $amply_org_id ) ) {
			if ( $show == true ) {
				echo "has amply org id: {$amply_org_id}<br>";
			}
			return;
		}

		// Prod
		$url   = self::$create_organization_url;
		$key   = self::$api_key_vrp; // Platform
		$key_n = self::$api_key_vrn; // Non Profit

		// Dev
		/*if ( server_type() === "dev" ) {
			$url   = self::$create_organization_url_dev;
			$key   = self::$api_key_vrp_dev; // Platform
			$key_n = self::$api_key_vrn_dev; // Non  Profit
		}*/

		$fundraiser_title = get_the_title( $f_id );
		$tax_id           = get_post_meta( $f_id, "tax_id", true );

		$type = 'non-profit';

		//if ( empty( $tax_id ) ) {
			$key    = $key_n; // non profit - not platform
			$tax_id = '85-3237286'; // use vr tax id
			$type   = 'chapter'; // set to chapter inside vr
		//}

		$fundraiser_link = get_site_url( $f_id ) . "/fundraiser/" . str_replace( " ", "-", strtolower( $fundraiser_title ) );

		$campaign_msg = get_post_meta( $f_id, "campaign_msg", true );
		$phone        = '2086995877';
		$email        = 'support@verticalraise.com';

		$f_image   = new Fundraiser_Media();
		$image_url = $f_image->get_fundraiser_logo( $f_id );
		$name = "Vertical Raise - " . $fundraiser_title;

		$nonprofit_data = array(
			"name"          => $name,
			"type"          => $type,
			"ein"           => $tax_id,
			"address"       => array(
				"street"  => "505 E Front Ave #300-3",
				"street2" => "",
				"city"    => "Coeur d Alene",
				"state"   => "ID",
				"zip"     => "83814",
				"country" => "US",
			),
			"url"           => $fundraiser_link,
			"mission"       => substr( $campaign_msg, 0, 255 ),
			"logo_url"      => $image_url,
			"phone"         => $phone,
			"contact_email" => $email
		);

		$nonprofit_json = json_encode( $nonprofit_data );

		$ch = curl_init( $url );

		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
				'Content-Type:application/json',
				"X-Authorization:$key",
				'Accept:application/json'
			)
		);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $nonprofit_json );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );

		$result      = curl_exec( $ch );
		$status_code = curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );

		curl_close( $ch );

		if ( $status_code === 200 ) {
			if ( extension_loaded( 'newrelic' ) ) {
				newrelic_notice_error( $result );
			}
			$aux    = json_decode( $result );
			$org_id = $aux->organization->id;
			$giveAmply_table->insert( $f_id, $org_id, $nonprofit_json, $result );

			if ( $show == true ) {
				echo 'org id: ' . $org_id . '<br>';
			}
		} else {
			if ( $show == true ) {
				echo 'error:';
				echo $result;
				echo '<br>';
			}
			error_log( $result );
			if ( extension_loaded( 'newrelic' ) ) {
				newrelic_notice_error( $result );
			}
		}

	}

}
