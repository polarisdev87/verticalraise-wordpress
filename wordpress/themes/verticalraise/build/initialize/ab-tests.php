<?php
/**
 * Run any page related AB Tests.
 *
 * @package VerticalRaise
 */

use classes\app\ab_test\AB_Test;

add_action(
	'get_header',
	function() {
		// Donation Page A/B Test.
		if ( is_page( 'donation' ) ) {
			$donation_variants = array(
				array(
					'name' => 'v1',
					'url'  => '/donationV1',
				),
				array(
					'name' => 'v3',
					'url'  => '/donationV3',
				)
			);

			$ab_test = new AB_Test();
			$results = $ab_test->run_test( $donation_variants );

			$site_url      = get_site_url();
			$query_string  = '';
			$query_string .= ! empty( $_GET['fundraiser_id'] ) ? "fundraiser_id={$_GET['fundraiser_id']}&" : '';
			$query_string .= ! empty( $_GET['media'] ) ? "media={$_GET['media']}&" : '';
			$query_string .= ! empty( $_GET['uid'] ) ? "uid={$_GET['uid']}&" : '';
			$query_string .= ! empty( $_GET['semail'] ) ? "semail={$_GET['semail']}&" : '';
			$query_string .= ! empty( $_GET['donation_amount'] ) ? "donation_amount={$_GET['donation_amount']}&" : '';

			header( 'Location: ' . $site_url . $results['url'] . '?' . $query_string );
			//header( 'Location: ' . $site_url . $donation_variants[0]['url'] . '?' . $query_string );
			exit();
		}
	}
);
