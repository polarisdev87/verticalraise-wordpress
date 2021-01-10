<?php

namespace classes\app\shorturls;

use classes\models\tables\Shortruls;

class Shorturl_Cron {

    private $table;

    public function __construct() {
        $this->table = new Shortruls();
    }

    public function run() {
        $this->process();
    }
    
    private function process() {
        // Target Date
        $target_date = $this->target_date();

        // Get list of fundraiser ids
        $fundraisers  = $this->table->get_all_f_ids();
        
        foreach ($fundraisers as $fundraiser) { 
            
            // Case 1: Fundraiser exists and has end date
            if ( get_post_status($fundraiser->fid) !== false ) {
                $end_date = get_post_meta($fundraiser->fid, 'end_date', true);
                if ( !empty($end_date) ) {
                    $end_date  = strtotime($end_date);
                    // If fundraiser ended 6+ months ago
                    if ($target_date >= $end_date) {
                        $this->table->delete($fundraiser->fid);
                    }
                }
            }
            
            // Case 2: Fundraiser was deleted/is missing
            else {
                $this->table->delete($fundraiser->fid);
            }
        }
    }
    
    private function target_date() {
        $six_months_ago = strtotime("today -6 months", current_time('timestamp', 0));
        $target_date  = date("Ymd", $six_months_ago);
        $target_date = strtotime($target_date);
        
        return $target_date;
    }

    

}
