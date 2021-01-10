<?php

namespace classes\models\tables;

/**
 * Handles sharing records for a participant of a fundariser
 */
class Participant_Fundraiser_Details
{

    // Class Variables
    private $table_name = "participant_fundraiser_details";
    private $wpdb;

    /**
     * Class Constructor.
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

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
    public function adjust( $fundraiser_id = null, $participant = null, $type = null, $value = null ) {

        // Sanitize the variables
        $fundraiser_id = (int) trim( $fundraiser_id );
        $participant   = (int) trim( $participant );
        $type          = trim( $type );
        $value         = (int) trim( $value );

        // Validate our inputs
        if ( $this->validate( $fundraiser_id, $participant, $type, $value ) == false )
            return false;

        // Look for existing participant row
        $myrow = $this->get_single_row( $fundraiser_id, $participant );

        if ( !empty( $myrow ) ) {

            $this->update( $myrow, $type, $value ); // Update the row
        } else {

            $this->insert( $fundraiser_id, $participant, $type, $value ); // Insert new row
        }
    }

    /**
     * Get the single record of a fundraiser_id, participant pair.
     * @param  int   $fundraiser_id
     * @param  int   $participant
     * @return mixed
     */
    ## TODO USE PREPARE ##
    public function get_single_row( $fundraiser_id, $participant ) {
        return $this->wpdb->get_row( "SELECT * FROM {$this->table_name} WHERE fundraiser = " . $fundraiser_id . " AND participant_id = " . $participant, OBJECT );
    }

    /**
     * Get the results of a participant.
     * @param  int   $participant
     * @return mixed
     */
    ## TODO USE PREPARE ##
    public function get_participant_results( $participant ) {
        return $this->wpdb->get_results( "SELECT * FROM {$this->table_name} WHERE participant_id = " . $participant ." LIMIT 1", OBJECT );
    }

    /**
     * Get the participants count count with 20 + emails by fundraiser id.
     * @param type $fundraiser_id
     * @return type
     */
    public function get_participant_by_fid_emailcount( $fundraiser_id ) {
        $total_emails = $this->wpdb->get_var( $this->wpdb->prepare(
                        "SELECT count(*) FROM {$this->table_name} WHERE fundraiser = '%d'and email >= 20 LIMIT 500", $fundraiser_id
                ) );

        if ( $total_emails == null )
            return 0;
        return $total_emails;
    }

    /**
     * Get the total email count by fundraiser id.
     * @param type $fundraiser_id
     * @return type
     */
    public function get_total_emails_by_fid( $fundraiser_id ) {
        $total_emails = $this->wpdb->get_var( $this->wpdb->prepare(
                        "SELECT SUM(email) FROM {$this->table_name} WHERE fundraiser = '%d' GROUP BY fundraiser", $fundraiser_id
                ) );
        if ( $total_emails == null )
            return 0;
        return $total_emails;
    }

    public function get_donor_participants_by_fid( $fundraiser_id ) {
        $donor_participants = $this->wpdb->get_results( $this->wpdb->prepare(
                        "SELECT participant_id, participant_name, SUM(total) AS total FROM {$this->table_name} WHERE fundraiser = '%d' GROUP BY participant_id", $fundraiser_id
                ), ARRAY_A );

        if ( count( $donor_participants ) == 0 || empty( $donor_participants ) || $donor_participants == null || $donor_participants == false ) {
            return 0;
        } else {
            $temp = array();
            foreach ( $donor_participants as $result ) {
                if ( $result['total'] != 0 )
                    $temp[] = $result['participant_id'];
            }

            $remove = $this->get_remove_user_ids( $fundraiser_id );

            if ( !empty( $remove ) && count( $remove ) >= 1 ) {
                foreach ( $temp as $key => $t ) {
                    if ( in_array( $t, $remove ) ) {
                        unset( $temp[$key] );
                    }
                }
            }
            if ( !empty( $temp ) ) {
                return count( $temp );
            } else {
                return 0;
            }
        }
    }

    public function get_donor_2_participants_by_fid( $fundraiser_id ) {
        $donor_participants = $this->wpdb->get_results( $this->wpdb->prepare(
                        "SELECT participant_id, participant_name, supporters FROM {$this->table_name} WHERE fundraiser = '%d' AND total > 0 GROUP BY participant_id", $fundraiser_id
                ), ARRAY_A );


        if ( count( $donor_participants ) == 0 || empty( $donor_participants ) || $donor_participants == null || $donor_participants == false ) {
            return 0;
        } else {
            $temp = array();
            foreach ( $donor_participants as $result ) {
                if ( $result['supporters'] >= 2 )
                    $temp[] = $result['participant_id'];
            }


            $remove = $this->get_remove_user_ids( $fundraiser_id );

            if ( !empty( $remove ) && count( $remove ) >= 1 ) {
                foreach ( $temp as $key => $t ) {
                    if ( in_array( $t, $remove ) ) {
                        unset( $temp[$key] );
                    }
                }
            }


            if ( !empty( $temp ) ) {
                return count( $temp );
            } else {
                return 0;
            }
        }
    }

    public function get_remove_user_ids( $f_id = null ) {

        // Load the secondary_adins class
        load_class( 'secondary_admins.class.php' );
        $sadmins = new Secondary_Admins();

        $remove = false;

        // Get secondary admins
        $check = $sadmins->get_sadmin_ids_by_fid( $f_id );

        if ( is_array( $check ) == true ) {
            foreach ( $check as $ch ) {
                $remove[] = $ch;
            }
        }

        // Get the post author
        $remove[] = get_post_field( 'post_author', $f_id );

        return $remove;
    }

    /**
     * Update participant name.
     * @param  int   $participant        The participant ID
     * @param  String $participant_name  The Participant Name
     * @return mixed
     */
    ## TODO USE PREPARE ##
    public function update_participant_name( $participant_name, $participant ) {
        $this->wpdb->query( "UPDATE $this->table_name SET `participant_name` = '$participant_name'  WHERE `participant_id` =  $participant " );
    }

    /**
     * Update the existing record, add the value to the total for the type.
     * @param  object $myrow The result row object
     * @param  string $type
     * @param  int    $value
     * @return void
     */
    private function update( $myrow, $type, $value ) {
        $id = $myrow->id;

        switch ( $type ) {
            case "parents":
                $parents    = $myrow->parents + $value;
                $this->wpdb->query( "UPDATE `{$this->table_name}` SET `parents` = $parents WHERE `id` = $id" );
                break;
            case "email":
                $email      = $myrow->email + $value;
                $this->wpdb->query( "UPDATE `{$this->table_name}` SET `email` = $email WHERE `id` = $id" );
                break;
            case "twitter":
                $twitter    = $myrow->twitter + $value;
                $this->wpdb->query( "UPDATE `{$this->table_name}` SET `twitter` = $twitter WHERE `id` = $id" );
                break;
            case "facebook":
                $facebook   = $myrow->facebook + $value;
                $this->wpdb->query( "UPDATE `{$this->table_name}` SET `facebook` = $facebook WHERE `id` = $id" );
                break;
            case "sms":
                $sms        = $myrow->sms + $value;
                $this->wpdb->query( "UPDATE `{$this->table_name}` SET `sms` = $sms WHERE `id` = $id" );
                break;
            case "flyer":
                $flyer      = $myrow->flyer + $value;
                $this->wpdb->query( "UPDATE `{$this->table_name}` SET `flyer` = $flyer WHERE `id` = $id" );
                break;
            case "supporters":
                $supporters = $myrow->supporters + $value;
                $this->wpdb->query( "UPDATE `{$this->table_name}` SET `supporters` = $supporters WHERE `id` = $id" );
                break;
            case "total":
                $total      = $myrow->total + $value;
                $this->wpdb->query( "UPDATE `{$this->table_name}` SET `total` = $total WHERE `id` = $id" );
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
    public function insert_initial( $fundraiser_id, $participant ) {
        /**
         * Quick validation to make sure no erroneous records are inserted.
         */
        // is empty?
        if ( empty( $fundraiser_id ) || empty( $participant ) )
            return false;

        // is fundraiser_id real?
        if ( get_post_status( $fundraiser_id ) == false )
            return false;

        // is participant real?
        if ( get_userdata( $participant ) == false )
            return false;

        $fundraiser_id = (int) $fundraiser_id;
        $participant   = (int) $participant;

        /**
         * Check for the existance of the record we are trying to insert.
         */
        $myrow = $this->get_single_row( $fundraiser_id, $participant );

        /**
         * If the record does not exist, then we will insert the new record.
         */
        if ( empty( $myrow ) ) {

            // Get the user object
            $user_info = get_userdata( $participant );

            // Insert the record
            $insert = $this->wpdb->insert( $this->table_name, array(
                'participant_name' => $user_info->display_name,
                'participant_id'   => $participant,
                'fundraiser'       => $fundraiser_id
                    ), array( '%s', '%d', '%d' )
            );

            // Return the results
            if ( empty( $insert ) ) {
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
    private function insert( $fundraiser_id, $participant, $type, $value ) {
        $user_info = get_userdata( $participant );

        switch ( $type ) {
            case "parents":
                $results = $this->wpdb->insert( $this->table_name, array(
                    'participant_name' => $user_info->display_name,
                    'participant_id'   => $participant,
                    'parents'          => $value,
                    'fundraiser'       => $fundraiser_id
                        ), array( '%s', '%d', '%d', '%d' )
                );
                break;
            case "email":
                $results = $this->wpdb->insert( $this->table_name, array(
                    'participant_name' => $user_info->display_name,
                    'participant_id'   => $participant,
                    'email'            => $value,
                    'fundraiser'       => $fundraiser_id
                        ), array( '%s', '%d', '%d', '%d' )
                );
                break;
            case "twitter":
                $results = $this->wpdb->insert( $this->table_name, array(
                    'participant_name' => $user_info->display_name,
                    'participant_id'   => $participant,
                    'twitter'          => $value,
                    'fundraiser'       => $fundraiser_id
                        ), array( '%s', '%d', '%d', '%d' )
                );
                break;
            case "facebook":
                $results = $this->wpdb->insert( $this->table_name, array(
                    'participant_name' => $user_info->display_name,
                    'participant_id'   => $participant,
                    'facebook'         => $value,
                    'fundraiser'       => $fundraiser_id
                        ), array( '%s', '%d', '%d', '%d' )
                );
                break;
            case "sms":
                $results = $this->wpdb->insert( $this->table_name, array(
                    'participant_name' => $user_info->display_name,
                    'participant_id'   => $participant,
                    'sms'              => $value,
                    'fundraiser'       => $fundraiser_id
                        ), array( '%s', '%d', '%d', '%d' )
                );
                break;
            case "flyer":
                $results = $this->wpdb->insert( $this->table_name, array(
                    'participant_name' => $user_info->display_name,
                    'participant_id'   => $participant,
                    'flyer'            => $value,
                    'fundraiser'       => $fundraiser_id
                        ), array( '%s', '%d', '%d', '%d' )
                );
                break;
            case "supporters":
                $results = $this->wpdb->insert( $this->table_name, array(
                    'participant_name' => $user_info->display_name,
                    'participant_id'   => $participant,
                    'supporters'       => $value,
                    'fundraiser'       => $fundraiser_id
                        ), array( '%s', '%d', '%d', '%d' )
                );
                break;
            case "total":
                $results = $this->wpdb->insert( $this->table_name, array(
                    'participant_name' => $user_info->display_name,
                    'participant_id'   => $participant,
                    'total'            => $value,
                    'fundraiser'       => $fundraiser_id
                        ), array( '%s', '%d', '%d', '%d' )
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
        if ( empty( $fundraiser_id ) )
            return false;
        if ( empty( $participant ) )
            return false;
        if ( empty( $type ) )
            return false;
        if ( empty( $value ) )
            return false;

        // positive integer validation
        if ( $this->is_positive_int( $fundraiser_id ) == false )
            return false;
        if ( $this->is_positive_int( $participant ) == false )
            return false;
        if ( $this->is_positive_int( $value ) == false )
            return false;

        // allowed type validation
        $allowed = [ 'parents', 'email', 'twitter', 'facebook', 'sms', 'flyer', 'supporters', 'total' ];
        if ( !in_array( $type, $allowed ) )
            return false;

        // is fundraiser_id real?
        if ( get_post_status( $fundraiser_id ) == false )
            return false;

        // is participant real?
        if ( get_userdata( $participant ) == false )
            return false;

        return true;
    }

    /**
     * Checks if a number is a positive integer.
     * @param  int  $number The number to check
     * @return bool
     */
    private function is_positive_int( $number ) {
        if ( is_int( $number ) == false )
            return false;
        if ( ctype_digit( (string) $number ) == false )
            return false;
        if ( $number < 0 )
            return false;

        return true;
    }


	/**
	 * @param $u_id
	 * @param $f_id
	 *
	 * @return false|int
	 */
	public function delete_participant_fundraiser_details( $u_id, $f_id ) {
		return $this->wpdb->delete( $this->table_name, array( 'participant_id' => $u_id, 'fundraiser' => $f_id ), array("%d", "%d") );
    }


	/**
	 * @param $f_id
	 *
	 * @return array|null|object
	 */
	public function get_participants( $f_id ) {
		return $this->wpdb->get_results( $this->wpdb->prepare(
			"
                SELECT participant_name, participant_id FROM `{$this->table_name}` WHERE fundraiser = '%d'
            ",
			$f_id
		) );
	}

}
