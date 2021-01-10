<?php

/**
 * This class generates a unique 10 digit join code
 */
class Join_Codes
{
    
    /**
     * Generate a unique join code.
     * @return 8 Digit code
     */
    public function generate_code() {
        $found_code = false;
        $max_tries  = 1000; // Max tries
        
        $x = 0;
        $unused_code = false;
        while ( $unused_code == false ) {
            
            if ( $x == $max_tries ) {
                return false;
            }
            
            // Generate a code
            $code = $this->get_random_code();

            if ( $this->code_exists($code) == false ) {
                $unused_code = true; // We found a code
            }
            
            $x++;
        }
        
        return $code;
    }
    
    /**
     * Generate random 8 digit code.
     * @return int 8 Digit code
     */
    private function get_random_code() {
        return mt_rand(10000000, 99999999);
    }
    
    /**
     * Check to see if code exists.
     * @param  int $code The code to check
     * @return bool
     */
    private function code_exists($code) {
        if ( $this->query_code($code) == true ) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Query the db for active code.
     * @param  int $code The code to check
     * @return bool
     */
    private function query_code($code) {
        
        $args  = array (
            'post_type'              => 'fundraiser',
            'posts_per_page'         => -1,
            'no_found_rows'          => true,
            'update_post_term_cache' => false,
            'fields'                 => 'ids',
            'meta_query'             => array (
                'relation' => 'OR',
                array (
                    'key'     => 'join_code',
                    'type'    => 'CHAR',
                    'value'   => $code,
                    'compare' => '=',
                ),
                array (
                    'key'     => 'join_code_sadmin',
                    'value'   => $code,
                    'type'    => 'CHAR',
                    'compare' => '='
                )
            )
        );
        add_filter('posts_clauses', 'wpse158898_posts_clauses', 10, 2);
        $posts = new WP_Query($args);
        remove_filter('posts_clauses', 'wpse158898_posts_clauses', 10);
        
        if ( $posts->have_posts() ) {          
            return true; // Code exists
        } else {           
            return false; // Code does not exist
        }
    }
    
}