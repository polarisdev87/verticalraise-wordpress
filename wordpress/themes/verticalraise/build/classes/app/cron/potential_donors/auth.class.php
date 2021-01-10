<?php

namespace classes\app\cron\potential_donors;

class Auth
{
    
    public function __construct($period = null, $allowed = array()) {
        $this->period  = $period;
        $this->allowed = allowed;
    }

    public function run() {
        $this->is_empty();
        $this->is_allowed();
        $this->is_type();
    }
    
    private function is_empty() {
        if ( empty($this->period) ) {
            throw new Exception("Missing number of days");
        }
    }
    
    private function is_allowed() {
        if ( !in_array($this->period, $this->allowed) ) {
            throw new Exception ("Param is not in allowed array");
        }
    }
    
    private function is_type() {
        if ( !is_int($this->period) ) {
            throw new Exception("Param is not an interger");
        }
    }

}