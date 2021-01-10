<?php

/**
 * Send email to fundraiser admins letting them know which participants have not shared or raised money.
 */
use \classes\app\fundraiser\Fundraiser_Statistics;
use classes\app\emails\Custom_Mail;

function process_low_participation( $period ) {
    try {
        /**
         * Load classes.
         */
        load_class( 'cron.class.php' );

        /**
         * Instantiate Classes.
         */
        $cron = new Cron( 'process_low_participation' . $period . 'days', 'l_participate_' . $period . 'days' );

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

        /**
         * Cycle through each fundraiser.
         */
        $fundraiser_query = new WP_Query( $args );

        if ( $fundraiser_query->have_posts() ) :

            // Get list of admins on Wefund4u
            $args        = array(
                'role' => 'administrator',
            );
            $admin_users = get_users( $args );
            $admins      = array();
            foreach ( $admin_users as $admin_user ) {
                $admins[] = $admin_user->ID;
            }

            while ( $fundraiser_query->have_posts() ) : $fundraiser_query->the_post();

                // Fundraiser ID
                $fundraiser_id = get_the_ID();

                // Fundraiser end date
                $end_date = strtotime( get_post_meta( $fundraiser_id, 'end_date', true ) );

                /**
                 * Check if the fundraiser ends in 17 days.
                 */
                if ( $end_date == $target_date ) {

                    /**
                     * Process the main loop action.
                     */
                    $output = process_low_participation_loop( $fundraiser_id, $end_date, $admins );
                }

            endwhile;
        endif;

        /**
         * Log the cron output.
         */
        $cron->log_output( $full_file_path, $output );

        /**
         * Record the cron execution.
         */
        $cron->update_record( $record_id );

        return 'success';
    } catch ( Exception $e ) {
        if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
            newrelic_notice_error( $e->getMessage(), $e );
        }

        echo $e->getMessage();

        return 'failure';
    }
}

/**
 * The main loop.
 * @param int $fundraiser_id
 * @param string $end_date
 * @param array $admins List of admin id's
 */
function process_low_participation_loop( $fundraiser_id, $end_date, $admins ) {

    /**
     * Load Classes.
     */
    load_class( 'participant_records2.class.php' );

    /**
     * Instantiate Classes.
     */
    $mail    = new Custom_Mail();
    $sharing = new Participant_Records2();

    try {

        // Get all of the sharing records for this fundraiser
        $low_participants = $sharing->get_low_sharing_by_fid( $fundraiser_id );

        // Show each one and their results to the coach
        if ( !empty( $low_participants ) && count( $low_participants ) > 0 ) {

            // Get team stats
            $fundraiser_statistics = new Fundraiser_Statistics( $fundraiser_id );
            $participation_score   = $fundraiser_statistics->participation_score_formatted();
            $email_quality_score   = $fundraiser_statistics->email_quality_score_formatted();
            $participants_score    = $fundraiser_statistics->participant_score_formatted();

            // Get the admin ids
            $admins[] = get_post_field( 'post_author', $fundraiser_id );

            $participants = array();

            // Build the table as a variable to pass to the template
            $table = "
            <table border='1' cellspacing='0' cellpadding='3'>
                <tr>
                    <td>Participant Name</td>
                    <td>Parent Shares</td>
                    <td>Email Shares</td>
                    <td>Facebook Shares</td>
                    <td>SMS Donations</td>                    
                    <td># of Supporters</td>
                    <td>Total Raised</td>
                </tr>
            ";

            foreach ( $low_participants as $p ) {

                // Skip if an admin
                if ( in_array( $p['participant_id'], $admins ) ) {
                    continue;
                }

                // Skip if a secondary admin
                $secondary_admins = json_decode( get_user_meta( $p['participant_id'], 'campaign_sadmin', true ) );
                if ( !empty( $secondary_admins ) ) {
                    if ( in_array( $fundraiser_id, $secondary_admins ) ) {
                        continue;
                    }
                }

                $p['sms']   = number_format( $p['sms'] );
                $p['total'] = number_format( $p['total'] );

                $table .= "
                <tr>
                    <td align='left'>{$p['participant_name']}</td>
                    <td align='right'>{$p['parents']}</td>
                    <td align='right'>{$p['email']}</td>
                    <td align='right'>{$p['facebook']}</td>
                    <td align='left'>\${$p['sms']}</td>                    
                    <td align='right'>{$p['supporters']}</td>
                    <td align='left'>\${$p['total']}</td>
                </tr>
                ";

                $participants[] = $p;
            }

            $table .= "</table>";

            // Get some values
            $admin_email = _CRON_FROM_EMAIL;
            $title       = get_the_title( $fundraiser_id );
            $ended       = date( "m/d/Y", $end_date );
            $permalink   = get_permalink( $fundraiser_id );

            // Get contacts
            $contact_name  = get_post_meta( $fundraiser_id, 'con_name', true );
            $contact_email = get_post_meta( $fundraiser_id, 'email', true );
            $coach_name    = get_post_meta( $fundraiser_id, 'coach_name', true );
            $coach_email   = get_post_meta( $fundraiser_id, 'coach_email', true );

            // Set custom html email template params
            $to      = $contact_email;
            $from    = $admin_email;
            $subject = "{$title} Update";
            $cc      = $coach_email;
            $reply   = null;

            // Set the template arguments to pass to the email template
            $template_args = array(
                'PARTICIPATION_SCORE'     => $participation_score,
                'EMAIL_QUALITY_SCORE'     => $email_quality_score,
                'PARTICIPANTS_SCORE'      => $participants_score,
                'LINK_TO_FUNDRAISER_PAGE' => $permalink,
                'FUNDRAISER_NAME'         => $title,
                'END_DATE'                => $ended,
                'TABLE'                   => $table,
                'PRIMARY_CONTACT_NAME'    => $contact_name,
                'SIGNATURE_EMAIL'         => _SIGNATURE_EMAIL,
                'SIGNATURE_PHONE_NUMBER'  => _SIGNATURE_OFFICE_PHONE_NUMBER,
                'LOGO_URL'                => get_template_directory_uri() . '/assets/images/email-logo.png'
            );

            /**
             * Send the email.
             */
            $sent = $mail->send_api( $to, $from, $cc, $subject, 'low_participation', $template_args );


            /**
             * Set the output.
             */
            $template_args['TABLE']        = '';
            $template_args['PARTICIPANTS'] = $participants;
            $for_output                    = $template_args;
            $for_output                    = array( 'FUNDRAISER_ID' => $fundraiser_id ) + $for_output; // add to front of array
            $output[]                      = $for_output;
        } else {
            $output[] = array( 'FUNDRAISER_ID' => $fundraiser_id, 'OUTPUT' => 'no records' );
        }

        return $output;
    } catch ( Exception $e ) {
        if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
            newrelic_notice_error( $e->getMessage(), $e );
        }

        return 'failure';
    }
}
