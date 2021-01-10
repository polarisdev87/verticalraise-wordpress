<?php

use \classes\app\encryption\Encryption;

/**
 * Main methods for getting the $user_ID and $fundraiser_ID for any of the sharing popup window pages
 * The fundraiser ID should always be in tact, we are more concerned about the user id getting cut off
 */
class Sharing
{

    // Class objects
    private $participants;   // Participants class object
    private $sadmins;        // Secondary admins class object
    private $encryption;     // Encryption class object

    /**
     * Class constructor.
     */

    public function __construct() {

        load_class( 'secondary_admins.class.php' );
        load_class( 'participants.class.php' );

        $this->participants = new Participants();
        $this->sadmins      = new Secondary_Admins();
        $this->encryption   = new Encryption();

        $this->fundraiser_ID = $this->set_fundraiser_ID();
        $this->user_ID       = $this->set_user_ID( $this->fundraiser_ID );
    }

    /**
     * Main public method for retreiving the user_ID.
     * @access Public
     * @return int user_ID
     */
    public function get_user_ID() {
        return $this->user_ID;
    }

    /**
     * Main public method for retreiving the fundraiser_ID.
     * @access Public
     * @return int fundraiser_ID
     */
    public function get_fundraiser_ID() {
        return $this->fundraiser_ID;
    }

    /**
     * Set the user ID using a set of predefined criteria so that the user ID is reliable.
     *
     * Criteria:
     * 1. First check for a logged in user
     * 2. Second check for a query string user_id
     *    A. Check if the user ID exists
     *       1. Check if the user ID is attached to the fundraiser ID
     * 3. Lastly, return 0 if nothing checks out
     *
     * @param  int $f_id The fundraiser ID
     *
     * @return int user_ID
     */
    private function set_user_ID( $f_id ) {

        /**
         * 1. Logged in user.
         * Return the User ID for the logged in user.
         */
        if ( is_user_logged_in() ) {
            global $user_ID;

            $attached_id = $this->is_user_attached( $user_ID, $f_id );

            if ( !empty( $attached_id ) ) {
                return $attached_id;
            }
            return 0;
        }

        /**
         * 2. Get the query string user_id provided.
         */
        if ( !empty( $_GET['uid'] ) ) {
            // User ID based on $_GET param
            $user_ID = intval( $_GET['uid'] );

            // Check if the user_ID is real
            $user = get_userdata( $user_ID );

            if ( $user === false ) {
                // Nothing
            } else {
                $attached_id = $this->is_user_attached( $user_ID, $f_id );

                if ( !empty( $attached_id ) ) {
                    return $attached_id;
                }
            }
        }

        /**
         * 3. Look for an ecrypted key user_id
         */
        if ( !empty( $_POST['e_key'] ) ) {
            // Decrypt
            $decrypt = $this->encryption->decrypt( $_POST['e_key'] );

            // Check the user_id and if the time is ok
            $ex = explode( '_', $decrypt );

            // Get 12 hours ago
            $twelve_hours_ago = strtotime( '12 hours ago', current_time( "timestamp" ) );

            if ( $ex[1] > $twelve_hours_ago ) {
                $attached_id = $this->is_user_attached( $ex[0], $f_id );

                if ( !empty( $attached_id ) ) {
                    return $attached_id;
                }
            }
        }

        /**
         * 4. None of our criteria is met so return a generic user id.
         */
        return 0;
    }

    /**
     * Set the fundraiser ID based off the query parameter.
     * @return int The fundraiser ID
     */
    private function set_fundraiser_ID() {
        // Use the $_GET       
        $f_id = '';
        if ( isset( $_GET['fundraiser_id'] ) && !empty( $_GET['fundraiser_id'] ) ) {

            // Makes sure the f_id exists
            $f_id = $this->get_fid();
            return ($f_id) ? $f_id : '';
        } else {
            // Redirect the user if the F_ID does not exist?
            $f_id = '';
            return $f_id;
        }

        // Look for a session, is it real?
        ###if ( empty($f_id) ) {
        ###$f_id = $this->get_session_fid();
        ###}
        // If the user is logged in, get the user id based off them
        ### The problem with this is if the u id is wrong, not sure I thought this through entirely ###
        /** if ( empty($f_id) ) {
          $f_id = $this->get_fid_from_uid();
          } * */
        return $f_id;
    }

    /**
     * Get the fundraiser ID based off of the get parameter.
     * @return mixed $f_id or false
     */
    private function get_fid() {
        $f_id = $_GET['fundraiser_id'];
        // check if the fundraiser_id is real
        if ( get_post_status( $f_id ) != false ) {
            return $f_id;
        } else {
            return false;
        }
    }

    /**
     * Get the fundraiser ID from a possible session value.
     * @return mixed $f_id or false
     */
    private function get_session_fid() {
        session_start();
        if ( !empty( $_SESSION['invite_f_id'] ) ) {
            $f_id = $_SESSION['invite_f_id'];
            // check if the fundraiser_id is real
            if ( get_post_status( $f_id ) != false ) {
                return $f_id;
            }
        } else {
            return false;
        }
    }

    /**
     * Get the fundraiser ID from the uid if it exists.
     * @param  int $userID
     * @return mixed $f_id or false
     */
    private function get_fid_from_uid( $user_ID ) {

        // if there is a user id
        if ( $user_ID ) {
            load_class( 'participants.class.php' );
            $participants = new Participants();
            $f_ids        = $participants->get_fundraiser_ids_by_userid( $user_ID );
            if ( !empty( $f_ids ) ) {
                foreach ( $f_ids as $f_id ) {
                    if ( get_post_status( $f_id ) != false ) {
                        return $f_id;
                    }
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Check if a user is attached to the fundraiser.
     *
     * @param  int $user_ID
     * @param  int $f_id
     *
     * @return mixed A user id or false
     */
    private function is_user_attached( $user_ID, $f_id ) {

        // Is the logged in user attached to the fundraiser ID?
        $attached = $this->participants->is_user_attached_to_fundraiser_id( $user_ID, $f_id );

        if ( $attached == true ) {
            return $user_ID;
        }

        // Is the logged in user an sadmin of the fundraiser ID?
        $attached = $this->sadmins->is_sadmin( $f_id, $user_ID );

        if ( !empty( $attached ) ) {
            return $user_ID;
        }

        // Is the logged in user an author of the fundraiser ID?
        $author_id = get_post_field( 'post_author', $f_id );

        if ( !empty( $author_id ) && $author_id == $user_ID ) {
            return $user_ID;
        }

        return false;
    }

}
