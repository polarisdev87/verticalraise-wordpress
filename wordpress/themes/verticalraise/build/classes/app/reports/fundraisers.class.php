<?php

namespace classes\app\reports;

class Fundraisers {
    public static function get_report( $type , $date_range, $start_date, $end_date ) {
        global $wpdb;

        if ( $date_range != 'pick_date') {
            $dateArr = Fundraisers::get_date($date_range);
            $start_date = $dateArr['from'];
            $end_date = $dateArr['to'];
        }

        $suggested_email_amount = _SUGGESTED_EMAIL_AMOUNT ;

        if ( $type === 1 ) {
            $query = "SELECT p.ID, p.post_author, p.post_date, post_title, post_name, 
            ( SELECT CONCAT_WS( '-' , COUNT(*), SUM(d.amount) ) FROM donations d WHERE d.f_id = p.id AND refunded = 0 AND deleted = 0 ) AS supporters_amount, 
            ( SELECT CONCAT_WS( '-' , SUM(email), SUM(sms), SUM(parents) ) FROM participant_fundraiser_details pfd WHERE pfd.fundraiser = p.id ) AS email_sms_parents, 
            ( SELECT COUNT(*) FROM participant_fundraiser_details pfd WHERE pfd.fundraiser = p.id  AND email >= {$suggested_email_amount} ) AS pgoal,
            ( SELECT COUNT(*) FROM participant_fundraiser_details pfd WHERE pfd.fundraiser = p.id  AND total > 0 ) AS participants_that_raised,
            ( SELECT COUNT(*) FROM fundraiser_participants fp WHERE fp.f_id = p.id ) AS participants,
            JSON_OBJECTAGG(m.meta_key, m.meta_value) AS meta 
            FROM wp_posts p LEFT JOIN wp_postmeta m ON p.ID = m.post_id 
            WHERE p.post_type = 'fundraiser' AND p.post_status = 'publish' AND p.post_date BETWEEN %s AND %s 
            GROUP BY p.ID ";
        } else {

            if ( $type === 2 ) {
                $var = "start_date";
            } else {
                $var = "end_date";
            }

            $query = "SELECT p.ID, p.post_author, p.post_date, post_title, post_name, 
            ( SELECT CONCAT_WS( '-' , COUNT(*), SUM(d.amount) ) FROM donations d WHERE d.f_id = p.id AND refunded = 0 AND deleted = 0 ) AS supporters_amount, 
            ( SELECT CONCAT_WS( '-' , SUM(email), SUM(sms), SUM(parents) ) FROM participant_fundraiser_details pfd WHERE pfd.fundraiser = p.id ) AS email_sms_parents, 
            ( SELECT COUNT(*) FROM participant_fundraiser_details pfd WHERE pfd.fundraiser = p.id  and email >= {$suggested_email_amount} ) AS pgoal,
            ( SELECT COUNT(*) FROM participant_fundraiser_details pfd WHERE pfd.fundraiser = p.id  AND total > 0 ) AS participants_that_raised,
            ( SELECT COUNT(*) FROM fundraiser_participants fp WHERE fp.f_id = p.id ) AS participants,
            JSON_OBJECTAGG(m.meta_key, m.meta_value) AS meta 
            FROM wp_posts p LEFT JOIN wp_postmeta m ON p.ID = m.post_id WHERE p.post_type = 'fundraiser'  AND p.post_status = 'publish'   
            GROUP BY p.ID 
            HAVING p.ID IN 
            ( 
                SELECT m.post_id FROM wp_postmeta m WHERE m.meta_key = '$var' 
                AND CAST(m.meta_value as DATE) BETWEEN CAST(%s as DATE) AND CAST(%s as DATE)  
            ) ";
        }

        $query = $wpdb->prepare( $query, array( $start_date, $end_date ) );

        $results = $wpdb->get_results( $query , ARRAY_A );

        foreach ( $results as $key => $item ) {

            $results[ $key ]['meta'] = json_decode( $item['meta'], true );

            $post_created_date = \DateTime::createFromFormat ( 'Y-m-d H:i:s', $results[ $key ]['post_date'] , new \DateTimeZone( "PDT" ));
            if ( $post_created_date )
                $results[ $key ]['post_date'] = $post_created_date->format("m/d/Y");

            $meta_start_date = \DateTime::createFromFormat ( 'Ymd', $results[ $key ]["meta"]['start_date'], new \DateTimeZone( "PDT" ) );
            if ( $meta_start_date )
                $results[ $key ]["meta"]['start_date'] = $meta_start_date->format("m/d/Y");

            $meta_end_date = \DateTime::createFromFormat ( 'Ymd', $results[ $key ]["meta"]['end_date'], new \DateTimeZone( "PDT" ) );
            if ( $meta_end_date )
                $results[ $key ]["meta"]['end_date'] = $meta_end_date->format("m/d/Y");

            $matches = null;
            $re = '/(?\'supporters\'\d+)-(?\'amount\'\d+)/m';
            $results[ $key ]['donation'] = array(
                'supporters' => 0,
                'amount'     => 0,
            );

            if ( preg_match ( $re, $item['supporters_amount'], $matches ) ) {
                $results[ $key ]['donation'] = array(
                    'supporters' => $matches['supporters'],
                    'amount'     => $matches['amount'],
                );
            }

            $matches = null;
            $re = '/(?\'email\'\d+)-(?\'sms\'\d+)-(?\'parents\'\d+)/m';
            $results[ $key ]['share'] = array(
                'email'   => 0,
                'sms'     => 0,
                'parents' => 0,
            );

            if ( preg_match ( $re, $item['email_sms_parents'], $matches ) ) {
                $results[ $key ]['share'] = array(
                    'email'   => $matches['email'],
                    'sms'     => $matches['sms'],
                    'parents' => $matches['parents'],
                );
            }

            if ( intval( $item['participants'] ) > 0 ) {
                $results[ $key ]['participation_score'] = number_format( ( intval( $item['pgoal'] ) / intval( $item['participants'] ) ) * 100, 2 );
                $results[ $key ]['participant_score']   = number_format( ( intval( $item['participants_that_raised'] ) / intval( $item['participants'] ) ) * 100, 2 );
            } else {
                $results[ $key ]['participation_score'] = 0;
                $results[ $key ]['participant_score']   = 0;
            }

            if ( intval( $results[ $key ]['share']['email'] ) > 0 && isset( $results[ $key ]['donation']['supporters'] ) && isset( $results[ $key ]['share']['email'] ) ) {
                $results[ $key ]['email_quality_score'] = number_format ( ( intval($results[ $key ]['donation']['supporters'] ) / intval( $results[ $key ]['share']['email'] ) ) * 100 , 2 );
            } else {
                $results[ $key ]['email_quality_score'] = 0;
            }

        }

        return $results;
    }

    public static function get_date( $date_range ) {

        $range_type = (isset( $date_range ) && $date_range != '') ? $date_range : '';
        switch ( $range_type ) {
            case '' :
            case 'this_week' :
                $from             = date( 'Ymd' , strtotime( 'monday this week', strtotime( current_time( "Ymd", 0 ) ) ));
                $to               = date( 'Ymd' , strtotime( 'sunday this week', strtotime( current_time( "Ymd", 0 ) ) ));
                break;
            case 'last_week' :
                $from             = date( 'Ymd' , strtotime( 'monday last week', strtotime( current_time( "Ymd", 0 ) ) ));
                $to               = date( 'Ymd' , strtotime( 'sunday last week', strtotime( current_time( "Ymd", 0 ) ) ));
                break;
            case 'this_month' :
                $from             = date( 'Ymd' , strtotime( 'first day of this month', strtotime( current_time( "Ymd", 0 ) ) ));
                $to               = date( 'Ymd' , strtotime( 'last day of this month', strtotime( current_time( "Ymd", 0 ) ) ));
                break;
            case 'last_month' :
                $from             = date( 'Ymd' , strtotime( 'first day of previous month', strtotime( current_time( "Ymd", 0 ) ) ));
                $to               = date( 'Ymd' , strtotime( 'last day of previous month', strtotime( current_time( "Ymd", 0 ) ) ));
                break;
            case 'today' :
                $from             = date( 'Ymd' , strtotime( current_time( "Ymd", 0 ) ));
                $to               = date( 'Ymd' , strtotime( current_time( "Ymd", 0 ) ));
                break;
            case 'yesterday' :
                $from             = date( 'Ymd' , strtotime( '-1 day', strtotime( current_time( "Ymd", 0 ) ) ));
                $to               = date( 'Ymd' , strtotime( '-1 day', strtotime( current_time( "Ymd", 0 ) ) ));
                break;
            default:
                $from = $to = false;
                break;
        }

        return compact("from", "to");
    }
}