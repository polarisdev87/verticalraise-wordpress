<?php

namespace classes\app\fundraiser;


use classes\app\stripe\Stripe_Form;
use classes\app\emails\Custom_Mail;
use classes\models\tables\Reports_Fundraisers_Reference;
use classes\models\tables\Subgroups;
use Stripe_Error;

class Create_Fundraiser
{

    private $user_ID;
    private $post_data;

    public function __construct( $user_ID ) {

        $this->user_ID   = $user_ID;
        $this->post_data = $_POST;

        load_class( 'join_codes.class.php' );
        $this->join_codes  = new \Join_Codes();
        $this->custom_mail = new Custom_Mail;
    }

    /**
     * Create Fundraiser
     */
    public function create() {

        try {
            $stripe_connect         = new Stripe_Form();
            if ( $this->post_data['payment_option'] == '1' ) {
                $account_id = $stripe_connect->create_connect_account( $this->post_data );
            } else {
                $account_id = $stripe_connect->create_own_account( $this->post_data );
            }

        } catch ( \Exception $e ) {
	        if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
		        newrelic_notice_error( $e->getMessage(), $e );
	        }
	        $result['status'] = false;
	        $result['message'] = "Failure creating the Fundraiser. Try again later";

	        wp_send_json($result, 400);
        }

        $fundraiser_name = sanitize_text_field( $this->post_data['fundraiser_name'] );

        $post           = array(
            'post_title'  => $fundraiser_name,
            'post_type'   => 'fundraiser',
            'post_status' => 'pending',
            'post_author' => $this->user_ID
        );

        $new_fundraiser = wp_insert_post( $post );

        add_post_meta( $new_fundraiser, 'con_name', sanitize_text_field( $this->post_data['con_name'] ) );
        add_post_meta( $new_fundraiser, 'phone', sanitize_text_field( $this->post_data['phone'] ) );
        add_post_meta( $new_fundraiser, 'email', sanitize_text_field( $this->post_data['email'] ) );
        add_post_meta( $new_fundraiser, 'org_type', sanitize_text_field( $this->post_data['org_type'] ) );

	    if ( ! empty( $this->post_data['participants_subgroups'] ) ) {
		    $subgroups       = $this->post_data['participants_subgroups'];
		    $subgroups_table = new Subgroups();

		    foreach ( $subgroups as $subgroup ) {
			    if ( ! empty( trim( $subgroup ) ) ) {
				    $subgroups_table->insert( $subgroup, $new_fundraiser );
			    }
		    }
	    }
//        add_post_meta($new_fundraiser, 'payment_option', sanitize_text_field($this->post_data['payment_option']));
//        payment option - direct diposit

        if ( $this->post_data['payment_option'] == '1' ) {
            add_post_meta( $new_fundraiser, 'stripe_connect', 1 );
        } else {
            add_post_meta( $new_fundraiser, 'stripe_connect', 0 );
            add_post_meta( $new_fundraiser, 'force_connect', 1 );
        }
        if ( isset( $this->post_data['our_fee'] ) )
            add_post_meta( $new_fundraiser, 'our_fee', sanitize_text_field( $this->post_data['our_fee'] ) );
		if ( isset( $this->post_data['tax_id'] ) || isset( $this->post_data['tax_id2'] ) ) {
			$tax_id = '';
			if ( ! empty( $this->post_data['tax_id'] ) ) {
				$tax_id = $this->post_data['tax_id'];
			}
			if ( ! empty( $this->post_data['tax_id2'] ) ) {
				$tax_id = $this->post_data['tax_id2'];
			}
			add_post_meta( $new_fundraiser, 'tax_id', sanitize_text_field( $tax_id ) );
		}
//        if (isset($this->post_data['bank_account_name']))
//            add_post_meta($new_fundraiser, 'bank_account_name', sanitize_text_field($this->post_data['bank_account_name']));
//        if (isset($this->post_data['routing']))
//            add_post_meta($new_fundraiser, 'routing', sanitize_text_field($this->post_data['routing']));
//        if (isset($this->post_data['direct_account']))
//            add_post_meta($new_fundraiser, 'direct_account', sanitize_text_field($this->post_data['direct_account']));
        //payment option - check by email
        if ( isset( $this->post_data['check_pay'] ) ) {
            add_post_meta( $new_fundraiser, 'check_pay', sanitize_text_field( $this->post_data['check_pay'] ) );
        } else {
            if ( isset( $this->post_data['bank_account_name'] ) && !empty( $this->post_data['bank_account_name'] ) ) {
                add_post_meta( $new_fundraiser, 'check_pay', sanitize_text_field( trim( $this->post_data['bank_account_name'] ) ) );
            }
        }
        if ( isset( $this->post_data['mailing_address'] ) ) {
            add_post_meta( $new_fundraiser, 'mailing_address', sanitize_text_field( $this->post_data['mailing_address'] ) );
        } else {
            if ( isset( $this->post_data['con_name'] ) && !empty( $this->post_data['con_name'] ) ) {
                add_post_meta( $new_fundraiser, 'mailing_address', sanitize_text_field( trim( $this->post_data['con_name'] ) ) );
            }
        }
        if ( isset( $this->post_data['street'] ) )
            add_post_meta( $new_fundraiser, 'street', sanitize_text_field( $this->post_data['street'] ) );
        if ( isset( $this->post_data['city'] ) )
            add_post_meta( $new_fundraiser, 'city', sanitize_text_field( $this->post_data['city'] ) );
        if ( isset( $this->post_data['state'] ) )
            add_post_meta( $new_fundraiser, 'state', sanitize_text_field( $this->post_data['state'] ) );
        if ( isset( $this->post_data['zipcode'] ) )
            add_post_meta( $new_fundraiser, 'zipcode', sanitize_text_field( $this->post_data['zipcode'] ) );
        //

        if ( isset( $this->post_data['coach_name'] ) )
            add_post_meta( $new_fundraiser, 'coach_name', sanitize_text_field( $this->post_data['coach_name'] ) );
        if ( isset( $this->post_data['coach_email'] ) )
            add_post_meta( $new_fundraiser, 'coach_email', sanitize_text_field( $this->post_data['coach_email'] ) );
        if ( isset( $this->post_data['coach_code'] ) )
            add_post_meta( $new_fundraiser, 'coach_code', sanitize_text_field( $this->post_data['coach_code'] ) );
        add_post_meta( $new_fundraiser, 'hear_about_us', sanitize_text_field( $this->post_data['hear_about_us'] ) );

        if ( isset( $this->post_data['sport_scope_integration_value'] ) ) {
			add_post_meta( $new_fundraiser, 'sport_scope_integrated', $this->post_data['sport_scope_integration_value'], true);
	    }

        add_post_meta( $new_fundraiser, 'team_name', sanitize_text_field( $this->post_data['team_name'] ) );
        $format_in  = 'm/d/Y';
        $format_out = 'Ymd';
        $start_date = \DateTime::createFromFormat( $format_in, $this->post_data['start_date'] );
        $start_date = $start_date->format( $format_out );
        $end_date   = \DateTime::createFromFormat( $format_in, $this->post_data['end_date'] );
        $end_date   = $end_date->format( $format_out );
        add_post_meta( $new_fundraiser, 'start_date', $start_date );
        add_post_meta( $new_fundraiser, 'end_date', $end_date );

	    if ( isset( $this->post_data['secondary_end_date'] ) ) {
		    $secondary_end_date        = \DateTime::createFromFormat( $format_in, $this->post_data['secondary_end_date'] );
		    $secondary_end_date_string = $secondary_end_date->format( $format_out );
		    add_post_meta( $new_fundraiser, 'secondary_end_date', $secondary_end_date_string, true );
	    }

        add_post_meta( $new_fundraiser, 'est_team_size', $this->post_data['est_team_size'] );
        add_post_meta( $new_fundraiser, 'fundraising_goal', str_replace( array( "$", "," ), '', $this->post_data['fundraising_goal'] ) );
        add_post_meta( $new_fundraiser, 'campaign_msg', sanitize_text_field( $this->post_data['campaign_msg'] ) );
        add_post_meta( $new_fundraiser, 'showPc_table', sanitize_text_field( $this->post_data['showPc_table'] ) );
        add_post_meta( $new_fundraiser, 'fund_amount', 0 );
//        add_post_meta($new_fundraiser, 'currency_selection', $this->post_data['currency_selection']);
        add_post_meta( $new_fundraiser, 'currency_selection', 'USD' );

        $potential_donors = array();
        add_post_meta( $new_fundraiser, 'potential_donors', json_encode( $potential_donors ) );
        add_post_meta( $new_fundraiser, 'show_progressbar', 1 );
        add_post_meta( $new_fundraiser, 'show_doller_amount', 1 );
        if ( isset( $this->post_data['show_progressbar'] ) ) {
            //add_post_meta($new_fundraiser, 'show_progressbar', 1);
        } else {
            //add_post_meta($new_fundraiser, 'show_progressbar', 0);
        }
        if ( isset( $this->post_data['show_doller_amount'] ) ) {
            //add_post_meta($new_fundraiser, 'show_doller_amount', 1);
        } else {
            //add_post_meta($new_fundraiser, 'show_doller_amount', 0);
        }
        if ( isset( $this->post_data['enable_custom_email'] ) ) {
            //add_post_meta($new_fundraiser, 'enable_custom_email', 1);
            //add_post_meta($new_fundraiser, 'email_invitation_template', $this->post_data['email_invitation_template']);
        } else {
            add_post_meta( $new_fundraiser, 'enable_custom_email', 0 );
        }

        // store stripe_connect_ids table if stripe connect status is TRUE
        if ( isset( $account_id ) ) {
            $stripe_connect->insert_account_id( $new_fundraiser, $account_id );
        }

        // Generate Join Codes
        $join_code = $this->join_codes->generate_code();
        update_post_meta( $new_fundraiser, 'join_code', $join_code );

        $join_code_sadmin = $this->join_codes->generate_code();
        update_post_meta( $new_fundraiser, 'join_code_sadmin', $join_code_sadmin );



        // Add to report fundraiser reference
        $reference = new Reports_Fundraisers_Reference();
        $reference->insert($new_fundraiser, $fundraiser_name, $start_date, $end_date);


        // Send Create fundraiser Email
        try {
            $this->send_email( $new_fundraiser );
        } catch ( \Exception $e ) {
            if ( extension_loaded( 'newrelic' ) ) {
                newrelic_notice_error( $e->getMessage(), $e );
            }
        }

        return $new_fundraiser;
    }

    /**
     * @param $new_fundraiser
     * @throws \Exception failed email delivery
     */
    private function send_email( $new_fundraiser ) {
        /**
         *  Mail to Admin
         */
        $admin_name = 'Vertical Raise';
        $to         = _ADMIN_TO_EMAIL;
        $from       = _TRANSACTIONAL_FROM_EMAIL;
        $subject    = "New Fundraiser Pending Approval: " . $this->post_data['fundraiser_name'] . ' | ' . _SIGNATURE_EMAIL;
        $cc         = null;
        $reply      = null;

        // Set the template arguments to pass to the email template
        $template_args = array(
            'FUNDRAISER_NAME' => $this->post_data['fundraiser_name'],
            'ADMIN_NAME'      => $admin_name,
            'URL'             => get_bloginfo( 'url' ) . '/wp-admin/post.php?post=' . $new_fundraiser . '&action=edit',
        );

        $sent = $this->custom_mail->send_api( $to, $from, $cc, $subject, 'create_fundraiser_admin', $template_args );

    }

}
