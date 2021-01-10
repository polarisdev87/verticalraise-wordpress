<?php

/**
 * SET and GET Goals Cache Layer
 */
class Goals
{
    
    public function __construct() {
    }

    /**
     * TODO
     */
    public function set_amount($fundraiser_id) {
        // wp_cache_delete( $key, $group );
    }
    
    /**
     * TODO
     */
    public function set_goal($fundraiser_id, $amount, $original) {
        // wp_cache_delete( $key, $group );
        // update post meta
        //update_post_meta($fundraiser_id, 'fundraising_goal', $amount, $original);
        // flush cache
        // set cache
    }

    /**
     * TODO
     */
    public function set_donators($fundraiser_id) {
        // wp_cache_delete( $key, $group );
        // set transient

        // return false or true
    }
    
    /**
     * Refactor the goal if it has been surpassed.
     * @param int $f_id
     */
    public function refactor_goal($f_id) {
        
        /*$amount = $this->get_amount($f_id);
        $original_goal = $this->get_goal($f_id);
        $new_goal = $original_goal;
        
        while ( $amount >= $new_goal ) {
            $new_goal = $new_goal + 1000;
        }
        
        $this->set_goal($f_id, $new_goal, $original_goal);*/
        
    }
    
    /**
     * Get the amount raised for a specific fundraiser.
     * @param int $f_id Fundraiser id
     * @return int The amount
     */
    public function get_amount($f_id) {
        global $wpdb;
        
        // Get the cache record
        $result = wp_cache_get( 'get_amount_' . $f_id );
        
        if ( false === $result ) {
            
            // Load the Payments class
            load_class('payment_records.class.php');
            
            $payment = new Payment_Records();
            $amount = $payment->get_total_by_fundraiser_id($f_id);

            // If no payments exist
            if ( $amount == null || $amount == '' ) {
                $amount = 0;
            }
            
            // Set the cache record
            //wp_cache_set( 'get_amount_' . $f_id, $amount );
            
            return $amount;
        } else {
            return $result;
        }
    }

    /**
     * Get a list of the donators and their donations for a specific fundraiser id.
     * @param int $f_id Fundraiser id
     * @return array The donators and their donations
     */
    public function get_donators($f_id) {
        global $wpdb;

        // Retrieve the donators and their donations from cache
        $results = get_transient( 'get_donators_' . $f_id );

        if ( false === $results ) {  
            
            // Load the Payments class
            load_class('payment_records.class.php');
            $payments = new Payment_Records();

            $donators = $payments->get_all_payments_by_fundraiser_id($f_id);

            set_transient( 'get_donators_' . $f_id, $donators , 3600 * 24 );
            return $donators;
        } else {
            return $results;
        }
    }

    /**
     * Get the goal for a specific fundraiser id.
     * @param int $fundraiser_id
     * @return float The goal
     */
    public function get_goal($f_id) {

        // Retrieve the goal from cache
        $result = wp_cache_get( 'get_goal_' . $f_id );
        
        if ( false === $result ) {
            // If the goal does not exist, get it from post_meta and store it in the cache
            $result = get_post_meta($f_id, 'fundraising_goal');
            $result = $result[0];
            //wp_cache_set( 'get_goal_' . $fundraiser_id, $result );
            
            return $result;
        } else {
            return $result;
        }
        
    }
    
    /**
     * Get the number of supporters (donators).
     * @param int $fundraiser_id 
     * @return int Number of supporters
     */
    public function get_num_supporters($f_id) {
        
        // Retrieve the goal from cache
        $result = wp_cache_get( 'get_num_supporters_' . $f_id );
        
        if ( false === $result ) {
            
            // Load the payments class
            load_class('payment_records.class.php');
            
            $payments = new Payment_Records();
            $total = $payments->get_number_supporters_by_fundraiser_id($f_id);
            
            if ( $total == null || $total == '' ) {
                $total = 0;
            }
            
            //wp_cache_set( 'get_num_supporters_' . $fundraiser_id, $result );
            
            return $total;
        } else {
            return $result;
        }
        
    }
    
    /**
     * Get the fundraiser end date.
     * @param int $fundraiser_id 
     * @return datetime
     */
    public function get_enddate($f_id) {
        return get_post_meta($f_id, 'end_date', true);
    }


}