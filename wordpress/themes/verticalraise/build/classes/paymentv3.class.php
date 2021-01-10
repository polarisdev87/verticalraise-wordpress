<?php

/**
 * Process Payments
 *
 * The various functions will support payment validation, creating the customer through Stripe, making the charge * through Stripe and inserting the successful transaction record into the database.
 *
 */

use classes\app\donation\Donations_Sum;
use classes\app\donation\Donations_Count;
use classes\app\stripe\Stripe_Form;


class PaymentsV3 {

    public function __construct() {

    }

    /**
     * Validate the incoming $_POST payment details.
     *
     * @param array $params Array containing the necessary params.
     *    $params = [
     *      'fundraiser_id'    => (int) Fundraiser id. Required.
     *      'email'            => (string) Customer email. Required.
     *      'token'            => (string) Stripe elements token. Required.
     *      'amount'           => (float) Donation amount. Required.
     *    ]
     *
     * @return string Returns the
     */
	public function validate_params( $params ) {

		// Required fields.
		$required = [
			'amount'        => 'float',
			'fundraiser_id' => 'int',
		];

		// Check they are not empty
		foreach ( $required as $key => $req ) {
			if ( empty( $params[ $key ] ) ) {
				return "Param {$key} is empty";
			}
		}

		// Check for correct types
		$flag = '';
		foreach ( $required as $key => $req ) {
			switch ( $req ) {
				case 'float':
					if ( floatval( $params[ $key ] ) <= 0 ) {
						$flag = 1;
					}
					break;
				case 'int':
					if ( intval( $params[ $key ] ) <= 0 ) {
						$flag = 1;
					}
					break;
				case 'string':
					if ( is_string( $params[ $key ] ) == false ) {
						$flag = 1;
					}
					break;
			}

			if ( $flag != '' ) {
				return "Param {$key} should be {$req}";
			}
		}

		// Check if fundraiser exists:
		if ( get_post_status( $params['fundraiser_id'] ) == false ) {
			return 'Fundraiser ID does not exist';
		}

		// is participant real?
		//if ( get_userdata( $participant ) == false ) return false;
		// Check for correct length ?
		// Check for user id, media, anonymous ?
		if ( ! empty( $_POST['uid'] ) ) {
			if ( get_userdata( $_POST['uid'] ) == false ) {
				return 'User ID does not exist';
			}
		}

		if ( ! empty( $_POST['media'] ) ) {
			if ( is_string( $_POST['media'] ) == false ) {
				return 'Media should be string';
			}
		}

		if ( isset( $_POST['anonymous'] ) ) {
			if ( is_bool( $_POST['anonymous'] ) ) {
				return 'Anonymous should be boolean';
			}
		}

		return true;
	}

    /**
     * Establish the params that will be used to create the charge using the incoming $_POST data.
     *
     * @param array $post_data Array containing the incoming $_POST data.
     *    $post_data = [
     *      'fundraiser_id'    => (int) Fundraiser id. Required.
     *      'token'            => (string) Stripe elements token. Required.
     *      'amount'           => (float) Donation amount. Required.
     *      'fname'            => (string) Donator's first name. Required.
     *      'lname'            => (string) Donator's last name. Required.
     *    ]
     *
     * @return array $params Returns an array of the params.
     */
    public function set_params($post_data) {
        $params['fundraiser_id'] = $post_data['fundraiser_id'];
        $params['fundraiser_title'] = get_the_title($post_data['fundraiser_id']);
        $params['currency'] = getCurrency($post_data['fundraiser_id']);
        $params['amount'] = $post_data['amount'] * 100; // why multiplied by 100?
        //$params['token'] = $post_data['stripeToken'];
        $uid = ( empty($post_data['uid']) ) ? '' : $post_data['uid'];
        $params['uid'] = $this->get_uid($uid, $post_data['fundraiser_id']);
        $params['media'] = ( empty($post_data['media']) ) ? '' : $post_data['media'];
		$params['anonymous'] = ( isset($post_data['anonymous']) ) ? intval($post_data['anonymous']) : 0;
		//$params['full_name'] = $post_data['full_name'];
        //$params['email'] = $post_data['email'];

        $params['stripe_connect'] = (empty($post_data['stripe_connect'])) ? '' : $post_data['stripe_connect'];
        $params['force_connect'] = (empty($post_data['force_connect'])) ? '' : $post_data['force_connect'];
        $params['stripe_connect_account'] = (empty($post_data['stripe_account_id'])) ? '' : $post_data['stripe_account_id'];
        $params['our_fee'] = (empty($post_data['our_fee'])) ? '' : $post_data['our_fee'];
        $params['rep_code'] = (empty($post_data['rep_code'])) ? '' : $post_data['rep_code'];


        ## TODO: See if the fundraiser stripe connect is set to on or off
        ## TODO: If on, get the stripeconnect account # if it's present
        ## TODO: If on, get the application fee, it should be [(.(100 - (the rate))) * the amount]
        ## TODO: If missing stripeconnect account #, then set stripe connect to off
        ## TODO: If missing application fee, then set stripe connect to off

        return $params;
    }

    /**
     * Charge the donator after they submit their credit card details.
     *
     * @param array $params Array containing the necessary params.
     *    $params = [
     *      'email'            => (string) Customer email. Required.
     *      'token'            => (string) Stripe elements token. Required.
     *      'amount'           => (float) Donation amount. Required.
     *      'fundraiser_title' => (string) Fundraiser's title. Required.
     *      'currency'         => (string) Currency. Required.
     *    ]
     *
     * @return bool Returns true on success, and a false on failure.
     */
    public function charge($params) {
        // Charge in Stripe
        $stripe_charge = $this->stripe($this->sanitize($params));

        // Successful charge
        if (!empty($stripe_charge)) {

            // Insert the record
            $payment_id = $this->insert_payment($stripe_charge, $params);

            // Return the results
            $results['stripe_charge'] = $stripe_charge;
            $results['params'] = $params;
            $results['payment_id'] = $payment_id;

            return $results;
        } else {
            // Unsuccessful charge
            return false;
        }
    }

    /**
     * Create the customer and charge them in Stripe.
     *
     * @param array $params Array containing the necessary params.
     *    $params = [
     *      'email'            => (string) Customer email. Required.
     *      'token'            => (string) Stripe elements token. Required.
     *      'amount'           => (float) Donation amount. Required.
     *      'fundraiser_title' => (string) Fundraiser's title. Required.
     *      'currency'         => (string) Currency. Required.
     *    ]
     *
     * @param bool $testmode
     * @return array|bool Returns amount and transaction id on success, and false on failure.
     */
    private function stripe( $params, $testmode = false) {
        if ( !$testmode ){
            require_once TEMPLATEPATH . '/stripe-php/config.php';
            \Stripe\Stripe::setApiVersion("2019-12-03");
        }
        ### TODO: Grab fundraiser stripe connect account ###

            // If Fundraiser is a Stripe Connect fundraiser
            if ( $params['stripe_connect'] == 1 ) {
                            // Create customer object   (Test decline token: 'tok_chargeDeclined')
                $customer = \Stripe\Customer::create(
                    array(
                        'email' => $params['email'],
                        'source' => $params['token']
                    )
                );
                //calculate application fee
                $repCode = floatval($params['rep_code']);
                $our_fee = (int) $params['our_fee'];

                $fee = 0;
                if ($our_fee == 2) {
                    if ($repCode == 0) {
                        $fee = 0;
                    } else {
                        $fee = (100 - $repCode) / 100 * $params['amount'];
                    }
                }

                $token = \Stripe\Token::create(
                        array(
                            "customer" => $customer->id
                        ), array(
                            "stripe_account" => $params['stripe_connect_account']
                        )
                );

                // Create charge object
                $descriptor = (strlen($params['fundraiser_title']) > 22) ?
                         substr($params['fundraiser_title'], 0, 18) . '...' :
                         substr($params['fundraiser_title'], 0, 22);


                $charge = \Stripe\Charge::create(
                    array(
                        'customer' => $customer->id,
                        'amount' => $params['amount'],
                        'description' => $params['fundraiser_title'],
                        'statement_descriptor' => $descriptor,
                        'currency' => $params['currency'],
                        "transfer_data" => [
                            "amount"  => floatval($params['amount']) - $fee,
                            "destination" => $params['stripe_connect_account']
                         ],
                    )
                );

            } elseif ( $params['force_connect'] == 1 ) {
                // Create customer object   (Test decline token: 'tok_chargeDeclined')
                $customer = \Stripe\Customer::create(
                    array(
                        'email' => $params['email'],
                        'source' => $params['token']
                    )
                );

                $token = \Stripe\Token::create(
                    array(
                        "customer" => $customer->id
                    ), array(
                        "stripe_account" => $params['stripe_connect_account']
                    )
                );

                // Create charge object
                $descriptor = (strlen($params['fundraiser_title']) > 22) ?
                    substr($params['fundraiser_title'], 0, 18) . '...' :
                    substr($params['fundraiser_title'], 0, 22);


                $charge = \Stripe\Charge::create(
                    array(
                        'customer' => $customer->id,
                        'amount' => $params['amount'],
                        'description' => $params['fundraiser_title'],
                        'statement_descriptor' => $descriptor,
                        'currency' => $params['currency'],
                        "transfer_data" => [
                            "amount"  => floatval($params['amount']),
                            "destination" => $params['stripe_connect_account']
                        ],
                    )
                );

            }  else {

            	$source = \Stripe\Source::create(
            		array(
            			'type' => 'card',
            			'token' => $params['token'],
		            )
	            );

                $customer = \Stripe\Customer::create(
                    [
                        'name' => $params['full_name'],
                        'description' => $params['full_name'],
                        'email' => $params['email'],
                    ]
                );
                // Create charge object
                $charge = \Stripe\Charge::create(
                    array(
                        'customer' => $customer->id,
                        'source' => $source->id,
                        'receipt_email' => $params['email'],
                        'amount' => $params['amount'],
                        'description' => $params['fundraiser_title'],
                        'currency' => $params['currency'],
                    )
                );
            }

            if ($charge->outcome->type == 'authorized') {
                // Successful payment
                $success['amount'] = (float) ($charge->amount / 100);
                $success['transaction_id'] = $charge->id;
                return $success;
            } else {
                // Unsuccessful payment
                return false;
            }

    }

    /**
     * To use it on phpunit
     * @param $args
     * @return array|bool
     */
    public function test_stripe($args){
        return $this->stripe($args, true);
    }

	public function get_payment_by_transaction_id( $transaction_id ) {

    	global $wpdb;

    	$query = $wpdb->prepare("SELECT count(id) from `donations` where transaction_id = %s", array($transaction_id));
    	$var = $wpdb->get_var($query);
    	return $var;
    }

    /**
     * Insert the payment record into the database.
     *
     * @param array $stripe_charge Array containing the necessary Stripe params.
     *    $stripe_charge = [
     *      'email'            => (string) Customer email. Required.
     *      'token'            => (string) Stripe elements token. Required.
     *    ]
     * @param array $params Array containing the necessary params.
     *
     */
    public function insert_payment($stripe_charge, $params) {

        try {
            global $wpdb;

            // Begin transaction
            $wpdb->query('BEGIN');

            // Insert query
            $insert = $wpdb->query($wpdb->prepare(
                            "
                    INSERT INTO `donations` (f_id, media, uid, email, name, anonymous, amount, time, transaction_id, donation_type ) VALUES ( %d, %s, %d, %s, %s, %d, %f, %s, %s, %s);
                ", array(
                        $params['fundraiser_id'], // fundraiser id
                        $params['media'], // media type (ie. f, flyer, sms, email)
                        $params['uid'], // participant's user id
                        $params['email'],
                        $params['full_name'], // donator's full name
                        $params['anonymous'], // donator's preference to be anonymous
                        $stripe_charge['amount'], // the amount donated returned by Stripe
                        current_time('mysql'), // time stamp
                        $stripe_charge['transaction_id'], // transaction id returned by Stripe
	                    $stripe_charge['charge_type'], // charge type (cc, apple pay, gpay, etc)
                            )
            ));

            if (empty($insert)) {
                // Error occured, don't save any changes
                $wpdb->query('ROLLBACK'); // Roll back the transaction
                throw new Exception('SQL Error: ' . $wpdb->print_error()); // Log error message
            } else {

                $last_id = $wpdb->insert_id;
//                //add donations_archive table
//                $insert_achive = $wpdb->query($wpdb->prepare(
//                                "
//                    INSERT INTO `donations_archive` (id, f_id, media, uid, email, name, anonymous, amount, time, transaction_id) VALUES ( %d, %d, %s, %d, %s, %s, %d, %f, %s, %s);
//                ", array (
//                            $last_id,
//                            $params['fundraiser_id'], // fundraiser id
//                            $params['media'], // media type (ie. f, flyer, sms, email)
//                            $params['uid'], // participant's user id
//                            $params['email'],
//                            $params['full_name'], // donator's full name
//                            $params['anonymous'], // donator's preference to be anonymous
//                            $stripe_charge['amount'], // the amount donated returned by Stripe
//                            current_time('mysql'), // time stamp
//                            $stripe_charge['transaction_id'] // transaction id returned by Stripe
//                                )
//                ));
//                if (empty($insert_achive)) {
//                    $wpdb->query('ROLLBACK');
//                } else {
//                    // Success
//                    $wpdb->query('COMMIT');
//
////                    $record_id = $wpdb->insert_id;
//                    //$record_id = $wpdb->query("SELECT LAST_INSERT_ID();");
//                    //return true;
//                    return $last_id;
//                }
                $wpdb->query('COMMIT');
                return $last_id;
            }
        } catch (Exception $e) {
            if (extension_loaded('newrelic')) { // Ensure PHP agent is available
                newrelic_notice_error($e->getMessage(), $e);
            }
            return "insert_payment failed: " . $e->getMessage();
        }
    }

    /**
     * Sanitize the parameters so they are ready for input.
     *
     * @param array $params Array containing the necessary params.
     *    $params = [
     *      'email'            => (string) Customer email. Required.
     *      'token'            => (string) Stripe elements token. Required.
     *      'amount'           => (float) Donation amount. Required.
     *      'fundraiser_title' => (string) Fundraiser's title. Required.
     *      'currency'         => (string) Currency. Required.
     *    ]
     *
     * @return array Returns the sanitized parameters.
     */
    private function sanitize($params) {

        $s_params['fundraiser_id'] = sanitize_email($params['fundraiser_id']);
        $s_params['email'] = sanitize_email($params['email']);
        $s_params['full_name'] = sanitize_text_field($params['full_name']);
        $s_params['token'] = sanitize_text_field($params['token']);
        $s_params['amount'] = (float) $params['amount'];
        $s_params['fundraiser_title'] = sanitize_text_field($params['fundraiser_title']);
        $s_params['currency'] = sanitize_text_field($params['currency']);
        $s_params['stripe_connect'] = (int) sanitize_text_field($params['stripe_connect']);
        $s_params['force_connect'] = (int) sanitize_text_field($params['force_connect']);
        $s_params['stripe_connect_account'] = sanitize_text_field($params['stripe_connect_account']);
        $s_params['our_fee'] = sanitize_text_field($params['our_fee']);
        $s_params['rep_code'] = sanitize_text_field($params['rep_code']);

        return $s_params;
    }

    /**
     * Make sure the user id is in fact attached to the fundraiser, if not return 0.
     *
     * @param int $uid The user id
     * @param int $fid The fundraiser id
     *
     * @return int The real user id, or 0 for 'generic'
     */
    private function get_uid($uid, $fid) {
        // If the submitted uid is 0 there is no need to check
        if ( $uid === 0 ) {
            return 0;
        }

        /**
         * Include the Stripe library and config.
         */
        require_once( TEMPLATEPATH . '/classes/participants.class.php' );

        $participants = new Participants();

        $fundraisers = $participants->get_fundraiser_ids_by_userid($uid);
        if (in_array($fid, $fundraisers)) {
            return $uid;
        } else {
            return 0;
        }
    }

    /**
     * Attempts to refund a charge
     *
     * @param int $donation_id donation id in table
     * @throws \Exception Stripe Exception
     *
     * @return bool success status
     */

     public static function refundPayment( $donation_id ) {
         global $wpdb;
         $query = $wpdb->prepare( "SELECT `f_id`, `uid`, `transaction_id`, `amount` FROM `donations` WHERE  id = %d ", array( $donation_id ) );
         $donation = $wpdb->get_row( $query , ARRAY_A );

         if ( isset( $donation['transaction_id'] ) ) {
            require_once( TEMPLATEPATH . '/stripe-php/config.php' );
            load_class( 'participant_records.class.php' );

            $refund = \Stripe\Refund::create([
                'charge' => $donation['transaction_id'],
            ]);
            if ( $refund->status === "succeeded"  ) {
                $modified = $wpdb->update( "donations", array( "refunded" => 1 ), array( "id" => $donation_id) , array("%d"), array("%d"));

                $donations_total = new Donations_Sum();
                $donations_total->increment_total( $donation['f_id'], -intval($donation['amount']) );

                $donations_count = new Donations_Count();
                $donations_count->increment_total( $donation['f_id'], -1 );

                $participant_records = new Participant_Sharing_Totals();

                if ( $donation['uid'] ) {
                    $participant_records->adjust( $donation['f_id'], $donation['uid'], 'supporters', -1 );
                    $participant_records->adjust( $donation['f_id'], $donation['uid'], 'total', -intval($donation['amount']) );
                }

                /**
                 * Cache Update - just flush the cache for specific keys.
                 */
                delete_transient( 'get_donators_' . $donation['f_id'] ); // Supporter List
                wp_cache_delete( 'get_amount_' . $donation['f_id'] ); // Total Raised
                wp_cache_delete( 'get_num_supporters_' . $donation['f_id'] ); // Number of Supporters


                if ( $modified === 1 ) {
                    return true;
                }
            }
         }
         return false;
     }

     public static function transferBackFromAccounts( $fundraiser_id, $account_id, $amount ){

         require_once( TEMPLATEPATH . '/stripe-php/config.php' );
         global $wpdb;

         $transfer = \Stripe\Transfer::create(
             array(
                 "amount"      => $amount,
                 "currency"    => "usd",
                 "destination" => "acct_1CGsXZAEOM60IWnV"
             ),
             array( "stripe_account" => $account_id )
         );

         if ( isset( $transfer->amount ) ) {
	         $modified = $wpdb->update( "fundraiser_details", array( "transferred" => 1 ), array( "f_id" => $fundraiser_id ) , array("%d"), array("%d"));
             return true;
         } else {
             return false;
         }

     }

	public static function payoutToAccount( $fundraiser_id, $account_id, $amount ){

		require_once( TEMPLATEPATH . '/stripe-php/config.php' );
		global $wpdb;

		$payout = \Stripe\Payout::create( array(
			'amount' => $amount,
			'currency' => 'usd',
		), array(
			'stripe_account' => $account_id
		) );

		if ( isset( $payout->amount ) ) {
			$modified = $wpdb->update( "fundraiser_details", array( "transferred" => 1 ), array( "f_id" => $fundraiser_id ) , array("%d"), array("%d"));
			return true;
		} else {
			return false;
		}

	}
}
