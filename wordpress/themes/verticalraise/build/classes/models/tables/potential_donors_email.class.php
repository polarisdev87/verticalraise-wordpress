<?php

/**
 * Get and set previously sent records for a specific user.
 */
namespace classes\models\tables;

class Potential_Donors_Email
{
    private $wpdb;
    private $table_name = "potential_donors_email";
    
    /**
     * Class Constructor.
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    
    /**
     * Insert an email into the potential donors table.
     *
     * @param string $email
     * @param int    $uid
     * @param int    $fid
     * @param int    $type
     *
     * @return string error|inserted
     */
    public function insert($email, $uid, $f_id, $type) {
        $insert = $this->wpdb->insert($this->table_name,
            array(
                'p_donator' => $email,
                'f_id'  => $f_id,
                'u_id'  => $uid,
                'type'  => $type
            ),
            array('%s', '%d', '%d', '%d')
        );

        // Return the results
        if ( empty($insert) ) {
            return 'error';
        } else {
            return 'inserted';
        }
    }

    /**
     * Get the list of potential donors for this fundraiser id.
     * @param  int $fundraiser_id 
     * @return mix Array of potential donors or false.
     */
    public function get_by_fid($fid, $uid) {
        $results = $this->wpdb->get_results( "SELECT `p_donator` FROM `{$this->table_name}` WHERE `f_id` = '{$fid}' AND `u_id` = '{$uid}'", ARRAY_N);
        
        foreach ( $results as $result ) {
            $potential_donors[] = $result[0];
        }
            
        if ( !empty($potential_donors) ) {
            return $potential_donors;
        } else {
            return false;
        }
    }
    
    /**
     * Get the list of potential donors for this fundraiser id.
     * @param  int $fundraiser_id 
     * @return mix Array of potential donors or false.
     */
    public function get_row_by_fid($fid) {
        $results = $this->wpdb->get_results( "SELECT * FROM `{$this->table_name}` WHERE `f_id` = '{$fid}'", ARRAY_A);
        
        foreach ( $results as $result ) {
            $potential_donors[] = $result;
        }
            
        if ( !empty($potential_donors) ) {
            return $potential_donors;
        } else {
            return false;
        }
    }
    
    /**
     * Get the potential donor emails by email, uid and fid.
     *
     */
    public function get_by_email_uid_fid($email, $uid, $fid) {
        $results = $this->wpdb->get_row( "SELECT `p_donator` FROM `{$this->table_name}` WHERE `p_donator` = '{$email}' AND `f_id` = '{$fid}' AND `u_id` = '{$uid}' LIMIT 1", ARRAY_N);
            
        if ( !empty($results) ) {
            return $results;
        } else {
            return false;
        }
        
    }
    
    public function update() {
    
    }
    
}