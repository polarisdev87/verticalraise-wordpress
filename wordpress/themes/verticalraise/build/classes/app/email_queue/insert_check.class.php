<?php

namespace classes\app\email_queue;

use \classes\models\tables\Email_Queue;
use \classes\models\tables\Email_Input;

class Insert_Check
{

    public function __construct() {
        $this->postData    = $_POST;
        $this->email_queue = new Email_Queue();
        $this->email_input = new Email_Input();
    }

    public function check_email_queue() {
        $limit        = 5000;
        $array        = array();
        $input_result = $this->email_input->get_emails_by_id_order( $limit );
        $queue_result = $this->email_queue->get_emails_by_id_order( $limit );

        $array = $this->get_results( $input_result, $queue_result );

        return $array;
    }

    public function get_results( $input_array, $queue_array ) {
        $result_array = array();
        foreach ( $input_array as $key => $value ) {
            if ( !array_key_exists( $key, $queue_array ) ) {
                $result_array[] = $value;
            }
        }
        return $result_array;
    }

    private function get_range_dateformatted( $from, $to ) {
        $range['from'] = date( "Y-m-d", strtotime( $from, strtotime( current_time( "Ymd", 0 ) ) ) );
        $range['to']   = date( "Y-m-d", strtotime( $to, strtotime( current_time( "Ymd", 0 ) ) ) );
        return $range;
    }

    public function insert_emails() {
        $success_insert = 0;
        $failed_insert  = 0;
        if ( $this->postData["id_array"] != '' ) {
            $id_array = explode( ",", $this->postData["id_array"] );
            foreach ( $id_array as $id ) {
                $input_array = $this->email_input->get_by_id( $id );
                if ( $input_array ) {
                    $return = $this->email_queue->insert_missing_emails( $input_array[0] );
                    if ( $return ) {
                        $success_insert++;
                    } else {
                        $failed_insert++;
                    }
                }
            }
        }
        $result = array(
            "success" => $success_insert,
            "fail"    => $failed_insert
        );
        return $result;
    }

}
