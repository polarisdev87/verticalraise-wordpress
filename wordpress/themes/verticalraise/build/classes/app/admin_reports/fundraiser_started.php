<?php

namespace classes\app\admin_reports;

use \classes\app\fundraiser\Fundraiser_Statistics;
use \classes\models\tables\Reports_Fundraisers_Reference;
use \classes\models\tables\Donations as Donations;
use \classes\models\tables\Donations_Total;
use \classes\models\tables\Donations_Count;

include_once( get_template_directory() . '/classes/participants.class.php' );
include_once( get_template_directory() . '/classes/goals.class.php' );

// Get specific libraries: http://sg.php.net/manual/en/function.extension-loaded.php

class Fundraisers_Started
{
    
    public function __construct() {
        ini_set('memory_limit', '-1');
    }
    
    public function get_fundraisers() {
        $date_range = $this->set_date_range();
        $reference = new Reports_Fundraisers_Reference();
        $fundraisers = $reference->get_by_start_date($date_range);
        
        $donation = new Donations();
        $donations_total = new Donations_Total();
        $donations_count = new Donations_Count();
        
        ####
        #
        # TODO: REWRITE
        #
        ####
        
        $x = 0;
        foreach ($fundraisers as $fundraiser) {
            
            $f_id = $fundraiser['f_id'];
            
            $fundraisers[$x]['fundraiser_link'] = "/?page_id={$f_id}";

            //$author = $this->get_author( get_post_field( 'post_author', $f_id ) ); // Not being used?

            $participants       = new \Participants();
            $participants_count = $participants->get_filtered_participant_ids_by_fid_count( $f_id );
            $total_share        = $participants->total_shares_count_by_uid( $f_id, false );

            $goal          = new \Goals;
            $goal_amount   = $goal->get_goal( $f_id );
            $raised_amount = $donations_total->get_single_sums_row( $f_id );
            $num_donators = $donations_count->get_single_count_row( $f_id );

            $fundraiser_statistics = new Fundraiser_Statistics( $f_id );
            
            ###
            #
            # All of this should be in a table somewhere
            #
            ###
	        $secondary_end_date    = get_post_meta( $fundraiser['f_id'], 'secondary_end_date', true );

	        $fundraisers[$x]['start_date']             = date("m/d/Y", strtotime($fundraiser['start_date'])); // Paul wants in specific format
            $fundraisers[$x]['end_date']               = date("m/d/Y", strtotime($fundraiser['end_date'])); // Paul wants in specific format
	        $fundraisers[$x]['secondary_end_date']     = ( $secondary_end_date )? date( 'm/d/Y', strtotime( $secondary_end_date, current_time( 'timestamp' ) ) ) : null;
            $fundraisers[$x]['created_date']           = get_the_date( 'm/d/Y', $f_id );
            $fundraisers[$x]['con_name']               = get_post_meta( $f_id, 'con_name', true );
            $fundraisers[$x]['con_number']             = get_post_meta( $f_id, 'phone', true );
            $fundraisers[$x]['con_email']              = get_post_meta( $f_id, 'email', true );
            $fundraisers[$x]['org_type']               = get_post_meta( $f_id, 'org_type', true );
            $fundraisers[$x]['hear_about_us']          = get_post_meta( $f_id, 'hear_about_us', true );
            $fundraisers[$x]['rep_name']               = get_post_meta( $f_id, 'coach_name', true );
            $fundraisers[$x]['rep_email']              = get_post_meta( $f_id, 'coach_email', true );
            $fundraisers[$x]['rep_code']               = get_post_meta( $f_id, 'coach_code', true );
            $fundraisers[$x]['check_pay']              = get_post_meta( $f_id, 'check_pay', true );
            $fundraisers[$x]['mailing_address']        = get_post_meta( $f_id, 'mailing_address', true );
            $fundraisers[$x]['street']                 = get_post_meta( $f_id, 'street', true );
            $fundraisers[$x]['city']                   = get_post_meta( $f_id, 'city', true );
            $fundraisers[$x]['state']                  = get_post_meta( $f_id, 'state', true );
            $fundraisers[$x]['zipcode']                = get_post_meta( $f_id, 'zipcode', true );
            $fundraisers[$x]['participants_count']     = $participants_count;
            $fundraisers[$x]['total_supporters']       = $num_donators;
            $fundraisers[$x]['total_raised']           = (!empty($raised_amount->amount)) ? $raised_amount->amount : 0;
            $fundraisers[$x]['emails_sent_num']        = (!empty( $total_share['email'] )) ? $total_share['email'][0] : 0;
            $fundraisers[$x]['sms_donation']           = (!empty( $total_share['sms'] )) ? $total_share['sms'][0] : 0;
            $fundraisers[$x]['parents_share']          = (!empty( $total_share['parents'] )) ? $total_share['parents'][0] : 0;
            $fundraisers[$x]['participation_score']    = $fundraiser_statistics->participation_score() * 100;
            $fundraisers[$x]['email_quality_score']    = $fundraiser_statistics->email_quality_score() * 100;
            $fundraisers[$x]['participant_donation']   = $fundraiser_statistics->participant_score() * 100;

            //'start_data'             => date( 'm/d/Y', strtotime( $fundraiser_start_date, current_time( 'timestamp' ) ) ),
            //'end_date'               => date( 'm/d/Y', strtotime( $fundraiser_end_date, current_time( 'timestamp' ) ) ),

            $x++;
        }
        
        return $fundraisers;
    }
    
    public function set_date_range() {
        
        $date_range_type = (isset( $_POST['date_range'] ) && $_POST['date_range'] != '') ? $_POST['date_range'] : '';
 
        switch ( $date_range_type ) {
            case '' :
            case 'this_week' :
                $date['from'] = date("Y-m-d H:i:s", strtotime( 'monday this week', strtotime( current_time( "Ymd", 0 ) ) ) );
                $date['to']   = date("Y-m-d H:i:s", strtotime( 'sunday this week', strtotime( current_time( "Ymd", 0 ) ) ) );
                break;
            case 'last_week' :
                $date['from'] = date("Y-m-d H:i:s", strtotime( 'monday last week', strtotime( current_time( "Ymd", 0 ) ) ) );
                $date['to'] = date("Y-m-d H:i:s", strtotime( 'sunday last week', strtotime( current_time( "Ymd", 0 ) ) ) );
                break;
            case 'this_month' :
                $date['from'] = date("Y-m-d H:i:s", strtotime( 'first day of this month', strtotime( current_time( "Ymd", 0 ) ) ) );
                $date['to'] = date("Y-m-d H:i:s", strtotime( 'last day of this month', strtotime( current_time( "Ymd", 0 ) ) ) );
                break;
            case 'last_month' :
                $date['from'] = date("Y-m-d H:i:s", strtotime( 'first day of previous month', strtotime( current_time( "Ymd", 0 ) ) ) );
                $date['to'] = date("Y-m-d H:i:s", strtotime( 'last day of previous month', strtotime( current_time( "Ymd", 0 ) ) ) );
                break;
            case 'today' :
                $date['from'] = date("Y-m-d H:i:s", strtotime( current_time( "Ymd", 0 ) ) );
                $date['to'] = null;
                break;
            case 'yesterday' :
                $date['from'] = date("Y-m-d H:i:s", strtotime( '-1 day', strtotime( current_time( "Ymd", 0 ) ) ) );
                $date['to'] = null;
                break;
            case 'pick_date' :
                $date['from'] = date("Y-m-d H:i:s", strtotime( $_POST['check_date_start'], current_time( 'timestamp' ) ) );
                $date['to'] = date("Y-m-d H:i:s", strtotime( $_POST['check_date_end'], current_time( 'timestamp' )  ));
                break;
            default:
                $date['from'] = date("Y-m-d H:i:s", strtotime( 'monday this week', strtotime( current_time( "Ymd", 0 ) ) ) );
                $date['to'] = date("Y-m-d H:i:s", strtotime( 'sunday this week', strtotime( current_time( "Ymd", 0 ) ) ) );
                break;
        }

        return $date;
    }

    public function get_author( $uid ) {
        $user_info = get_userdata( $uid );
        return $user_info->display_name;
    }


    public function get_current_time_formatted() {
        return current_time( 'Y-m-d H:i:s' );
    }

}
