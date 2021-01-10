<?php
/**
 * Class StripeTest
 *
 * @package Vertical_Raise_Theme_Buildout
 */

/**
 * Stripe test case.
 */
class StripeTest extends WP_UnitTestCase {

	/**
	 * charge test
	 */
	public function test_charge() {

        \Stripe\Stripe::setApiKey('sk_test_siM606LU15EpxpeVMAlaszY7');
        $faker = Faker\Factory::create();
        $payments = new Payments();

        //create card
        $card = \Stripe\Token::create([
            'card' => [
                'number'    => '4242424242424242',
                'exp_month' => 11,
                'exp_year'  => 2020,
                'cvc'       => '314',
            ],
        ]);
        $this->assertTrue( is_object($card) );


        //create source and attach card
        $source = \Stripe\Source::create([
            "type" => "card",
            "currency" => "usd",
            "token" => $card->id
        ]);
        $this->assertTrue( is_object($source) );

        // payment data
        $params = [
            'stripe_connect'   => '0',
            'force_connect'    => '0',
            'full_name'        => $faker->name,
            'email'            => $faker->email,
            'amount'           => $faker->numberBetween(1500, 100000),
            'currency'         => 'usd',
            'fundraiser_title' => 'Fundraiser from phpunit test',
            'token'            => $source->id
        ];

        $success = $payments->test_stripe($params);

        $this->assertTrue( is_array($success) );
        $this->assertTrue( is_string($success['transaction_id']) );

        /*
        //create customer
        $customer = \Stripe\Customer::create(
            [
                'name' => $params['full_name'],
                'description' => $params['full_name'],
                'email' => $params['email'],
            ]
        );
        $this->assertTrue( is_object($customer) );

        // attempt to charge customer using the source
        $charge = \Stripe\Charge::create(
            array(
                'customer'      => $customer->id,
                'source'        => $source->id,
                'receipt_email' => $params['email'],
                'amount'        => $params['amount'],
                'description'   => $params['fundraiser_title'],
                'currency'      => $params['currency'],
            )
        );

		$this->assertTrue( is_object($charge) );*/
	}


    /**
     * charge test dd
     */
    public function test_charge_direct_deposit() {

        \Stripe\Stripe::setApiKey('sk_test_siM606LU15EpxpeVMAlaszY7');
        $faker = Faker\Factory::create();
        $payments = new Payments();

        //create card
        $card = \Stripe\Token::create([
            'card' => [
                'number'    => '4242424242424242',
                'exp_month' => 11,
                'exp_year'  => 2020,
                'cvc'       => '314',
            ],
        ]);
        $this->assertTrue( is_object($card) );


        // payment data
        $params = [
            'stripe_connect'          => '1',
            'force_connect'           => '0',
            'full_name'               => $faker->name,
            'email'                   => $faker->email,
            'amount'                  => $faker->numberBetween(1500, 100000),
            'currency'                => 'usd',
            'fundraiser_title'        => 'Fundraiser from phpunit test',
            'statement'               => 'Fundraiser donation',
            'stripe_connect_account'  => 'acct_1FXGMnDnshrS1boj',
            'token'                   => $card->id,
            'rep_code'                => 0,
            'our_fee'                 => 2
        ];

        $success = $payments->test_stripe($params);

        $this->assertTrue( is_array($success) );
        $this->assertTrue( is_string($success['transaction_id']) );

        /*
        //create customer
        $customer = \Stripe\Customer::create(
            [
                'name' => $params['full_name'],
                'description' => $params['full_name'],
                'email' => $params['email'],
                'card' => $card->id
            ]
        );
        $this->assertTrue( is_object($customer) );

        // attempt to charge customer using the source
        $charge = \Stripe\Charge::create(
            array(
                'customer'             => $customer->id,
                'amount'               => $params['amount'],
                'description'          => $params['fundraiser_title'],
                'statement_descriptor' => $params['statement'],
                'currency'             => $params['currency'],
                "destination"          => [
                    "amount"                => floatval($params['amount']),
                    "account"               => $params['stripe_connect_account']
                ],
            )
        );


        $this->assertTrue( is_object($charge) );*/

    }
}
