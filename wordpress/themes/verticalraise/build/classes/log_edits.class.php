<?php

class Log_Edits
{

    public function __construct() {
        $this->table = 'edit_log';
    }
        
    /**
     * Insert a log entry.
     * @param int $f_id
     * @param int $user_ID
     * @param string $edit_type
     * @param string $new_value
     * @param string $old_value
     * @return mixed Record id or false
     */
    public function log($f_id = null, $user_ID = null, $edit_type = null, $new_value = null, $old_value = null) {
        global $wpdb;
        
        if ( $this->validate($f_id, $user_ID, $edit_type, $new_value, $old_value) == true ) {

            $inserted = $wpdb->insert( 
                $this->table, 
                array( 
                    'f_id' => esc_sql((int) $f_id),
                    'u_id' => esc_sql((int) $user_ID), 
                    'edit_type' => esc_sql($edit_type),
                    'new_value' => esc_sql($new_value), 
                    'old_value' => esc_sql($old_value),
                    'date' => current_time('mysql')
                ) 
            );

            if ( $inserted != false ) {
                return $wpdb->insert_id;
            } else {
                return false;
            }
        }
        
    }
    
    /**
     * Validate the inputs for a log entry.
     * @param int $f_id
     * @param int $user_ID
     * @param string $edit_type
     * @param string $new_value
     * @param string $old_value
     * @return bool
     */
    private function validate($f_id = null, $user_ID = null, $edit_type = null, $new_value = null, $old_value = null) {
        if ( empty($f_id) || !is_int((int) $f_id) ) {
            return false;
        }
        if ( empty($user_ID) || !is_int((int) $user_ID) ) {
            return false;
        }
        if ( empty($edit_type) ) {
            return false;
        }
        if ( empty($new_value) ) {
            return false;
        }
        if ( empty($old_value) ) {
            return false;
        }
        
        return true;
    }
}