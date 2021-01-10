<?php

/**
 * Create and get potential donor records
 */
class Potential_Donors
{
    
    private $wpdb;  // Wordpress Database Object
    private $type;  // Define the type of potential donator
    private $table; // Define which table to use
    
    /**
     * Class Constructor.
     * @param string $type Options: 'sms' or 'email'
     */
    public function __construct($type) {
        global $wpdb;
        
        $this->wpdb  = $wpdb;
        $this->type  = $type;
        $this->table = $this->get_table($this->type);
    }
    
    /**
     * Get the correct table based on the type.
     * @param string $type
     * @return string The table name
     */
    public function get_table($type) {
        switch($type) {
            case "sms":
                return "potential_donors_sms";
                break;
            case "email":
                return "potential_donors_email";
                break;
        }
    }

    /**
     * Create a potential donator record.
     *
     * @param int    $f_id Fundraiser ID
     * @param int    $uid  User ID
     * @param string $input The value string to store
     * @param int    $type The type of invite
     *
     * @return mixed bool on failure, record id on success
     */
    public function create($f_id = null, $uid = null, $input = null, $type = null) {
        
        // Validate the inputs
        if ( $this->validate($f_id, $uid, $input, $type) == false ) {
            return false;
        }

        $this->wpdb->show_errors(); 

        // Insert the record
        $inserted = $this->wpdb->insert( 
            $this->table, 
            array( 
                'f_id'       => $f_id,
                'u_id'       => $uid,
                'p_donator'  => $input,
                'type'       => $type
            ) 
        );
        
        // Return the results
        if ( $inserted != false ) {
            return $this->wpdb->insert_id;
        } else {
            return false;
        }
    }
        
    /**
     * Get records by user ID, fundraiser ID or both.
     * @param int $f_id Fundraiser ID
     * @param int $uid  User ID
     */
    public function get($f_id = null, $uid = null) {
        
        // Require atleast 1 arg
        if ( empty($f_id) && empty($uid) ) {
            return false;
        }
        
        // Build the WHERE clause
        $and = 0;
        $where = "";
        if ( !empty($f_id) ) {
            $where .= "`f_id` = '{$f_id}'";
            $and = 1;
        }
        if ( !empty($uid) ) {
            if ( $and == 1 ) {
                $where .= " AND ";
            }
            $where .= "`uid` = '{$uid}'";
        }

        $results = $this->wpdb->get_results( "SELECT * FROM `{$this->table}` WHERE {$where}", ARRAY_N);

        if ( !empty($results) ) {
            foreach ( $results as $result ) {
                $temp[] = $result[0];
            }
        } else {
            $temp = false;
        }

        return $temp;

    }
    
    /**
     * Validate the inputs.
     * @return bool
     */
    private function validate($f_id = null, $uid = null, $input = null) {
        // Check for values
        if ( empty($f_id) || empty($uid) || empty($input) ) {
            return false;
        }
        // Check value types
        if ( !is_int($f_id) || !is_int($uid) ) {
            return false;
        }
        // Check length
        if ( strlen((string)$f_id) > 9 || strlen((string)$uid) > 9 || strlen((string)$input) > 255 ) {
            return false;
        }
        
        return true;
    }

}