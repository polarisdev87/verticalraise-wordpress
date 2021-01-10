<?php

namespace classes\app\cron;

use \classes\models\tables\Cron_log;

class Log_Database
{
    
    private $log_id;
    
    public function __construct() {
        $this->log_id;
        $this->db = new Cron_Log();
    }
    
    public function run($period) {
        $this->check($period);
        
    }
    
    private function check() {
        // DO we already have an entry for today?
        $check = $this->db->check_existing($this->period);
        if ( !empty($check) ) {
            throw new Exception("Cron already ran today");
        } else {
            $this->log_id = $this->db->insert($period);
        }
    }
    
    private function set_period($period) {
        $this->period = $period;
    }
}