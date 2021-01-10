<?php

namespace classes\app\fundraiser;

use classes\app\stripe\Stripe_Form;
load_class( "payment.class.php" );

/**
 *
 */
function transfer_connect_funds() {
	global $wpdb;
	
    require_once( TEMPLATEPATH . '/stripe-php/config.php' );

    $query = "SELECT p.ID FROM fundraiser_details f
                  LEFT JOIN wp_posts p ON f.f_id = p.ID
                  LEFT JOIN wp_postmeta pm ON f.f_id = pm.post_id            
                  WHERE f.transferred = 0 AND p.post_status = 'publish' AND  pm.meta_key = 'stripe_connect' AND pm.meta_value = 0
                  HAVING p.ID IN 
                  ( 
                    SELECT m.post_id FROM wp_postmeta m 
                    LEFT JOIN donations_sum ds ON m.post_id = ds.f_id
                    WHERE m.meta_key = 'end_date' AND ADDDATE( CAST(m.meta_value as DATE), 3) < NOW() 
                    AND ds.amount > 0
                  )
              ";

	$fundraisers = $wpdb->get_results( $query );

	foreach ( $fundraisers as $fundraiser ) {

		$stripe_connect = new Stripe_Form();
		$get_account    = $stripe_connect->get_account_id( $fundraiser->ID );
		$account_id     = $get_account->stripe_account_id;
		if ( ! $account_id ) {
			continue;
		}

		$balance = \Stripe\Balance::retrieve( array( 'stripe_account' => $account_id ) );
		if ( ! $balance ) {
			continue;
		}
		$pending_amount = $balance->jsonSerialize()['pending'][0]['amount'];
		$pending_amount = $pending_amount / 100;
		$pending_amount = floatval( $pending_amount );

		if ( $pending_amount !== 0 ) {
			continue;
		}

		$available_amount  = $original_available_amount = $balance->jsonSerialize()["available"][0]['amount'] ;
		$available_amount  = $available_amount / 100;
		$available_amount  = floatval($available_amount);

		if ( $available_amount > 0 ) {
			$success = \Payments::transferBackFromAccounts( $fundraiser->ID , $account_id, $original_available_amount );
		}
	}

}
