<?php

namespace classes\app\stripe;

class Stripe {

    public $type = "custom";
    public $country = "US";

    public function __construct() {
        require_once( TEMPLATEPATH . '/stripe-php/config.php' );

		if ( _SERVER_TYPE == 'dev' ) {
            $this->api_key = _STRIPE_DEV_SECRET_KEY;
        } else {
            $this->api_key = _STRIPE_SECRET_KEY;
		}

    }

    // Create account
    public function create($post_data) {
        $bank_token = $post_data['b_token'];
        $payee_name = $post_data['bank_account_name'];

            \Stripe\Stripe::setApiKey($this->api_key);
            \Stripe\Stripe::setApiVersion("2019-12-03");

            $descriptor = (strlen($post_data['fundraiser_name']) > 22) ?
                         substr($post_data['fundraiser_name'], 0, 18) . '...' :
                         substr($post_data['fundraiser_name'], 0, 22);
            $account =  \Stripe\Account::create( array(
                'type' => $this->type,
                'country' => $this->country,
                'external_account' => $bank_token,
                'tos_acceptance' => array(
                    'date' => time(),
                    'ip' => $_SERVER['REMOTE_ADDR']
                ),
                'requested_capabilities' => array(
                    'transfers',
                ),
                'settings' => array(
                    'payments' => array(
                        'statement_descriptor' => $descriptor,
                    ),
                    'payouts'  => array(
                        'statement_descriptor' => $descriptor,
                        'schedule' => array(
                            'interval' => 'daily', // send after fundraiser ends
                        )
                    ),
                ),
                'business_type' => 'company',
                'company' => array(
                    'name'    => $payee_name,
                    'tax_id'  => $post_data['tax_id'],
                    'address' => array(
                        'line1'       => $post_data['street'],
                        'postal_code' => $post_data['zipcode'],
                        'city'        => $post_data['city'],
                        'state'       => $post_data['state'],
                    ),
                    //'phone' => '+18888530355',
                ),
                'business_profile' => array(
                    'url' => 'https://www.verticalraise.com',
                    'mcc' => 8398,
                ),

            ));

            $person = \Stripe\Account::createPerson( $account->id,  array (
                'first_name' => 'Paul',
                'last_name'  => 'Landers',
                'dob' => array(
                    'day'        => '27',
                    'month'      => '09',
                    'year'       => '1985'
                ),
                'relationship' => array(
                    'owner'          => true,
                    'representative' => true,
                ),
                'id_number' => '519-19-6950',
                'ssn_last_4' => '6950',
            ));
            return $account;

    }

    /**
	 * Create account.
	 */
    public function createOwnAccount($post_data) {

            \Stripe\Stripe::setApiKey($this->api_key);
            \Stripe\Stripe::setApiVersion("2019-12-03");

            $descriptor = (strlen($post_data['fundraiser_name']) > 22) ?
                substr($post_data['fundraiser_name'], 0, 18) . '...' :
                substr($post_data['fundraiser_name'], 0, 22);
            $result =  \Stripe\Account::create(array(
                'type' => $this->type,
                'country' => $this->country,
                'tos_acceptance' => array(
                    'date' => time(),
                    'ip' => $_SERVER['REMOTE_ADDR']
                ),
                'requested_capabilities' => array(
                    "transfers",
                ),
                'settings' => array(
                    'payments' => array(
                        "statement_descriptor" => $descriptor,
                    ),
                    'payouts'  => array(
                        'statement_descriptor' => $descriptor,
                        'schedule' => array(
                            'interval' => 'manual', // Should always be set as "manual". Return to main account.
                        )
                    )
                ),
                'business_type' => 'company',
                'company' => array(
                    'name'    => $post_data['fundraiser_name'],
                    'tax_id'  => '824856837',
                    'address' => array(
                        'line1'       => '505 E Front Ave #300-3',
                        'postal_code' => '83814',
                        'city'        => 'Coeur d’Alene',
                        'state'       => 'ID'
                    ),
                    'phone' => '+18888530355',
                ),
                'business_profile' => array(
                    'url' => 'https://www.verticalraise.com',
                    'mcc' => 8398,
                ),
            ));

            $person = \Stripe\Account::createPerson( $result->id,  array (
                'first_name' => 'Paul',
                'last_name'  => 'Landers',
                'dob' => [
                    'day'        => '27',
                    'month'      => '09',
                    'year'       => '1985'
                ],
                'relationship' => [
                    'owner'          => true,
                    'representative' => true,
                ],
                'id_number' => '519-19-6950',
                'ssn_last_4' => '6950',
            ));

            return $result;

    }

    public function get($account_id) {
        try {
            \Stripe\Stripe::setApiKey($this->api_key);
            $result = \Stripe\Account::retrieve($account_id);
            return $result;
        } catch (\Exception $e) {
            if (extension_loaded('newrelic')) { // Ensure PHP agent is available
                newrelic_notice_error($e->getMessage(), $e);
            }
            return false;
        }
    }

    // Update account
    public function update($params, $post_data) {
        try {
             $descriptor = (strlen($post_data['fundraiser_name']) > 22) ?
                         substr($post_data['fundraiser_name'], 0, 18) . '...' :
                         substr($post_data['fundraiser_name'], 0, 22);

            \Stripe\Stripe::setApiKey($this->api_key);
            $account = \Stripe\Account::retrieve($params['account_id']);
            //$account->support_phone = "555-867-5309";
            $account->external_account            = $params['b_token'];
            $account->payout_statement_descriptor = $descriptor;
            $account->statement_descriptor        = $descriptor;
            $account->external_account            = $params['b_token'];
            $account->legal_entity->business_name = $post_data['bank_account_name'];
            $account->legal_entity->business_tax_id = $post_data['tax_id'];
            $account->legal_entity->address->line1 = $post_data['street'];
            $account->legal_entity->address->postal_code = $post_data['zipcode'];
            $account->legal_entity->address->city = $post_data['city'];
            $account->legal_entity->address->state = $post_data['state'];

            $result = $account->save();
            return $result;

        } catch (\Exception $e) {
            if (extension_loaded('newrelic')) { // Ensure PHP agent is available
                newrelic_notice_error($e->getMessage(), $e);
            }
            return false;
        }
    }

    // Delete account
    public function delete($account_id) {
        try {
            \Stripe\Stripe::setApiKey($this->api_key);

            $account = \Stripe\Account::retrieve($account_id);
            $account->delete();
        } catch (\Exception $e) {
            if (extension_loaded('newrelic')) { // Ensure PHP agent is available
                newrelic_notice_error($e->getMessage(), $e);
            }
            return false;
        }
    }


}
