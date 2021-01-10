<?php

namespace classes\app\sidebar;

use \classes\app\date\date as Date;

class Sidebar
{
    function __construct() {
        $this->date = new Date();
    }

    public function days_ago($donation_date) {
        
        $current_timestamp  = $this->date->get_current_timestamp();                                           
        $donation_timestamp = $this->date->string_to_time($donation_date, $current_timestamp);
        $difference         = $this->date->get_timestamp_difference($donation_timestamp);
        $num_days           = $this->date->get_num_days($difference);

        if ( $num_days < 1 ) {
            return "Today";
        } else if ( $num_days >= 1 && $num_days < 2 ) {
            return "Yesterday";
        } else {
            return round($num_days) . " days ago";
        }
    }
    
    public function donation_date($date_time) {
        return explode(" ", $date_time)[0];
    }
    
    public function donator_name($name, $anonymous) {
        if ( $anonymous != 1 ) { 
            return $name; 
        } else {
            return "Anonymous";
        }
    }
    
    public function format_donation_amount($amount) {
        $currency = $this->get_currency();
        $formatted = "{$currency}{$amount}";
        
        return $formatted;
    }
    
    public function get_currency() {
        return "$";
    }
    
}