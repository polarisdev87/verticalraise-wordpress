<?php

namespace classes\models\tables;

/**
 * Retreive Donation Comments
 */
class Donation_Comments{
    
    /**
     * Class variables.
     */
    private $table_name = "donation_comments"; // Table name
    private $wpdb;                             // Wordpress Database Object
    
    /**
     * Class constructor.
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    
    /**
     * Get all payments for a specific fundraiser id.
     * @param  int   $fundraiser_id 
     * @return float $total
     */
    public function get_by_fundraiser_id($fundraiser_id) {
            
        $results = $this->wpdb->get_results( $this->wpdb->prepare(  
            "
                SELECT * FROM `{$this->table_name}` WHERE f_id = '%d' ORDER BY `id` DESC
            ",
            $fundraiser_id 
        ), ARRAY_A );
        
        $comments = array();
        
        foreach ( $results as $result ) {
            $comments[$result['d_id']] = $result;
        }
            
        if ( !empty($comments) ) {
            return $comments;
        } else {
            return false;
        }
    }
    
    /**
     * Insert the record.
     */
    public function insert($data) {        
        $insert = $this->wpdb->insert($this->table_name,
            array(
                'd_id' => $data->d_id,
                'f_id' => $data->f_id,
                'comment' => $data->comment,
                'avatar_url' => $data->avatar_url
            ),
            array('%d', '%d', '%s', '%s')
        );

        // Return the results
        if ( empty($insert) ) {
            return 'error';
        } else {
            return 'inserted';
        }
    }
    
}