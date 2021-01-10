<?php

/**
 * Get fundraiser participation records by either user id or fundraiser id
 */
class Participants
{

    // Class variables
    private $table_name  = "fundraiser_participants";  // Table name
    private $table_name1 = "participant_fundraiser_details";  // Table name
    private $wpdb;

    /**
     * Class constructor.
     */
    public function __construct () {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    /**
     * Retreive all fundraiser ids attached to a user.
     * @param  int $uid
     * @return mixed object of results or false
     */
    public function get_fundraiser_ids_by_userid( $uid ) {

        // Validation
        $validation = $this->validate( $uid, 'uid' );
        if ( $validation != true ) {
            return $validation;
        }

        // Query the database
        $results = $this->wpdb->get_results( "SELECT `f_id` FROM `{$this->table_name}` WHERE `u_id` = '{$uid}'", ARRAY_N );

        foreach ( $results as $result ) {
            $temp[] = $result[0];
        }

        return ( isset( $temp ) ) ? $temp : "";
    }

    public function get_total_fundraiser_ids_by_userid( $uid ) {

        // Validation
        $validation = $this->validate( $uid, 'uid' );
        if ( $validation != true ) {
            return $validation;
        }

        // Query the database
        $results = $this->wpdb->get_results( "SELECT `f_id` FROM `{$this->table_name}` WHERE `u_id` = '{$uid}'", ARRAY_N );
        return $results;
    }

    /**
     * Retreive ONLY the participant user ids attached to a fundraiser (no admin, post author, secondary admins).
     * @param  int $uid
     * @return mixed object of results or false
     */
    public function get_filtered_participant_ids_by_fid( $f_id ) {
        global $wpdb;

        // Validation
        $validation = $this->validate( $f_id, 'f_id' );
        if ( $validation != true ) {
            return $validation;
        }

        // Query the database
        $results = $this->wpdb->get_results( "SELECT `u_id` FROM `{$this->table_name}` WHERE `f_id` = '{$f_id}'", ARRAY_N );

        if ( $results != false ) {

            foreach ( $results as $result ) {
                $temp[] = $result[0];
            }

            // Grab the list of admins, secondary admins & post author.
            $remove = $this -> get_remove_user_ids( $f_id );

            // Remove secondary admins, post author and admins.
            if ( !empty( $remove ) && count( $remove ) >= 1 ) {
                foreach ( $temp as $key => $t ) {
                    if ( in_array( $t, $remove ) ) {
                        unset( $temp[$key] );
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
     * @param  int $f_id The fundraiser ID
     * @return int $number The number of
     */
    public function get_filtered_participant_ids_by_fid_count( $f_id ) {

        // Validation
        $validation = $this->validate( $f_id, 'f_id' );
        if ( $validation != true ) {
            return $validation;
        }

        // Get the participants
        $participants = $this->get_filtered_participant_ids_by_fid( $f_id );
        $number = ( !empty( $participants ) ) ? count( $participants ) : 0;


        return $number;
    }

    /**
     * Retreive ALL user IDs attached to a fundraiser.
     * @param  int $f_id The fundraiser ID
     * @return mixed object of results or false
     */
    public function get_all_user_ids_by_fid( $f_id = null ) {

        // Validation
        $validation = $this -> validate( $f_id, 'f_id' );
        if ( $validation != true ) {
            return $validation;
        }

        // Query the database
        $results = $this->wpdb->get_results( "SELECT `u_id` FROM `{$this->table_name}` WHERE `f_id` = '{$f_id}'", ARRAY_N );

        foreach ( $results as $result ) {
            $temp[] = $result[0];
        }

        return $temp;
    }

    /**
     * Insert a 'participation' user's fundraiser participation record into `fundraiser_participants` table.
     * @param  int $f_id The fundraiser ID
     * @param  int $u_id The user ID
     * @return void
     */
    public function insert_record( $f_id, $u_id ) {
        global $wpdb;

        // Validation -- fundraiser id
        $validation = $this->validate( $f_id, 'f_id' );
        if ( $validation != true ) {
            return $validation;
        }

        // Validation -- user id
        $validation = $this->validate( $u_id, 'u_id' );
        if ( $validation != true ) {
            return $validation;
        }

        if ( $this->wpdb->get_row( "SELECT * FROM `{$this->table_name}` WHERE ( `f_id` = '{$f_id}' AND `u_id` = '{$u_id}' ) LIMIT 1", ARRAY_N ) == null ) {
            $wpdb->insert(
                $this->table_name,
                array(
                    'f_id' => $f_id,
                    'u_id' => $u_id,
                )
            );
        }
    }

    /**
     * Get remove user ids
     * @param  int $f_id The fundraiser ID
     * @return mixed Array of user IDs to remove, or false if there are none
     */
    public function get_remove_user_ids( $f_id = null ) {

        // Validation
        $validation = $this->validate( $f_id, 'f_id' );
        if ( $validation != true ) {
            return $validation;
        }

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
     * Is user ID attached to fundraiser ID?
     * @param  int $u_id The user ID.
     * @param  int $f_id The fundraiser ID.
     * @return bool
     */
    public function is_user_attached_to_fundraiser_id( $u_id, $f_id ) {
        global $wpdb;

        // Validation -- fundraiser id
        $validation = $this->validate( $f_id, 'f_id' );
        if ( $validation != true ) {
            return $validation;
        }

        // Validation -- user id
        $validation = $this->validate( $u_id, 'u_id' );
        if ( $validation != true ) {
            return $validation;
        }

        // Query the database
        $results = $this->wpdb->get_results( "SELECT * FROM `{$this->table_name}` WHERE `f_id` = '{$f_id}' AND `u_id` = '{$u_id}'", ARRAY_N );

        if ( empty( $results ) ) {
            return false;
        } else {
            return true;
        }

    }

    /**
     * Is user ID attached to fundraiser ID?
     * @param  int $p_id The Participant ID.
     * @param  int $f_id The fundraiser ID.
     * @return bool
     */
    public function total_shares_count_by_uid( $f_id, $p_id = false ) {
        // Validation -- fundraiser id
        $result = array();
        $validation = $this -> validate( $f_id, 'f_id' );
        if ( $validation != true ) {
            return $validation;
        }
        $types = array( 'parents', 'email', 'facebook', 'twitter', 'text', 'sms' );
        foreach ( $types as $type ) {
            $where = ( $p_id ) ? "WHERE `fundraiser`='{$f_id}' AND `participant_id`='{$p_id}'" : "WHERE `fundraiser`='{$f_id}'";
            $res = $this->wpdb->get_results( "SELECT SUM(`{$type}`) as `{$type}` FROM `{$this->table_name1}` " . $where . " GROUP BY `fundraiser`", ARRAY_N );

            if ( !empty($res) )
                $result[$type] = $res[0];
        }


        if ( empty( $result ) ) {
            return false;
        } else {
            return $result;
        }

    }

    public function total_shares_count_by_fid( $f_id ) {
        $total = 0;
        $result = $this->total_shares_count_by_uid( $f_id, false );
        if ( !empty($result) ) {
            foreach ( $result as $key => $res ) {
                if ( $key != 'sms' ) {
                    $total += $res[0];
                }
            }
        }
        return $total;
    }

    /**
     * New function for participant share level
     * @param int $value : share count
     * @param string $param : share kind
     * @return $color;
     */
    public function level_color($value, $param ) {

        switch ( $param ) {
            case 'email' :
            case 'parent' :
                if ( $value < 1 ) {
                    $color = "#ed1c24";
                } elseif ( $value == 1 ) {
                    $color = "#3f9fe6";
                } else {
                    $color = "#46ce53";
                }
                break;
            case 'facebook' :
                if ( $value <= 1 ) {
                    $color = "#ed1c24";
                } else {
                    $color = "#46ce53";
                }
                break;
            case 'donate' :
                if ( $value < 100 ) {
                    $color = "#ed1c24";
                } elseif ( $value >= 100 && $value < 200 ) {
                    $color = "#3f9fe6";
                } else {
                    $color = "#46ce53";
                }
                break;
            case 'text':
                if ( $value <= 1 ) {
                    $color = "#ed1c24";
                } elseif ( $value > 1 && $value < 3 ) {
                    $color = "#3f9fe6";
                } else {
                    $color = "#46ce53";
                }
                break;
            default:
                break;
        }
        return $color;

    }

    public function participant_level( $fundraiser_id, $uid ) {
        global $wpdb;

        $myrows1 = $wpdb->get_results( "SELECT * FROM participant_fundraiser_details WHERE fundraiser = '{$fundraiser_id}' AND participant_id='{$uid}' AND (total >= " . _PARTICIPATION_GOAL . " OR email >=20) ORDER BY total DESC, email DESC, participant_name ASC", OBJECT );
        $myrows2 = $wpdb->get_results( "SELECT * FROM participant_fundraiser_details WHERE fundraiser = '{$fundraiser_id}' AND participant_id='{$uid}' AND (email >= 10 AND email < 20) AND total < " . _PARTICIPATION_GOAL . " ORDER BY total DESC, email DESC, participant_name ASC", OBJECT );
        $myrows3 = $wpdb->get_results( "SELECT * FROM participant_fundraiser_details WHERE fundraiser = '{$fundraiser_id}' AND participant_id='{$uid}' AND email <10 AND total < " . _PARTICIPATION_GOAL . " ORDER BY total DESC, email DESC, participant_name ASC", OBJECT );

        if ( !empty($myrows1) ) {
            $color = "#46ce53";
            return array($color, 'GOOD');
        }

        if ( !empty($myrows2) ) {
            $color = "#3f9fe6";
            return array($color, 'MODERATE');
        }

        if ( !empty($myrows3) ) {
            $color = "#ed1c24";
            return array($color, 'LOW');
        }


    }


    /**
     * Validate inputs.
     * @param  any $value
     * @param  string $type
     * @return mixed  Message on failure, true on success
     */
    private function validate( $value = null, $type ) {
        if ( empty( $value ) ) {
            return "{$type} is empty";
        }

        return true;
    }

}