<?php

/**
 * Crons
 */
class Cron
{
    
    public function __construct( $type = null, $db_key = null ) {
        $this->table       = "cron_log";
        $this->type        = $type;
        $this->db_key      = $db_key;
        $this->today       = current_time("Y-m-d 00:00:00");
        $this->insert_time = current_time( "mysql" );
    }

    /**
     * Create the output file.
     * @param string $type The cron type
     * @return bool
     */
    public function create_output_file() {  

        // Set file name
        $file_name = $this->get_file_name($this->type);

        // Set file path
        $full_file_path = $this->get_file_path($file_name);

        // Create file
        $this->create_file($full_file_path);
        
        return $full_file_path;
    }
    
    /**
     * Create the file if it does not exist.
     * @param string $path The file path
     * @return bool
     */
    private function get_file_name($type) {
        return date("m-d-Y") . "_{$type}.json";
    }
    
    /**
     * Create the file if it does not exist.
     * @param string $path The file path
     * @return bool
     */
    private function get_file_path($file_name) {
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['basedir'];
        
        return $upload_path . '/cron_log/' . $file_name;
    }
    
    /**
     * Create the file if it does not exist.
     * @param string $path The file path
     * @return bool
     */
    private function create_file($path) {
        
        if ( file_exists($path) == false ) {
            $file = fopen($path, "w");
            $file = fclose($file);
        
            return true;
        } else {
            return false;
        }
    }
    
   /**
    * Create database entry to log cron execution.
    * @return mixed
    */
    public function create_db_entry() {
        global $wpdb;
        
        // Check for existing record
        if ( $this->check_record_exists() == null ) {
            
            // If none, insert the record
            $record_id = $this->insert_record();
            
            if ( $record_id == false) {
                return false;
            } else {
                return $record_id;
            }

        } else {
            return false;
        }
    }
    
    /**
     * Check if a specific record exists.
     */
    private function check_record_exists() {
        global $wpdb;
        
        $wpdb->show_errors(); 
        
        return $wpdb->get_row( "SELECT * FROM `{$this->table}` WHERE `type` = '{$this->db_key}' AND `started` >= '{$this->today}' LIMIT 1", ARRAY_N );
    }
    
    /**
     * Insert a cron log record.
     */
    private function insert_record() {
        global $wpdb;
        
        $wpdb->show_errors(); 

        $inserted = $wpdb->insert( 
            $this->table, 
            array( 
                'type' => $this->db_key, 
                'started' => $this->insert_time, 
            ) 
        );
        
        if ( $inserted != false ) {
            return $wpdb->insert_id;
        } else {
            return false;
        }
    }
    
    /**
     * Update the cron log record.
     * @param int $record_id
     * @return bool
     */
    public function update_record($record_id) {
        global $wpdb;
        
        return $wpdb->update( $this->table, array('started' => $this->insert_time, 'ended' => current_time( 'mysql' ) ), array('id' => $record_id), array('%s'));
    }
    
    
    /**
     * Log output to file.
     * @param string $full_file_path
     * @return mixed False on falure, bytes on success
     */
    public function log_output( $full_file_path, $output ) {
        return file_put_contents($full_file_path, json_encode($output));
    }

}