<?php

use \classes\app\fundraiser\Fundraiser_Ended;
use classes\app\emails\Custom_Mail;

/**
 * Send email to site admin for each fundraiser that ended today.
 */
function process_ended_admin() {

    try {

        // Load class
        load_class( 'cron.class.php' );
        $cron = new Cron( 'fundraisers_ended', 'f_ended_a' );

        // Create file for output
        $full_file_path = $cron->create_output_file();

        $output = array();

        // Create db entry in 'cron_log' for this cron execution
        $record_id = $cron->create_db_entry();

        // If a record exists, do not run
        if ( $record_id == false ) {
            return false;
        }

        // Cycle through each fundraiser.
        $args = array(
            'post_type'      => 'fundraiser',
            'post_status'    => 'publish',
            'posts_per_page' => -1
        );

        $fundraiser_query = new WP_Query( $args );

        if ( $fundraiser_query->have_posts() ) :
            while ( $fundraiser_query->have_posts() ) : $fundraiser_query->the_post();

                // Fundraiser ID
                $fundraiser_id  = get_the_ID();
                // Fundraiser end date
                $fundraiser_end = new Fundraiser_Ended( $fundraiser_id );
                $end_date       = strtotime( get_post_meta( $fundraiser_id, 'end_date', true ), current_time( 'timestamp' ) );
                // Check if the fundraiser ended today.
                if ( $fundraiser_end->match_enddate() ) {
                    echo "{$fundraiser_id} matched";
                    // Run the main loop
                    $output[] = process_ended_admin_fundraiser_loop( $fundraiser_id, $end_date );
                }
            endwhile;
        endif;

        // Log the cron output.
        $cron->log_output( $full_file_path, $output );

        // Record the cron execution.
        $cron->update_record( $record_id );

        return 'success';
    } catch ( Exception $e ) {
        if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
            newrelic_notice_error( $e->getMessage(), $e );
        }

        return false;
    }
}

/**
 * Process each fundraiser.
 * @param  int    $fundraiser_id
 * @param  string $end_date
 * @return mixed 'failure' on failure, or array $output on success
 */
function process_ended_admin_fundraiser_loop( $fundraiser_id, $end_date ) {

    try {

        // Load classes
        load_class( 'payment_records.class.php' );
        load_class( 'participants.class.php' );

        // Objects
        $mail         = new Custom_Mail();
        $payments     = new Payment_Records();
        $participants = new Participants();

        // Function variables
        $n_participants = $participants->get_filtered_participant_ids_by_fid_count( $fundraiser_id );

        $n_supporters = 0;
        $fund_amount  = 0;

        $n_supporters = $payments->get_number_supporters_by_fundraiser_id( $fundraiser_id );
        $fund_amount  = '$' . number_format( $payments->get_total_by_fundraiser_id( $fundraiser_id ) );

        // Set custom html email template params
        $title     = get_the_title( $fundraiser_id );
        $ended     = date( "m/d/Y", $end_date );
        $permalink = get_permalink( $fundraiser_id );

        $to      = _ENDED_CAMPAIGNS_TO_EMAIL;
        $from    = _CRON_FROM_EMAIL;
        $subject = "The {$title} ended on {$ended}";
        $cc      = null;
        $reply   = null;

        $street  = get_post_meta( $fundraiser_id, 'street', true );
        $city    = get_post_meta( $fundraiser_id, 'city', true );
        $state   = get_post_meta( $fundraiser_id, 'state', true );
        $zipcode = get_post_meta( $fundraiser_id, 'zipcode', true );

        unset( $template_args );

        // Set the template arguments to pass to the email template
        $template_args = array(
            'LINK_TO_FUNDRAISER_PAGE' => $permalink,
            'FUNDRAISER_NAME'         => $title,
            'END_DATE'                => $ended,
            'NUMBER_OF_SUPPORTERS'    => $n_supporters,
            'TOTAL_AMOUNT_RAISED'     => $fund_amount,
            'NUMBER_OF_PARTICIPANTS'  => $n_participants,
            'CHECK_PAYABLE'           => get_post_meta( $fundraiser_id, 'check_pay', true ),
            'MAILING_ADDRESS'         => get_post_meta( $fundraiser_id, 'mailing_address', true ),
            'ADDRESS'                 => $street . " " . $city . " " . $state . " " . $zipcode,
            'PRIMARY_CONTACT_NAME'    => get_post_meta( $fundraiser_id, 'con_name', true ),
            'PRIMARY_CONTACT_EMAIL'   => get_post_meta( $fundraiser_id, 'email', true ),
            'FUNDRAISER_COACH_NAME'   => get_post_meta( $fundraiser_id, 'coach_name', true ),
            'FUNDRAISER_COACH_EMAIL'  => get_post_meta( $fundraiser_id, 'coach_email', true ),
            'FUNDRAISER_COACH_CODE'   => get_post_meta( $fundraiser_id, 'coach_code', true )
        );

        // Send the email.
        try {
            $sent = $mail->send_api( $to, $from, $cc, $subject, 'fundraiser_ended', $template_args );
        } catch ( \Exception $e ) {
            throw new Exception( 'Could not send: template_args: ' . json_encode( $template_args ) );
        }

        // Set the output.
        $for_output = $template_args;
        $for_output = array( 'FUNDRAISER_ID' => $fundraiser_id ) + $for_output; // add to front of array

        return $for_output;
    } catch ( Exception $e ) {
        if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
            newrelic_notice_error( $e->getMessage(), $e );
        }

        $for_output = array( 'FUNDRAISER_ID' => $fundraiser_id, 'STATUS' => $e->getMessage() ); // add to front of array

        return $for_output;
    }
}
