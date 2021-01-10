<?php

namespace classes\app\cron\potential_donors;

use \classes\app\date\Date;

class Dates {
    
    private $date;
    private $period;
    private $current_timestamp;
    
    public function __construct($period) {
        $this->date = new Date();
        
        $this->curent_timestamp = $this->date->get_current_timestamp();
        $this->period = $period;
    }

    public function run() {
        
        $time = array();
    
        switch($this->period) {
                
            case 1:
                $time['start_date'] = $this->date->build_current_time("Ymd");
                break;
            case 3:
                $time['start_date'] = '';  ///??????
                break;
            case 14:
                $time['end_date'] = $this->get_date("+", "2", "weeks");
                break;
            case 7:
                $time['end_date'] = $this->get_date("+", "7", "days");
                break;
            case 2:
                $time["end_date"] = $this->get_date("+", "2", "days");
                break;
        
        }

        return $time;

    }
    
    /**
     * Get a start or end date in timestamp format for a specific target date.
     *
     * @param string $plus_minus Plus, minus or blank
     * @param int    $number     Integer number
     * @param string $units      Units of time
     *
     * @return timestamp
     */
    private function get_date($plus_minus = "", $number, $units) {
        $timestamp    = $this->get_timestamp($plus_minus, $number, $units);     // Figure out the timestamp
        $date_string  = $this->date->build_date("Ymd", $timestamp);             // Format the Ymd string
        $time         = $this->date->string_to_time($date_string, $timestamp);  // Figure out the timestamp
        
        return $time;
    }
    
    /**
     * Get a time stamp for a specific string input.
     */
    private function get_timestamp($plus_minus, $number, $units) {
        $target_date = $this->get_target_date($plus_minus, $number, $units);
        $time        = $this->date->string_to_time($target_date, $this->current_time);
        
        return $time;
    }
    
    /**
     * Format the target date in string format.
     */
    private function get_target_date($plus_minus, $number, $units) {
        return "{$plus_minus}{$number} {$units}";
    }
    
}