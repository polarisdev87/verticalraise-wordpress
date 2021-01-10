<?php

namespace classes\app\email_queue;

use classes\app\email_queue\Verify;
use classes\app\email_queue\Move;
use classes\app\email_queue\Send;
use classes\app\email_queue\Delete;

class Cron
{

    public function run($type) {
        
        switch($type) {
            case "verify":
                $this->verify();
                break;
            case "send":
                $this->send();
                break;
            case "move":
                $this->move();
                break;
            case "clear":
                $this->clear();
                break;
        }

    }
    
    // runs every minute
    private function verify() {
        $verify = new Verify();
        $verify->verify();
    }
    
    // runs every 5 minutes
    private function send() {
        $send = new Send();
        $send->send();
    }
    
    private function move() {
        $move = new Move();
        $move->move();
    }
    
    private function clear() {
        $delete = new Delete();
        $delete->delete_all();
    }
    
    private function purge() {
        
    }
    
    public function test() {

    }

}