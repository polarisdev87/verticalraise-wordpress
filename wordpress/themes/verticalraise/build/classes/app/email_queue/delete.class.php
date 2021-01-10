<?php

namespace classes\app\email_queue;

use \classes\models\tables\Email_Queue;

class Delete
{
   
    public function __construct() {
        $this->email_queue = new Email_Queue();
    }

    public function delete_all() {
        // Delete all unverified.
        $this->email_queue->delete_unverified();
        // Delete all verified/sent/moved.
        $this->email_queue->delete_moved();
        // Delete all sent emails for channel 2.
        $this->email_queue->delete_channel2_sent();
    }
    
}