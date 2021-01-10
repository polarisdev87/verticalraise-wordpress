<?php

namespace classes\app\email_queue;

use \classes\models\tables\Email_Queue;
use \classes\app\initial_send\Build_Email as Build_Email;

class Bulk
{
    private $email_queue;

    
    public function __construct() {
        $this->email_queue = new Email_Queue();
    }
    
    public function queue($emails, $user_id, $fundraiser_id, $type, $logged_in, $logged_in_u_id, $parent, $share_type) {
        $info = new Build_Email();

        $messages = '';
        foreach ( $emails as $email ) {
            try {
                $results['message'] = $this->email_queue->insert($email, $user_id, $fundraiser_id, $type, $info->set_from_name($user_id), $logged_in, $logged_in_u_id, $parent, $share_type);
                // messages
            }
            catch(Exception $e) {
                $messages['errors'][$email] = $e->getMessage();
            }
        }
        
        return $results;
    }

}