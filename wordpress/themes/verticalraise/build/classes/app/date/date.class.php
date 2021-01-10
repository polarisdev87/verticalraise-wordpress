<?php

namespace classes\app\date;

class Date
{

    public function get_current_timestamp() {
        return current_time('timestamp');
    }
    
    public function string_to_time($string = "", $timestamp = "") {
        return strtotime($string, $timestamp);
    }
    
    public function build_date($format = "", $timestamp = "") {
        return date($format, $timestamp);
    }
    
    public function build_current_date($format = null) {
        return current_time($format);
    }
    
    public function get_timestamp_difference($timestamp) {
        return $this->get_current_timestamp() - $timestamp;
    }
    
    public function get_num_days($difference) {
        return $difference / $this->get_seconds_in_day();
    }
    
    public function get_seconds_in_day() {
        return 60 * 60 * 24;
    }

}