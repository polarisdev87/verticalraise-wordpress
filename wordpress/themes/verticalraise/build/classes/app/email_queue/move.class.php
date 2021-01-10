<?php

namespace classes\app\email_queue;

use \classes\models\tables\Email_Queue as Email_Queue_Verify;
use \classes\models\tables\Potential_Donors_Email;

class Move
{

    public function __construct() {
        $this->email_queue = new Email_Queue_Verify();
        $this->potential_donors = new Potential_Donors_Email();
    }
    
    public function move() {
        $records = $this->email_queue->get_ready_to_move();
        $this->move_records($records);
    }
    
    private function move_records($records) {
        if ( !empty($records) ) {
            foreach ( $records as $record ) {
                try{
                    // Check and see if it already exists in Potential Donors
                    if ( $this->check_record_exists($record) != false ) {
                        $this->email_queue->update_moved($record['id'], 1);
                        continue;
                    }
                    $this->potential_donors->insert($record['email'], $record['u_id'], $record['f_id'], $record['type']);
                    if ( $this->check_record_exists($record) ) {
                        $this->email_queue->update_moved($record['id'], 1);
                    }
                } catch(Exception $e) {
                    echo $e->getMessage();
                }
            }
        }
    }
    
    private function check_record_exists($record) {
        return $this->potential_donors->get_by_email_uid_fid($record['email'], $record['u_id'], $record['f_id']);
    }

}