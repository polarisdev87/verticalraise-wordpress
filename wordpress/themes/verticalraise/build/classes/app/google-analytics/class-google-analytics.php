<?php

namespace classes\app\google_analytics;

/**
 * Implements and injects Google Analytics package and handles injecting the tracking event and Enhanced Ecommerce events.
 */
class GoogleAnalytics {
	/**
	 * Google UA Code
	 *
	 * @var string Analytics property code
	 */
	private $google_ua_code = _GOOGLE_UA_CODE;

	/**
	 * Google Adwords conversion Code
	 *
	 * @var string Analytics conversion code
	 */
	private $google_awc_code = _GOOGLE_AWC_CODE;

	/**
	 * Display Google Analytics.
	 *
	 * @param string $type The type of `event` to send.
	 *
	 * @return html
	 */
	public function display( $type = 'pageview' ) {
		return "
            <!-- Google Analytics code -->
            <script>
                (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
                ga('create', '{$this->google_ua_code}', 'auto');
                ga('send', '{$type}');
            </script>

            <!-- Global site tag (gtag.js) - Google Ads: {$this->google_awc_code}' -->
            <script async src=\"https://www.googletagmanager.com/gtag/js?id={$this->google_awc_code}\"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){
                    dataLayer.push(arguments);
                }
                gtag('js', new Date());
                gtag('config', '{$this->google_awc_code}');
            </script>
        ";
	}

	/**
	 * Enhanced Ecommerce event code
	 *
	 * @param array $params The available transaction data.
	 *
	 * @return string The Google Analytics code
	 */
	public function ecommerce( $params ) {

		$brand       = 'Vertical Raise';
		$affiliation = 'Vertical Raise';
		$category    = 'Donations';

		$transaction_id = $params['transaction_id'];
		$amount         = $params['amount'];
		$option_id      = $params['option_id'];
		$option_name    = $params['option_name'];

		return "
            <!-- Enhanced Ecommerce -->
            <script>
            ga('require', 'ec');
            ga('ec:addProduct', {                 // Provide product details in an productFieldObject.
                'id': '{$option_id}',             // Product ID (string).
                'name': '{$option_name}',         // Product name (string).
                'category': '{$category}',        // Product category (string).
                'brand': '{$brand}',              // Product brand (string).
                'variant': '',                    // Product variant (string).
                'price': {$amount},               // Product price (number).
                'quantity': 1                     // Product quantity (number).
            });

            ga('ec:setAction', 'purchase', {      // Transaction details are provided in an actionFieldObject.
                'id': '{$transaction_id}',        // (Required) Transaction id (string).
                'affiliation': '{$affiliation}',  // Affiliation (string).
                'revenue': {$amount},             // Revenue (number).
                'tax': 0,                         // Tax (number).
                'shipping': 0,                    // Shipping (number).
			});
			ga('send', 'event', 'Ecommerce', 'Purchase', 'revenue', { nonInteraction: true });
            </script>
        ";
	}

	/**
	 * A/B Test Event Checkout Code
	 *
	 * @param string  $event_category Typically the object that was interacted with (e.g. 'Video'). Required.
	 * @param string  $event_action   The type of interaction (e.g. 'play'). Required.
	 * @param string  $event_label    Useful for categorizing events (e.g. 'Fall Campaign'). Optional.
	 * @param integer $event_value    A numeric value associated with the event (e.g. 42). Optional.
	 *
	 * @return string The Google Analytics event
	 */
	public function custom_event( $event_category = '', $event_action = '', $event_label = '', $event_value = '' ) {
		if ( empty( $event_category ) || empty( $event_action ) ) {
			return '';
		};
		return "
			<script>
				ga('send', 'event', '{$event_category}', '{$event_action}', '{$event_label}', { nonInteraction: true });
			</script>
		";

	}
}
