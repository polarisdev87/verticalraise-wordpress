<?php

namespace classes\models\tables;

/**
 * Handles setting and getting secondary admins for fundraisers
 */
class Secondary_Admins{
    
    private $table_name = "fundraiser_sadmin";
    private $wpdb;

    /**
     * Class Constructor.
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    
    /**
     * Retreive all the secondary admin ids attached to fundraiser id.
     * @param  int   $f_id
     * @return mixed Object of results or false
     */
    public function get_sadmin_ids_by_fid($f_id) {
        if ( !empty( $f_id ) ) {
            $results = $this->wpdb->get_results( "SELECT `u_id` FROM `{$this->table_name}` WHERE `f_id` = '{$f_id}'", ARRAY_N);
            
            if ( !empty($results) ) {
            
                foreach ( $results as $result ) {
                    $temp[] = $result[0];
                }
                
            } else {
                $temp = false;
            }
            
            return $temp;
        }
    
    }
    
    /**
     * Retreive all the secondary admin ids attached to user id.
     * @param  int   $uid
     * @return mixed Object of results or false
     */
    public function get_fids_by_sadmin_id($u_id) {
        if ( !empty( $u_id ) ) {
            $results = $this->wpdb->get_results( "SELECT `f_id` FROM `{$this->table_name}` WHERE `u_id` = '{$u_id}'", ARRAY_N);
            
            if ( !empty($results) ) {
            
                foreach ( $results as $result ) {
                    $temp[] = $result[0];
                }
                
            } else {
                $temp = array();
            }
            
            return $temp;
        }
    
    }
    
    /**
     * Insert a secondary admin user id into `fundraiser_sadmin` table.
     * @param  int $f_id  The fundraiser ID
     * @param  int $f_uid The user ID
     * @return void
     */
    public function store_secondary_admin($f_id, $u_id) {
        if ( !is_int($f_id) ) return false;
        if ( !is_int($u_id) ) return false;

        if ( $this->wpdb->get_row( "SELECT * FROM `{$this->table_name}` WHERE ( `f_id` = '{$f_id}' AND `u_id` = '{$u_id}' ) LIMIT 1", ARRAY_N ) == null) {
            $this->wpdb->insert( 
                $this->table_name, 
                array( 
                    'f_id' => $f_id, 
                    'u_id' => $u_id, 
                ) 
            );
        }   
    }
    
    /**
     * Check to see if a specific user ID is a secondary admin for a specific fundraiser ID.
     * @param  int $f_id The fundraiser iD
     * @param  int $u_id The user ID
     * @return bool
     */
    public function is_sadmin($f_id, $u_id) {
        if ( !empty( $f_id ) ) {
            $results = $this->wpdb->get_var( "SELECT `u_id` FROM `{$this->table_name}` WHERE `f_id` = '{$f_id}' AND `u_id` = '{$u_id}' LIMIT 1");
            
            if ( !empty($results) ) {
                return true;
            } else {
                return false;
            }
        }
    }

}