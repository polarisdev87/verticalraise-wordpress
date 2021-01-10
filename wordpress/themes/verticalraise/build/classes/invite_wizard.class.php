<?php

class Invite_Wizard
{

    // Class variables
    private $user_ID;
    private $fundraiser_ID;
    // Class objects
    private $participants;
    private $sadmins;

    public function __construct() {
        global $user_ID;
        // Load classes
        load_class( 'secondary_admins.class.php' );
        load_class( 'participants.class.php' );

        $this->participants = new Participants();
        $this->sadmins      = new Secondary_Admins();

        $u_ID          = 0;
        $fundraiser_ID = null;

        if ( isset( $_GET['uid'] ) && !empty( $_GET['uid'] ) ) {
            $u_ID = $_GET['uid'];
        }
        if ( isset( $_GET['fundraiser_id'] ) && !empty( $_GET['fundraiser_id'] ) ) {
            $fundraiser_ID = $_GET['fundraiser_id'];
        }
        // invite wizard from participants or admins
        if ( isset( $_GET['type'] ) ) {
            if ( $_GET['type'] == 'participant' || $_GET['type'] == 'admin' ) {
                $u_ID = $user_ID;
            } else if ( $_GET['type'] == 'permalink' ) {
                $u_ID = $_GET['uid'];
            } else {
                $this->redirect( 'Return type is missing' );
            }
        }
        // Get parameters to check auth            
        $this->auth( $u_ID, $fundraiser_ID );
    }

    private function auth( $user_ID, $fundraiser_ID ) {
        if ( $user_ID == 0 ) {
            $user_ID = get_post_field( 'post_author', $fundraiser_ID );
        }
        if ( !$user_ID ) {
            $this->redirect( 'The user id is missing' );
        }

        if ( empty( $fundraiser_ID ) ) {
            $this->redirect( 'The fundraiser id is missing' );
        }

        // Check to see if the fundraiser exists
        if ( get_post_status( $fundraiser_ID ) == false ) {
            $this->redirect( 'This fundraiser id does not exist' );
        }

        // Check to see if the user exists
        $user = get_userdata( $user_ID );

        if ( $user === false ) {
            $this->redirect( 'This user id does not exist' );
        }

        // Check to see if the user is attached to the fundraiser
        $attached = $this->participants->is_user_attached_to_fundraiser_id( $user_ID, $fundraiser_ID );

        if ( $attached == true ) {
            return; // We are attached
        }

        // Is the user an sadmin of the fundraiser ID?
        $attached2 = $this->sadmins->is_sadmin( $fundraiser_ID, $user_ID );

        if ( !empty( $attached2 ) ) {
            return; // We are attached
        }

        // Is the user an author of the fundraiser ID?
        $author_id = get_post_field( 'post_author', $fundraiser_ID );

        if ( !empty( $author_id ) && $user_ID == $author_id ) {
            return; // We are attached
        }

        // User was not attached
        $this->redirect( 'This user id does not belong to this fundraiser id' );
    }

    private function redirect( $message = null ) {

        echo $message;
        exit();

        //$message = urlencode($message);
        //header( 'Location: ' . get_site_url() . '/wizard-error?message=' . $message ) ;
    }

}
