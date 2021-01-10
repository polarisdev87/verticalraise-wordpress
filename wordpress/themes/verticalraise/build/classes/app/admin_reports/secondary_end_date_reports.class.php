<?php

namespace classes\app\admin_reports;

use \classes\app\fundraiser\Secondary_End_Date;
use \classes\app\fundraiser\Fundraiser_Statistics;
use \classes\models\tables\Donations as Donations;

include_once( get_template_directory() . '/classes/participants.class.php' );
include_once( get_template_directory() . '/classes/goals.class.php' );
include_once( get_template_directory() . '/classes/payment_records.class.php' );
// Get specific libraries: http://sg.php.net/manual/en/function.extension-loaded.php

class Secondary_End_Date_Reports
{

    public function __construct() {
        ini_set('memory_limit', '-1');
    }
    
    public function get_all_ended_fundraisers() {
        $ended_fids       = array();
        $fundraiser_query = $this->get_all_fundraisers();

        if ( $fundraiser_query->have_posts() ) :

            while ( $fundraiser_query->have_posts() ) : $fundraiser_query->the_post();

                // Fundraiser ID
                $fundraiser_id = get_the_ID();
                $title         = get_the_title();

                //Check ended fundraisers
                $matched = $this->check_match_fundraisers( $fundraiser_id );
                if ( $matched ) {

	                $fundraiser_link               = get_permalink( $fundraiser_id );
	                $fundraiser_start_date         = get_post_meta( $fundraiser_id, 'start_date', true );
	                $fundraiser_end_date           = get_post_meta( $fundraiser_id, 'end_date', true );
	                $fundraiser_secondary_end_date = get_post_meta( $fundraiser_id, 'secondary_end_date', true );

                    $author                = $this->get_author( get_post_field( 'post_author', $fundraiser_id ) );

                    $participants       = new \Participants();
                    $participants_count = $participants->get_filtered_participant_ids_by_fid_count( $fundraiser_id );
                    $total_share        = $participants->total_shares_count_by_uid( $fundraiser_id, false );

                    $goal          = new \Goals;
                    $goal_amount   = $goal->get_goal( $fundraiser_id );

                    $payment_recods = new \Payment_Records();
                    $raised_amount = $payment_recods->get_total_to_end_date( $fundraiser_id );
	                $secondary_raised_amount = $payment_recods->get_total_since_end_date( $fundraiser_id );

                    $fundraiser_statistics = new Fundraiser_Statistics( $fundraiser_id );
                    $donation              = new Donations();
                    $stripe_connect = get_post_meta( $fundraiser_id, 'stripe_connect', true );
                    if ( $stripe_connect == 1 ) {
                        $our_fee_opt = get_post_meta( $fundraiser_id, 'our_fee', true );
                    } else {
                        $our_fee_opt = 0;
                    }
                    $labels = [
                        "Pay by check",
                        "Deposit 100%",
                        "Take fee out",
                    ];

                    $our_fee = $labels[$our_fee_opt];
                    $ended_fids[] = array(
                        'ID'                     => $fundraiser_id,
                        'fname'                  => $title,
                        'fundraiser_link'        => $fundraiser_link,
                        'created_date'           => get_the_date("m/d/Y"),
                        'start_data'             => date( 'm/d/Y', strtotime( $fundraiser_start_date, current_time( 'timestamp' ) ) ),
                        'end_date'               => date( 'm/d/Y', strtotime( $fundraiser_end_date, current_time( 'timestamp' ) ) ),
                        'secondary_end_date'     => date( 'm/d/Y', strtotime( $fundraiser_secondary_end_date, current_time( 'timestamp' ) ) ),
                        'our_fee'                => $our_fee,
                        'con_name'               => get_post_meta( $fundraiser_id, 'con_name', true ),
                        'con_number'             => get_post_meta( $fundraiser_id, 'phone', true ),
                        'con_email'              => get_post_meta( $fundraiser_id, 'email', true ),
                        'org_type'               => get_post_meta( $fundraiser_id, 'org_type', true ),
                        'hear_about_us'          => get_post_meta( $fundraiser_id, 'hear_about_us', true ),
                        'rep_name'               => get_post_meta( $fundraiser_id, 'coach_name', true ),
                        'rep_email'              => get_post_meta( $fundraiser_id, 'coach_email', true ),
                        'rep_code'               => get_post_meta( $fundraiser_id, 'coach_code', true ),
                        'check_pay'              => get_post_meta( $fundraiser_id, 'check_pay', true ),
                        'mailing_address'        => get_post_meta( $fundraiser_id, 'mailing_address', true ),
                        'street'                 => get_post_meta( $fundraiser_id, 'street', true ),
                        'city'                   => get_post_meta( $fundraiser_id, 'city', true ),
                        'state'                  => get_post_meta( $fundraiser_id, 'state', true ),
                        'zipcode'                => get_post_meta( $fundraiser_id, 'zipcode', true ),
                        'author'                 => $author,
                        'participants_count'     => $participants_count,
                        'total_supporters'       => $donation->get_total_donors_by_fid( $fundraiser_id ),
                        'goal_amount'            => $goal_amount,
                        'total_raised'           => $raised_amount,
                        'secondary_raised'       => $secondary_raised_amount,
                        'emails_sent_num'        => (!empty( $total_share['email'] )) ? $total_share['email'][0] : 0,
                        'sms_donation'           => (!empty( $total_share['sms'] )) ? $total_share['sms'][0] : 0,
                        'parents_share'          => (!empty( $total_share['parents'] )) ? $total_share['parents'][0] : 0,
                        'participation_score'    => $fundraiser_statistics->participation_score() * 100,
                        'email_quality_score'    => $fundraiser_statistics->email_quality_score() * 100,
                        'participant_donation'   => $fundraiser_statistics->participant_score() * 100
                    );
                }

            endwhile;
            wp_reset_postdata();
        endif;

        return $ended_fids;
    }

    public function get_author( $uid ) {
        $user_info = get_userdata( $uid );
        return $user_info->display_name;
    }

    private function get_all_fundraisers() {
        $args = array(
            'post_type'      => 'fundraiser',
            'post_status'    => 'publish',
            'posts_per_page' => -1
        );

        $fundraiser_query = new \WP_Query( $args );
        return $fundraiser_query;
    }

    private function check_match_fundraisers( $fundraiser_id ) {
        // Fundraiser end date
        $fundraiser_end = new Secondary_End_Date( $fundraiser_id );
        $end_date       = $fundraiser_end->f_enddate();

        $range_type = (isset( $_POST['date_range'] ) && $_POST['date_range'] != '') ? $_POST['date_range'] : '';
        switch ( $range_type ) {
            case '' :
            case 'this_week' :
                $from             = strtotime( 'monday this week', strtotime( current_time( "Ymd", 0 ) ) );
                $to               = strtotime( 'sunday this week', strtotime( current_time( "Ymd", 0 ) ) );
                $matched          = $this->check_match_range( $from, $to, $end_date );
                break;
            case 'last_week' :
                $from             = strtotime( 'monday last week', strtotime( current_time( "Ymd", 0 ) ) );
                $to               = strtotime( 'sunday last week', strtotime( current_time( "Ymd", 0 ) ) );
                $matched          = $this->check_match_range( $from, $to, $end_date );
                break;
            case 'this_month' :
                $from             = strtotime( 'first day of this month', strtotime( current_time( "Ymd", 0 ) ) );
                $to               = strtotime( 'last day of this month', strtotime( current_time( "Ymd", 0 ) ) );
                $matched          = $this->check_match_range( $from, $to, $end_date );
                break;
            case 'last_month' :
                $from             = strtotime( 'first day of previous month', strtotime( current_time( "Ymd", 0 ) ) );
                $to               = strtotime( 'last day of previous month', strtotime( current_time( "Ymd", 0 ) ) );
                $matched          = $this->check_match_range( $from, $to, $end_date );
                break;
            case 'today' :
                $check_date       = strtotime( current_time( "Ymd", 0 ) );
                $matched          = $this->check_match_pick( $check_date, $end_date );
                break;
            case 'yesterday' :
                $check_date       = strtotime( '-1 day', strtotime( current_time( "Ymd", 0 ) ) );
                $matched          = $this->check_match_pick( $check_date, $end_date );
                break;
            case 'pick_date' :
                $check_date_start = strtotime( $_POST['check_date_start'], current_time( 'timestamp' ) );
                $check_date_end   = strtotime( $_POST['check_date_end'], current_time( 'timestamp' ) );
                $matched          = $this->check_match_range( $check_date_start, $check_date_end, $end_date );
                break;
            default:
                $matched          = false;
                break;
        }

        return $matched;
    }

    public function get_current_time_formatted() {
        return current_time( 'Y-m-d H:i:s' );
    }

    private function check_match_range( $from, $to, $enddate ) {

        $check = ($enddate >= $from && $enddate <= $to) ? true : false;
        return $check;
    }

    private function check_match_pick( $pickdate, $enddate ) {
        $check = ( $enddate - $pickdate == 0) ? true : false;
        return $check;
    }

}
