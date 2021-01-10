<?php

/**
 * Template Name: Edit Fundraiser
 * @name Edit Fundraiser
 * @description Fundraiser author can edit information about the fundraiser.
 */
use classes\app\fundraiser\Edit_Fundraiser;

if ( is_user_logged_in() ) {

    /**
     * Global User ID.
     */
    global $user_ID;

    if ( isset( $_POST['update_fundraiser'] ) ) {

        /**
         * Process the Form Submit.
         */
        $f_id = $_POST['fundraiser_id'];

        $edit_fundraiser = new Edit_Fundraiser( $user_ID, $f_id );
        $result          = $edit_fundraiser->edit();
        do_action( 'dbt_fundraiser_updated', $f_id );
        die( json_encode( $result ) );
    }

    /**
     * Include Template Footer.
     */
} else {
    /**
     * Redirect User.
     */
    $redirectUrl      = get_bloginfo( 'url' );
    $result['status'] = true;
    $result['data']   = $redirectUrl;
    die( json_encode( $result ) );
    exit();
}
