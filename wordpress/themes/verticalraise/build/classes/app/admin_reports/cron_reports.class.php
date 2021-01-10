<?php

namespace classes\app\admin_reports;

use \classes\models\tables\Cron_log;

// Get specific libraries: http://sg.php.net/manual/en/function.extension-loaded.php

class Cron_Reports
{

    private $json_folder;
    private $cron_log;

    public function __construct() {
        ini_set('memory_limit', '-1');
        $this->cron_log = new Cron_Log();
    }

    public function get_cron_data() {
        $array           = array();
        $formatted_range = array();

        if ( isset( $_POST['check_date'] ) && $_POST['check_date'] != '' ) {
            $pick_date = date( "Y-m-d", strtotime( $_POST['check_date'], current_time( 'timestamp' ) ) );
        } else {
            $pick_date = current_time( "Y-m-d", 0 );
        }

        $range_type = (isset( $_POST['date_range'] ) && $_POST['date_range'] != '') ? $_POST['date_range'] : '';
        $range_flag = true;
        switch ( $range_type ) {
            case '' :
            case 'this_week' :
                $formatted_range = $this->get_range_dateformatted( 'monday this week', 'sunday this week' );
                break;
            case 'last_week' :
                $formatted_range = $this->get_range_dateformatted( 'monday last week', 'sunday last week' );
                break;
            case 'this_month' :
                $formatted_range = $this->get_range_dateformatted( 'first day of this month', 'last day of this month' );
                break;
            case 'last_month' :
                $formatted_range = $this->get_range_dateformatted( 'first day of previous month', 'last day of previous month' );
                break;
            //
            case 'today' :
                $check_date      = current_time( "Y-m-d", 0 );
                $range_flag      = false;
                break;
            //
            case 'yesterday' :
                $check_date      = date( "Y-m-d", strtotime( '-1 day', strtotime( current_time( "Y-m-d", 0 ) ) ) );
                $range_flag      = false;
                break;
            case 'pick_date' :
                $check_date      = date( "Y-m-d", strtotime( $_POST['check_date'], current_time( 'timestamp' ) ) );
                $range_flag      = false;
                break;
            default:
                $result          = array();
                break;
        }

        if ( $range_flag ) {
            $result = $this->cron_log->get_cronlog_by_range( $formatted_range['from'], $formatted_range['to'] );
        } else {
            $result = $this->cron_log->get_cronlog_by_date( $check_date );
        }

        if ( !empty( $result ) ) {
            $array = $this->get_log_array( $result );
        }

        return $array;
    }

    public function get_log_array( $results ) {
        $type_array = array(
            '2_days'               => '2_days',
            '7_days'               => '7_days',
            '14_days'              => '14_days',
            'f_ended_c'            => 'fundraisers_ended_coach',
            'l_participate_10days' => 'process_low_participation10days',
            'l_participate_17days' => 'process_low_participation17days',
            'l_participate_19days' => 'process_low_participation19days',
            'l_participant_10days' => 'process_low_participants10days',
            'l_participant_17days' => 'process_low_participants17days',
            'l_participant_19days' => 'process_low_participants19days',
        );
        $log_array  = array();
        foreach ( $results as $item ) {
            $status = $this->check_status( $item );
            if ( $item->type == 'l_participant' || $item->type == 'l_participate' || $item->type == 'f_ended_a' ) {
                continue;
            }
            $filename    = date( "m-d-Y", strtotime( $item->started ) ) . "_" . $type_array[$item->type] . ".json";
            $info        = $this->get_count_by_type( $item->type, $item->started, $filename );
            $log_array[] = array(
                'ID'               => $item->id,
                'type'             => $item->type,
                'started'          => $item->started,
                'ended'            => $item->ended,
                'filename'         => $type_array[$item->type],
                'status'           => $status['finished'],
                'duration'         => $status['duration'],
                'email_count'      => $info['email_count'],
                'fundraiser_count' => $info['fundraiser_count']
            );
        }

        return $log_array;
    }

    private function get_range_dateformatted( $from, $to ) {
        $range['from'] = date( "Y-m-d", strtotime( $from, strtotime( current_time( "Ymd", 0 ) ) ) );
        $range['to']   = date( "Y-m-d", strtotime( $to, strtotime( current_time( "Ymd", 0 ) ) ) );
        return $range;
    }

    private function check_status( $item ) {
        $data      = array();
        $starttime = strtotime( $item->started, strtotime( current_time( 'timestamp' ) ) );
        $endtime   = strtotime( $item->ended, strtotime( current_time( 'timestamp' ) ) );
        if ( $endtime < 0 ) {
            $data['finished'] = false;
            $data['duration'] = '';
        } else {
            $data['finished'] = true;
            $diff             = $endtime - $starttime;
            $data['duration'] = $this->duration_format( $diff );
        }

        return $data;
    }

    private function duration_format( $diff ) {
        $hours   = floor( $diff / 3600 );
        $minutes = floor( ($diff / 60) % 60 );
        $seconds = $diff % 60;
        $format  = '';
        if ( $hours != 0 ) {
            $format .= $hours . "hours ";
        }
        if ( $minutes != 0 ) {
            $format .= $minutes . "min ";
        }
        $format .= $seconds . "sec";
        return $format;
    }

    public function getjson( $post_data ) {
        $cron_data = $post_data['cron_date'];
        $cron_type = $post_data['cron_type'];
        $started   = $post_data['started'];
        $filename  = $post_data['filename'];
        return $this->get_count_by_type( $cron_type, $started, $filename );
//        return $this->read_json($filename);
    }

    private function read_json( $filename ) {
        $upload_dir      = wp_upload_dir();
        $upload_jsonPath = $upload_dir['basedir'] . '/cron_log/' . $filename;
        if ( file_exists( $upload_jsonPath ) !== false ) {
            return json_decode( file_get_contents( $upload_jsonPath ), true );
        } else {
            return "nodata";
        }
    }

    private function get_count_by_type( $type, $started, $filename ) {
        $cron_date = date( "m-d-Y", strtotime( $started, strtotime( current_time( "m-d-Y", 0 ) ) ) );
//        $filename  = $cron_date . "_" . $filename . ".json";
        $json_data = $this->read_json( $filename );
        if ( $json_data == 'nodata' ) {
            $data['jsonData']         = array();
            $data['email_count']      = 0;
            $data['fundraiser_count'] = 0;
            $data['fundraiser_array'] = array();
            return $data;
        }
        switch ( $type ) {
            case '2_days':
            case '7_days':
            case '14_days':
                $data = $this->potential_info( $json_data );
                break;
            case 'f_ended_a':
            case 'f_ended_c':
                $data = $this->ended_info( $json_data );
                break;
            case 'l_participate_10days':
            case 'l_participate_17days':
            case 'l_participate_19days':
                $data = $this->participation_info( $json_data );
                break;
            case 'l_participant_10days':
            case 'l_participant_17days':
            case 'l_participant_19days':
                $data = $this->participant_info( $json_data );
                break;
            default:
                break;
        }
        $data['jsonData'] = $json_data;
        return $data;
    }

    private function potential_info( $json ) {
        $array['fundraiser_count'] = $json['fundraiser_count'];
        $array['email_count']      = 0;
        $array['fundraiser_array'] = array();
        if ( $json['fundraiser_count'] != 0 ) {
            foreach ( (array) $json['fundraisers'] as $key => $fundraiser ) {
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

    private function participant_info( $json ) {
        $p_check                   = false;
        $array['fundraiser_count'] = count( $json );
        $array['email_count']      = 0;
        $array['fundraiser_array'] = array();

        foreach ( $json as $item ) {
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

    private function participation_info( $json ) {

        $array['fundraiser_array'] = array();
        $array['fundraiser_count'] = $array['email_count']      = count( $json );
        foreach ( $json as $item ) {
            if ( isset( $item['FUNDRAISER_ID'] ) ) {

                $array['fundraiser_array'][] = array(
                    'FID'    => $item['FUNDRAISER_ID'],
                    'FNAME'  => $item['FUNDRAISER_NAME'],
                    'EMAILS' => $array['email_count']
                );
            }
        }
        return $array;
    }

    private function ended_info( $json ) {

        if ( $json != NULL ) {
            $array['fundraiser_array'] = array();
            $array['fundraiser_count'] = $array['email_count']      = count( $json );
            foreach ( $json as $item ) {
                if ( isset( $item['FUNDRAISER_ID'] ) && isset( $item['FUNDRAISER_NAME'] ) ) {

                    $array['fundraiser_array'][] = array(
                        'FID'    => $item['FUNDRAISER_ID'],
                        'FNAME'  => $item['FUNDRAISER_NAME'],
                        'EMAILS' => $array['email_count']
                    );
                }
            }
        } else {
            $array['fundraiser_array'] = array();
            $array['fundraiser_count'] = $array['email_count']      = 0;
        }
        return $array;
    }

}
