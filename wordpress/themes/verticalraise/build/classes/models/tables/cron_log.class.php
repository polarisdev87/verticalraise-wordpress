<?php

namespace classes\models\tables;

class Cron_Log
{
    
    private $table_name = 'cron_log';

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb; 
    }
    
    public function check_existing($period, $started) {

        if ( $wpdb->get_row( "SELECT * FROM `{$this->table_name}` WHERE `type` = '{$period}_days' AND `started` >= '{$today}' LIMIT 1", ARRAY_N ) == null) {
            // If none, insert the record

        } else {
            update_option('cron_' . $period, 'idle');
            return 'Cron already ran today';
        }

    }
    
    public function insert($period, $insert_time) {
        $this->wpdb->insert( 
            $this->table_name, 
            array( 
                'type' => $period . '_days', 
                'started' => $insert_time, 
            ) 
        );
        
        $insert_id = $this->wpdb->insert_id;
            
        return $insert_id;
        
    }
    
    public function get_cronlog_by_date ($date) {      
        $result = $this->wpdb->get_results("SELECT * FROM {$this->table_name} WHERE DATE(started) = '{$date}'", OBJECT);
        return $result;
    }
    
    public function get_cronlog_by_range ($from, $to) {
        $result = $this->wpdb->get_results("SELECT * FROM {$this->table_name} WHERE DATE(started) >= '{$from}' AND DATE(started) <= '{$to}'", OBJECT);
        return $result;
    }

}
