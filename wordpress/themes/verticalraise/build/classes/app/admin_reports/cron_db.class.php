<?php

namespace classes\app\admin_reports;

use \classes\models\tables\Potential_Donors_Email;
use \classes\app\fundraiser\Fundraiser_Ended;
use \classes\app\fundraiser\Fundraiser_Statistics;
use \classes\models\tables\Donations;

class Cron_Fundraisers_DB
{

    public function __construct() {
        $this->potential_donors_email = new Potential_Donors_Email();
        $this->donators               = new Donations();
    }

    public function get_fundraiser_query() {
        $args = array(
            'post_type'      => 'fundraiser',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'author__not_in' => [ '' ]
        );

        $fundraiser_query = new \WP_Query( $args );
        return $fundraiser_query;
    }

    public function get_cron_data( $post_data ) {
        $cron_type = $post_data['cron_type'];
        $cron_date = $post_data['cron_date'];

        switch ( $cron_type ) {
            case '2_days' :
                $result = $this->process_expiry( 2, $cron_date );
                break;
            case '7_days' :
                $result = $this->process_expiry( 7, $cron_date );
                break;
            case '14_days' :
                $result = $this->process_expiry( 14, $cron_date );
                break;
            case 'f_ended_a' :
                $result = $this->process_ended( $cron_date );
                break;
            case 'f_ended_c' :
                $result = $this->process_ended( $cron_date );
                break;
            case 'l_participate_10days' :
                $result = $this->process_low_participation( 10, $cron_date );
                break;
            case 'l_participate_17days' :
                $result = $this->process_low_participation( 17, $cron_date );
                break;
            case 'l_participate_19days' :
                $result = $this->process_low_participation( 19, $cron_date );
                break;
            case 'l_participant_10days' :
                $result = $this->process_low_participants( 10, $cron_date );
                break;
            case 'l_participant_17days' :
                $result = $this->process_low_participants( 17, $cron_date );
                break;
            case 'l_participant_19days' :
                $result = $this->process_low_participants( 19, $cron_date );
                break;
        }

        if ( isset( $result ) && !empty( $result ) ) {
            return $result;
        } else {
            return 'NO CRON';
        }
    }

    private function process_expiry( $period, $cron_date ) {

        ini_set( 'memory_limit', '-1' );
        try {

            $output = array();

            if ( isset( $period ) ) {
                $fundraiser_query = $this->get_fundraiser_query();

                $f_count = 0;
                if ( $fundraiser_query->have_posts() ) :

                    while ( $fundraiser_query->have_posts() ) :
                        $fundraiser_query->the_post();

                        $current_time       = current_time( 'timestamp', 0 );
                        $check_time         = strtotime( str_replace( "-", "/", $cron_date ), $current_time );
                        $start_date         = strtotime( get_post_meta( get_the_ID(), 'start_date', true ), $current_time );
                        $end_date           = strtotime( get_post_meta( get_the_ID(), 'end_date', true ), $current_time );
                        $current_date_start = '';
                        $current_date_end   = '';

                        if ( $period == 14 )
                            $current_date_end = strtotime( date( "Ymd", strtotime( "+2 week", $check_time ) ), $current_time );
                        if ( $period == 7 )
                            $current_date_end = strtotime( date( "Ymd", strtotime( "+1 week", $check_time ) ), $current_time );
                        if ( $period == 2 )
                            $current_date_end = strtotime( date( "Ymd", strtotime( "+2 days", $check_time ) ), $current_time );

                        if ( ($end_date == $current_date_end) || ($start_date == $current_date_start) ) {
                            $potential_donors = $this->potential_donors_email->get_row_by_fid( get_the_ID() );

                            $fundraiser_id = get_the_ID();

                            $support_array = false;

                            // Get people who already donated

                            $donations      = new Donations();
                            $donator_emails = $donations->get_donator_emails_by_fundraiser_id( $fundraiser_id );

                            if ( !empty( $potential_donors ) ) {
                                $f_count++;
                                $fundraiser_id = get_the_ID();
                                foreach ( $potential_donors as $pd ) {
                                    if ( in_array( $pd['p_donator'], $donator_emails ) ) {
                                        continue;
                                    }
                                    $output['fundraisers'][$fundraiser_id][] = array( $pd['p_donator'] );
                                }
                            }

                            if ( $period == 2 ) {
                                if ( _SERVER_TYPE != 'dev' ) {
                                    $potential_donors_sms = json_decode( get_post_meta( $fundraiser_id, 'potential_donors_sms_array', true ) );

                                    if ( !empty( $potential_donors_sms ) ) {
                                        foreach ( $potential_donors_sms as $pds ) {
                                            $phone                           = trim( $pds[1] );
                                            $output['sms'][$fundraiser_id][] = array( $pds[0], $phone );
                                        }
                                    }
                                }
                            }
                        }
                    endwhile;
                endif;
            }
            $output['fundraiser_count'] = $f_count;
        } catch ( Exception $e ) {

            if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
                newrelic_notice_error( $e->getMessage(), $e );
            }

            return 'failure';
        }

//        $result = json_encode($output);
        $array                     = array();
        $array['fundraiser_count'] = $output['fundraiser_count'];
        $array['email_count']      = 0;
        $array['fundraiser_array'] = array();
        $array['dbData']           = $output;
        if ( $output['fundraiser_count'] != 0 ) {
            foreach ( (array) $output['fundraisers'] as $key => $fundraiser ) {
                $array['email_count']        += count( $fundraiser );
                $array['fundraiser_array'][] = array(
                    'FID'    => $key,
                    'FNAME'  => get_the_title( $key ),
                    'EMAILS' => count( $fundraiser )
                );
            }
        }

        return $array;
    }

    private function process_ended( $cron_date ) {
        ini_set( 'memory_limit', '-1' );
        $fundraiser_query = $this->get_fundraiser_query();
        $output           = array();
        if ( $fundraiser_query->have_posts() ) :
            while ( $fundraiser_query->have_posts() ) : $fundraiser_query->the_post();

                // Fundraiser ID
                $fundraiser_id  = get_the_ID();
                // Fundraiser end date
                $fundraiser_end = new Fundraiser_Ended( $fundraiser_id );
                $end_date       = strtotime( get_post_meta( $fundraiser_id, 'end_date', true ), current_time( 'timestamp' ) );
                // Check if the fundraiser ended cron date.
                if ( $fundraiser_end->match_enddate( str_replace( "-", "/", $cron_date ) ) ) {

                    try {

                        // Load classes
                        load_class( 'payment_records.class.php' );
                        load_class( 'participants.class.php' );

                        $payments     = new \Payment_Records();
                        $participants = new \Participants();

                        // Function variables
                        $n_participants = $participants->get_filtered_participant_ids_by_fid_count( $fundraiser_id );
                        $n_supporters   = 0;
                        $fund_amount    = 0;

                        $n_supporters = $payments->get_number_supporters_by_fundraiser_id( $fundraiser_id );
                        $fund_amount  = '$' . number_format( $payments->get_total_by_fundraiser_id( $fundraiser_id ) );

                        // Set custom html email template params
                        $title     = get_the_title( $fundraiser_id );
                        $ended     = date( "m/d/Y", $end_date );
                        $permalink = get_permalink( $fundraiser_id );

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

                        // Set the output.
                        $for_output = $template_args;
                        $output[]   = array( 'FUNDRAISER_ID' => $fundraiser_id ) + $for_output; // add to front of array
                    } catch ( Exception $e ) {
                        if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
                            newrelic_notice_error( $e->getMessage(), $e );
                        }

                        $output[] = array( 'FUNDRAISER_ID' => $fundraiser_id, 'STATUS' => $e->getMessage() ); // add to front of array
                    }
//                    
                }
            endwhile;
        endif;

        $array                     = array();
        $array['fundraiser_array'] = array();
        $array['dbData']           = $output;
        $array['fundraiser_count'] = $array['email_count']      = count( $output );
        foreach ( $output as $item ) {
            if ( isset( $item['FUNDRAISER_ID'] ) ) {
                $array['email_count']        += count( $item['PARTICIPANTS'] );
                $array['fundraiser_array'][] = array(
                    'FID'    => $item['FUNDRAISER_ID'],
                    'FNAME'  => $item['FUNDRAISER_NAME'],
                    'EMAILS' => $array['email_count']
                );
            }
        }
        return $array;
    }

    private function process_low_participants( $period, $cron_date ) {
        ini_set( 'memory_limit', '-1' );
        $output      = array();
        $target_ymd  = date( "Ymd", strtotime( str_replace( '-', '/', $cron_date ) . " + " . $period . " days", current_time( 'timestamp', 0 ) ) );
        $target_date = strtotime( $target_ymd );
        $args        = array(
            'post_type'      => 'fundraiser',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
//            'no_found_rows'          => true,
//            'update_post_term_cache' => false,
            'fields'         => 'ids',
            'author__not_in' => [ '' ],
            'meta_query'     => array(
                array(
                    'key'     => 'end_date',
                    'value'   => $target_ymd,
                    'compare' => '=',
                ),
            ),
        );

        load_class( 'admins.class.php' );
        $Admins           = new \Admins();
        $fundraiser_query = new \WP_Query( $args );

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

                    load_class( 'participant_records2.class.php' );

                    $sharing = new \Participant_Records2();

                    // Get all of the sharing records for this fundraiser
                    $low_participants = $sharing->get_low_sharing_by_fid( $fundraiser_id );
                    // Show each one and their results to the coach
                    if ( !empty( $low_participants ) && count( $low_participants ) > 0 ) {

                        ## TODO: Get actual admins ###

                        $participants = array();

                        // Get some values
                        $admin_email = _CRON_FROM_EMAIL;
                        $title       = get_the_title( $fundraiser_id );
                        $ended       = date( "m/d/Y", $end_date );
                        $permalink   = get_permalink( $fundraiser_id );

                        // Set the template arguments to pass to the email template
                        $template_args = array(
                            'LINK_TO_FUNDRAISER_PAGE' => $permalink,
                            'FUNDRAISER_NAME'         => $title,
                            'END_DATE'                => $ended,
                            'ADMIN_EMAIL'             => $admin_email,
                            'LOGO_URL'                => get_template_directory_uri() . '/assets/images/email-logo.png'
                        );

                        foreach ( $low_participants as $p ) {

                            // Skip if an admin, post author, sadmin
                            if ( in_array( $p['participant_id'], $admins ) ) {
                                continue;
                            }

//                                $output[] = process_low_participants_player_loop($p, $template_args, $mail_args);
                            $participants[]                    = $p;
                            $template_args['PARTICIPANT_NAME'] = $p['participant_name'];
                        }

                        // Set the output
                        $template_args['TABLE']        = '';
                        $template_args['PARTICIPANTS'] = $participants;
                        $for_output                    = $template_args;
                        $for_output                    = array( 'FUNDRAISER_ID' => $fundraiser_id ) + $for_output; // add to front of array
                        $output[]                      = $for_output;
                    } else {
                        $output[] = array( 'FUNDRAISER_ID' => $fundraiser_id, 'OUTPUT' => 'none' );
                    }
                }

            endwhile;
        endif;

        $array                     = array();
        $array['fundraiser_array'] = array();
        $array['dbData']           = $output;
        $array['fundraiser_count'] = $array['email_count']      = count( $json );
        foreach ( $output as $item ) {
            if ( isset( $item['FUNDRAISER_ID'] ) ) {
                $array['email_count']        += count( $item['PARTICIPANTS'] );
                $array['fundraiser_array'][] = array(
                    'FID'    => $item['FUNDRAISER_ID'],
                    'FNAME'  => $item['FUNDRAISER_NAME'],
                    'EMAILS' => $array['email_count']
                );
            }
        }
        return $array;
    }

    private function process_low_participation( $period, $cron_date ) {
        ini_set( 'memory_limit', '-1' );
        $output = array();

        $target_ymd  = date( "Ymd", strtotime( str_replace( '-', '/', $cron_date ) . " + " . $period . " days", current_time( 'timestamp', 0 ) ) );
        $target_date = strtotime( $target_ymd );
        $args        = array(
            'post_type'      => 'fundraiser',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
//            'no_found_rows'          => true,
//            'update_post_term_cache' => false,
            'fields'         => 'ids',
            'author__not_in' => [ '' ],
            'meta_query'     => array(
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
        $fundraiser_query = new \WP_Query( $args );

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
//                    $output = process_low_participation_loop($fundraiser_id, $end_date, $admins);

                    load_class( 'participant_records2.class.php' );
                    $sharing = new \Participant_Records2();


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

                            $participants[] = $p;
                        }

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
                            'ADMIN_EMAIL'             => $admin_email,
                            'LOGO_URL'                => get_template_directory_uri() . '/assets/images/email-logo.png'
                        );

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
                }

            endwhile;
        endif;


        $array                     = array();
        $array['fundraiser_array'] = array();
        $array['dbData']           = $output;
        $array['fundraiser_count'] = $array['email_count']      = count( $json );
        foreach ( $output as $item ) {
            if ( isset( $item['FUNDRAISER_ID'] ) ) {
                $array['email_count']        += count( $item['PARTICIPANTS'] );
                $array['fundraiser_array'][] = array(
                    'FID'    => $item['FUNDRAISER_ID'],
                    'FNAME'  => $item['FUNDRAISER_NAME'],
                    'EMAILS' => $array['email_count']
                );
            }
        }
        return $array;
    }

}
