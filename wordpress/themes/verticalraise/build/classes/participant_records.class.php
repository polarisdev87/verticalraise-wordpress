<?php

/**
 * Handles sharing records for a participant of a fundariser
 */

class Participant_Sharing_Totals
{

    /**
     * Inserts or updates participant's fundraiser record.
     * How many emails, texts, fb shares, twitter shares, flyer shares, etc.
     * Either update existing record, or insert a new record
     *
     * @param int    $fundraiser_id The fundraiser to update
     * @param int    $participant   The participant wp user id to update
     * @param string $type          The distribution method
     * @param int    $value         The value to increment or insert
     */
    public function adjust($fundraiser_id = null, $participant = null, $type = null, $value = null) {

        // Sanitize the variables
        $fundraiser_id = (int) trim($fundraiser_id);
        $participant   = (int) trim($participant);
        $type          = trim($type);
        $value         = (int) trim($value);

        // Validate our inputs
        if ( $this->validate($fundraiser_id, $participant, $type, $value) == false ) return false;

        // Look for existing participant row
        $myrow = $this -> get_single_row( $fundraiser_id, $participant );

        if ( !empty($myrow) ) {

            $this->update($myrow, $type, $value); // Update the row

        } else {

            $this->insert($fundraiser_id, $participant, $type, $value); // Insert new row

        }
    }

    /**
     * Get the single record of a fundraiser_id, participant pair.
     * @param  int   $fundraiser_id
     * @param  int   $participant
     * @return mixed
     */
    ## TODO USE PREPARE ##
    public function get_single_row($fundraiser_id, $participant) {
        global $wpdb;
        return $wpdb->get_row( "SELECT * FROM participant_fundraiser_details WHERE fundraiser = " . $fundraiser_id . " AND participant_id = " . $participant, OBJECT ); 
    }

    /**
     * Update the existing record, add the value to the total for the type.
     * @param  object $myrow The result row object
     * @param  string $type
     * @param  int    $value
     * @return void
     */
    private function update($myrow, $type, $value) {
        global $wpdb;
        $id = $myrow->id;
        
        switch ($type) {
            case "parents":
                $parents = $myrow->parents + $value;
                $wpdb->query("UPDATE participant_fundraiser_details SET parents = $parents WHERE id = $id");
                break;
            case "email":
                $email = $myrow->email + $value;
                $wpdb->query("UPDATE participant_fundraiser_details SET email = $email WHERE id = $id");
                break;
            case "twitter":
                $twitter = $myrow->twitter + $value;
                $wpdb->query("UPDATE participant_fundraiser_details SET twitter = $twitter WHERE id = $id");
                break;
            case "facebook":
                $facebook = $myrow->facebook + $value;
                $wpdb->query("UPDATE participant_fundraiser_details SET facebook = $facebook WHERE id = $id");
                break;
            case "sms":
                $sms = $myrow->sms + $value;
                $wpdb->query("UPDATE participant_fundraiser_details SET sms = $sms WHERE id = $id");
                break;
            case "text":
                $text = $myrow->text + $value;
                $wpdb->query("UPDATE participant_fundraiser_details SET text = $text WHERE id = $id");
                break;
            case "flyer":
                $flyer = $myrow->flyer + $value;
                $wpdb->query("UPDATE participant_fundraiser_details SET flyer = $flyer WHERE id = $id");
                break;
            case "supporters":
                $supporters = $myrow->supporters + $value;
                $wpdb->query("UPDATE participant_fundraiser_details SET supporters = $supporters WHERE id = $id");
                break;
            case "total":
                $total = $myrow->total + $value;
                $wpdb->query("UPDATE participant_fundraiser_details SET total = $total WHERE id = $id");
                break;
            default:
        }
    }
    
    /**
     * Create an initial record when a user joins as a participant for a fundraiser.
     * @param  int  $fundraiser_id
     * @param  int  $participant
     * @return bool If the record was inserted successfully or not
     */
    public function insert_initial($fundraiser_id, $participant) {
        global $wpdb;
        
        /**
         * Quick validation to make sure no erroneous records are inserted.
         */
        
        // is empty?
        if ( empty($fundraiser_id) || empty($participant) ) return false;
        
        // is fundraiser_id real?
        if ( get_post_status( $fundraiser_id ) == false ) return false;
        
        // is participant real?
        if ( get_userdata( $participant ) == false ) return false;
        
        $fundraiser_id = (int) $fundraiser_id;
        $participant = (int) $participant;
                
        /**
         * Check for the existance of the record we are trying to insert.
         */
        $myrow = $this->get_single_row($fundraiser_id, $participant);
        
        /**
         * If the record does not exist, then we will insert the new record.
         */
        if ( empty($myrow) ) {
            
            // Get the user object
            $user_info = get_userdata($participant);
        
            // Insert the record
            $insert = $wpdb->insert('participant_fundraiser_details',
                array(
                    'participant_name' => $user_info->display_name,
                    'participant_id' => $participant,
                    'fundraiser' => $fundraiser_id
                ),
                array('%s', '%d', '%d')
            );
            
            // Return the results
            if ( empty($insert) ) {
                return false;
            } else {
                return true;
            }
            
        }
        
    }
    
    ### TODO : Not sure if we need the insert function now? what was it used for in the past? ###
    
    /**
     * Insert a new record into the database
     *
     * @param  int    $fundraiser_id
     * @param  int    $participant
     * @param  string $type
     * @param  int    $value
     *
     * @return void
     */
    private function insert($fundraiser_id, $participant, $type, $value) {
        global $wpdb;
        $user_info = get_userdata($participant);

        switch ($type) {
            case "parents":
                $results = $wpdb->insert('participant_fundraiser_details',
                    array(
                        'participant_name' => $user_info->display_name,
                        'participant_id' => $participant,
                        'parents' => $value,
                        'fundraiser' => $fundraiser_id
                    ),
                    array('%s', '%d', '%d', '%d')
                );
                break;
            case "email":
                $results = $wpdb->insert('participant_fundraiser_details',
                    array(
                        'participant_name' => $user_info->display_name,
                        'participant_id' => $participant,
                        'email' => $value,
                        'fundraiser' => $fundraiser_id
                    ),
                    array('%s', '%d', '%d', '%d')
                );
                break;
            case "twitter":
                $results = $wpdb->insert('participant_fundraiser_details',
                    array(
                        'participant_name' => $user_info->display_name,
                        'participant_id' => $participant,
                        'twitter' => $value,
                        'fundraiser' => $fundraiser_id
                    ),
                    array('%s', '%d', '%d', '%d')
                );
                break;
            case "facebook":
                $results = $wpdb->insert('participant_fundraiser_details',
                    array(
                        'participant_name' => $user_info->display_name,
                        'participant_id' => $participant,
                        'facebook' => $value,
                        'fundraiser' => $fundraiser_id
                    ),
                    array('%s', '%d', '%d', '%d')
                );
                break;
            case "sms":
                $results = $wpdb->insert('participant_fundraiser_details',
                    array(
                        'participant_name' => $user_info->display_name,
                        'participant_id' => $participant,
                        'sms' => $value,
                        'fundraiser' => $fundraiser_id
                    ),
                    array('%s', '%d', '%d', '%d')
                );
                break;
            case "text":
                $results = $wpdb->insert('participant_fundraiser_details',
                    array(
                        'participant_name' => $user_info->display_name,
                        'participant_id' => $participant,
                        'text' => $value,
                        'fundraiser' => $fundraiser_id
                    ),
                    array('%s', '%d', '%d', '%d')
                );
                break;
            case "flyer":
                $results = $wpdb->insert('participant_fundraiser_details',
                    array(
                        'participant_name' => $user_info->display_name,
                        'participant_id' => $participant,
                        'flyer' => $value,
                        'fundraiser' => $fundraiser_id
                    ),
                    array('%s', '%d', '%d', '%d')
                );
                break;
            case "supporters":
                $results = $wpdb->insert('participant_fundraiser_details',
                    array(
                        'participant_name' => $user_info->display_name,
                        'participant_id' => $participant,
                        'supporters' => $value,
                        'fundraiser' => $fundraiser_id
                    ),
                    array('%s', '%d', '%d', '%d')
                );
                break;
            case "total":
                $results = $wpdb->insert('participant_fundraiser_details',
                    array(
                        'participant_name' => $user_info->display_name,
                        'participant_id' => $participant,
                        'total' => $value,
                        'fundraiser' => $fundraiser_id
                    ),
                    array('%s', '%d', '%d', '%d')
                );
                break;
            default:
        }
    }

    /**
     * Validate the incoming participant fundraiser details.
     * Since we are updating/inserting database records, it's important to validate each piece of data
     *
     * @param  int    $fundraiser_id The fundraiser to update
     * @param  int    $participant   The participant wp user id to update
     * @param  string $type          The distribution method
     * @param  int    $value         The value to increment or insert
     *
     * @return bool
     */
    private function validate( $fundraiser_id, $participant, $type, $value ) {
        // empty value validation
        if ( empty($fundraiser_id) ) return false;
        if ( empty($participant) ) return false;
        if ( empty($type) ) return false;
        if ( empty($value) ) return false;

        // positive integer validation
        if ( $this->is_positive_int($fundraiser_id) == false ) return false;
        if ( $this->is_positive_int($participant) == false ) return false;
        

        // allowed type validation
        $allowed = [ 'parents', 'email', 'twitter', 'facebook', 'text','sms', 'flyer', 'supporters', 'total' ];
        if ( !in_array($type, $allowed) ) return false;

        // is fundraiser_id real?
        if ( get_post_status( $fundraiser_id ) == false ) return false;

        // is participant real?
        if ( get_userdata( $participant ) == false ) return false;

        return true;
    }

    /**
     * Checks if a number is a positive integer.
     * @param  int  $number The number to check
     * @return bool
     */
    private function is_positive_int($number) {
        if ( is_int($number) == false ) return false;
        if ( ctype_digit((string) $number) == false ) return false;
        if ( $number < 0 ) return false;

        return true;
    }

}
?>