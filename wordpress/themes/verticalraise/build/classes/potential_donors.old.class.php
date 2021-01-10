<?php

class Potential_Donors
{
    
    /**
     * Get the list of potential donors for this fundraiser id.
     * @param  int    $fundraiser_id 
     * @param  string $type ie. 'potential_donors_array'
     * @return mixed  Array of potential donors or false.
     */
    public function get_potential_donors($fundraiser_id, $type) {
        
        // Check for ID
        if ( empty($fundraiser_id) ) {
            return 'missing fundraiser id';
        }
        
        // Check for type
        if ( empty($type) ) {
            return 'missing type';
        }
        
        $potential_donors = json_decode(get_post_meta($fundraiser_id, $type, true));
        if ( !empty($potential_donors) ) {
            return $potential_donors;
        } else {
            return false;
        }
    }
    
    /**
     * Update potential donors
     * @param int $f_id
     * @param int $user_ID
     * @param int $data    The data to store for the user
     */
    private function update_potential_donors($fundraisr_id, $user_ID, $data, $type) {
        
        // Check for ID
        if ( empty($fundraiser_id) ) {
            return 'missing fundraiser id';
        }
        
        // Check for type
        if ( empty($type) ) {
            return 'missing type';
        }
        
        $potential_donors = $this->get_potential_donors($fundraiser_id, $type);

        if ( !empty($potential_donors) ) {
            $potential_donors[] = array($user_ID, $data);
        } else {
            $potential_donors[] = array($user_ID, $data);
        }
        update_post_meta($fundraiser_id, $type, json_encode($potential_donors));
        
    }

}