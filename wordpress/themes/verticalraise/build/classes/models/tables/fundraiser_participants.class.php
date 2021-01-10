<?php

namespace classes\models\tables;

/**
 * Get fundraiser participation records by either user id or fundraiser id
 */
class Fundraiser_Participants
{

    // Class variables
    private $table_name = "fundraiser_participants";  // Table name
    private $wpdb;

    /**
     * Class constructor.
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    /**
     * Retreive all fundraiser ids attached to a user.
     * @param  int   $uid
     * @return mixed object of results or false
     */
    public function get_fundraiser_ids_by_userid( $uid ) {
        // Validation
        $validation = $this->validate($uid, 'uid');
        if ( $validation != true ) {
            return $validation;
        }

        // Query the database
        $results = $this->wpdb->get_results("SELECT `f_id` FROM `{$this->table_name}` WHERE `u_id` = '{$uid}'", ARRAY_N);

        foreach ( $results as $result ) {
            $temp[] = $result[0];
        }

        return ( isset($temp) ) ? $temp : "";
    }

    /**
     * Retreive ONLY the participant user ids attached to a fundraiser (no admin, post author, secondary admins).
     * @param  int   $uid
     * @return mixed object of results or false
     */
    public function get_filtered_participant_ids_by_fid( $f_id ) {
        // Validation
        $validation = $this->validate($f_id, 'f_id');
        if ( $validation != true ) {
            return $validation;
        }

        // Query the database
        $results = $this->wpdb->get_results("SELECT `u_id` FROM `{$this->table_name}` WHERE `f_id` = '{$f_id}'", ARRAY_N);

        if ( $results != false ) {

            foreach ( $results as $result ) {
                $temp[] = $result[0];
            }

            // Grab the list of admins, secondary admins & post author.
            $remove = $this->get_remove_user_ids($f_id);

            // Remove secondary admins, post author and admins.
            if ( !empty($remove) && count($remove) >= 1 ) {
                foreach ( $temp as $key => $t ) {
                    if ( in_array($t, $remove) ) {
                        unset($temp[$key]);
                    }
                }
            }

            return $temp;
        } else {
            return false;
        }
    }

    /**
     * Get the count of filtered participant IDs for a given fundraiser ID.
     * @param  int $f_id   The fundraiser ID
     * @return int $number The number of
     */
    public function get_filtered_participant_ids_by_fid_count( $f_id ) {
        // Validation
        $validation = $this->validate($f_id, 'f_id');
        if ( $validation != true ) {
            return $validation;
        }

        // Get the participants
        $participants = $this->get_filtered_participant_ids_by_fid($f_id);
        $number       = (!empty($participants) ) ? count($participants) : 0;

        return $number;
    }

    /**
     * Retreive ALL user IDs attached to a fundraiser.
     * @param  int   $f_id The fundraiser ID
     * @return mixed object of results or false
     */
    public function get_all_user_ids_by_fid( $f_id = null ) {
        // Validation
        $validation = $this->validate($f_id, 'f_id');
        if ( $validation != true ) {
            return $validation;
        }

        // Query the database
        $results = $this->wpdb->get_results("SELECT `u_id` FROM `{$this->table_name}` WHERE `f_id` = '{$f_id}'", ARRAY_N);

        foreach ( $results as $result ) {
            $temp[] = $result[0];
        }

        return $temp;
    }

    public function get_total_participants_by_fid( $f_id ) {
        // Query the database
        $total_participants = $this->wpdb->get_var($this->wpdb->prepare(
                        "SELECT COUNT(*) FROM {$this->table_name} WHERE f_id = '%d' LIMIT 500", $f_id
        ));
        if ( $total_participants == null )
            return 0;
        return $total_participants;
    }

    /**
     * Insert a 'participation' user's fundraiser participation record into `fundraiser_participants` table.
     * @param  int  $f_id The fundraiser ID
     * @param  int  $u_id The user ID
     * @return void
     */
    public function insert_record( $f_id, $u_id ) {
        // Validation -- fundraiser id
        $validation = $this->validate($f_id, 'f_id');
        if ( $validation != true ) {
            return $validation;
        }

        // Validation -- user id
        $validation = $this->validate($u_id, 'u_id');
        if ( $validation != true ) {
            return $validation;
        }

        if ( $this->wpdb->get_row("SELECT * FROM `{$this->table_name}` WHERE ( `f_id` = '{$f_id}' AND `u_id` = '{$u_id}' ) LIMIT 1", ARRAY_N) == null ) {
            $wpdb->insert(
                    $this->table_name, array (
                'f_id' => $f_id,
                'u_id' => $u_id,
                    )
            );
        }
    }

    /**
     * Get remove user ids
     * @param  int   $f_id The fundraiser ID
     * @return mixed Array of user IDs to remove, or false if there are none
     */
    public function get_remove_user_ids( $f_id = null ) {
        // Validation
        $validation = $this->validate($f_id, 'f_id');
        if ( $validation != true ) {
            return $validation;
        }

        // Load the secondary_adins class
        load_class('secondary_admins.class.php');
        $sadmins = new Secondary_Admins();

        $remove = false;

        // Get secondary admins
        $check = $sadmins->get_sadmin_ids_by_fid($f_id);

        if ( is_array($check) == true ) {
            foreach ( $check as $ch ) {
                $remove[] = $ch;
            }
        }

        // Get the post author
        $remove[] = get_post_field('post_author', $f_id);

        return $remove;
    }

    /**
     * Is user ID attached to fundraiser ID?
     * @param  int $u_id The user ID.
     * @param  int $f_id The fundraiser ID.
     * @return bool
     */
    public function is_user_attached_to_fundraiser_id( $u_id, $f_id ) {
        // Validation -- fundraiser id
        $validation = $this->validate($f_id, 'f_id');
        if ( $validation != true ) {
            return $validation;
        }

        // Validation -- user id
        $validation = $this->validate($u_id, 'u_id');
        if ( $validation != true ) {
            return $validation;
        }

        // Query the database
        $results = $this->wpdb->get_results("SELECT * FROM `{$this->table_name}` WHERE `f_id` = '{$f_id}' AND `u_id` = '{$u_id}'", ARRAY_N);

        if ( empty($results) ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Validate inputs.
     * @param  any    $value
     * @param  string $type
     * @return mixed  Message on failure, true on success
     */
    private function validate( $value = null, $type ) {
        if ( empty($value) ) {
            return "{$type} is empty";
        }

        return true;
    }


	/**
	 * @param $u_id
	 * @param $f_id
	 *
	 * @return false|int
	 */
	public function delete_fundraiser_participant( $u_id, $f_id ) {
		return $this->wpdb->delete( $this->table_name, array( 'u_id' => $u_id, 'f_id' => $f_id ), array( "%d", "%d" ) );
	}

}
