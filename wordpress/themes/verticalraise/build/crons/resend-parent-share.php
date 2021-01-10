<?php

/**
 * Send email to the participants letting them know they have low participation.
 * @return mixed 'failure' or 'success'
 */
use classes\app\emails\Custom_Mail;

function resend_parent_share($period) {

    try {

        load_class( 'cron.class.php' );

        $cron = new Cron( 'resend_parent_share' . $period . 'days', 'l_participant_' . $period . 'days' );

        // Create file for output
        $full_file_path = $cron->create_output_file();

        $output = array();

        // Create db entry in 'cron_log' for this cron execution
        $record_id = $cron->create_db_entry();

        // If a record exists, do not run
        if ( $record_id == false ) {
            return false;
        }

        // Today's Date
        $target_ymd  = date( "Ymd", strtotime( "today + " . $period . " days", current_time( 'timestamp', 0 ) ) );
        $target_date = strtotime( $target_ymd );
        $args        = array(
            'post_type'              => 'fundraiser',
            'post_status'            => 'publish',
            'posts_per_page'         => -1,
            'no_found_rows'          => true,
            'update_post_term_cache' => false,
            'fields'                 => 'ids',
            'meta_query'             => array(
                array(
                    'key'     => 'end_date',
                    'value'   => $target_ymd,
                    'compare' => '=',
                ),
            ),
        );

        load_class( 'admins.class.php' );
        $Admins = new Admins();

        // Cycle through each fundraiser.
        $fundraiser_query = new WP_Query( $args );

        if ( $fundraiser_query->have_posts() ) :

            while ( $fundraiser_query->have_posts() ) : $fundraiser_query->the_post();

                // Fundraiser ID
                $fundraiser_id = get_the_ID();

                // Fundraiser end date
                $end_date = strtotime( get_post_meta( $fundraiser_id, 'end_date', true ) );


                // Get the admins
                $admins = $Admins->get_all_admins( $fundraiser_id );

                // Check if the fundraiser ends in 17 days.
                if ( $end_date == $target_date ) {

                    // Process the main loop action.
                    $output = process_resend_parent_share_loop( $fundraiser_id, $end_date, $admins , $period);
                }


            endwhile;
        endif;

        // Log the cron output.
        $cron->log_output( $full_file_path, $output );

        // Record the cron execution.
        $cron->update_record( $record_id );

        return 'success';
    } catch ( \Exception $e ) {
        if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
            newrelic_notice_error( $e->getMessage(), $e );
        }

        return 'failure';
    }
}

/**
 * Process each fundraiser.
 * @param  int    $fundraiser_id
 * @param  string $end_date
 * @param  array  $admins List of admin ids
 * @return mixed  Returns 'failure' on failure, or array $output on success
 */
function process_resend_parent_share_loop( $fundraiser_id, $end_date, $admins , $period) {

    try {

        load_class( 'participant_records2.class.php' );

        $sharing = new Participant_Records2();

        // Get all of the sharing records for this fundraiser
        $all_participants = $sharing->get_all_participants_by_fid( $fundraiser_id );

        // Show each one and their results to the coach
        if ( !empty( $all_participants ) && count( $all_participants ) > 0 ) {

            ## TODO: Get actual admins ###

            $participants = array();

            // Get some values
            $admin_email = _CRON_FROM_EMAIL;
            $title       = get_the_title( $fundraiser_id );
            $ended       = date( "m/d/Y", $end_date );
            $permalink   = get_permalink( $fundraiser_id );
            $team_name   = get_field("team_name", $fundraiser_id);
            // Set email parameters
            $from    = $admin_email;
            $subject = "Resend Parent Share";
            $cc      = null;

            // Set mail args to pass
            $mail_args['from']      = $from;
            $mail_args['from_name'] = $team_name;
            $mail_args['subject']   = $subject;
            $mail_args['cc']        = $cc;

            // Set the template arguments to pass to the email template
            $template_args = array(
                'LINK_TO_FUNDRAISER_PAGE' => $permalink,
                'FUNDRAISER_NAME'         => $title,
                'END_DATE'                => $ended,
                'SIGNATURE_EMAIL'         => _SIGNATURE_EMAIL,
                'SIGNATURE_PHONE_NUMBER'  => _SIGNATURE_OFFICE_PHONE_NUMBER,
                'LOGO_URL'                => get_template_directory_uri() . '/assets/images/email-logo.png',
                'TEAM_NAME'               => $team_name,
                'FUNDRAISER_ID'           => $fundraiser_id
            );

            foreach ( $all_participants as $p ) {

                // Skip if an admin, post author, sadmin
                if ( in_array( $p['participant_id'], $admins ) ) {
                    continue;
                }

                $response = process_resend_parent_share_player_loop( $p, $template_args, $mail_args );
                if ( $response != 'failed' ) {
                    $participants[] = $response;
                }
            }

            // Set the output
            $template_args['TABLE']        = '';
            $template_args['PARTICIPANTS'] = $participants;
            //$template_args['TEMPLATE']     = 'resend_parent_share';
            $for_output                    = $template_args;
            $for_output                    = array( 'FUNDRAISER_ID' => $fundraiser_id ) + $for_output; // add to front of array
            $output[]                      = $for_output;
        } else {
            $output[] = array( 'FUNDRAISER_ID' => $fundraiser_id, 'OUTPUT' => 'none' );
        }

        return $output;
    } catch ( Exception $e ) {
        if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
            newrelic_notice_error( $e->getMessage(), $e );
        }

        echo $e->getMessage();

        return 'failure';
    }
}

/**
 * Process each participant in the fundraiser.
 * @param  array $p
 * @param  array $template_args
 * @param  array $mail_args
 * @return mixed Returns 'failure' on failure, or array $p on success
 */
function process_resend_parent_share_player_loop( $p, $template_args, $mail_args ) {

    try {

        $mail = new Custom_Mail();

        $template_args['PARTICIPANT_NAME'] = $p['participant_name'];

        $from      = $mail_args['from'];
        $from_name = $mail_args['from_name'];
        $cc        = $mail_args['cc'];
        $subject   = $mail_args['subject'];

        // Get user data
        $user_data = get_userdata( $p['participant_id'] );

        // Set who we are sending the email to
        $to = $user_data->user_email;

        $uid = $p['participant_id'];
        $fid = $template_args['FUNDRAISER_ID'];

        $template_args['PARENT_SHARE_LINK'] = get_site_url() . "/invite-parent-start/?fundraiser_id=$fid&uid=$uid&parent=1";

        // Make sure there is a real email before we send
        if ( !empty( $to ) && is_email( $to ) == true ) {

            /**
             * Send the email.
             */
            $sent = $mail->send_api( $to, $from, $cc, $subject, 'resend_parent_share', $template_args, $from_name );

        }

        return $p;
    } catch ( \Exception $e ) {
        if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
            newrelic_notice_error( $e->getMessage(), $e );
        }

        echo $e->getMessage();

        return 'failed';
    }
}
