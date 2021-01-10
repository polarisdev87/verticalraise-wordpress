<?php

/* Template Name: Create Fundraiser */

use classes\app\fundraiser\Create_Fundraiser;

if ( is_user_logged_in() ) {

    global $user_ID;
    if ( isset( $_POST['submit_for_approval'] ) ) {

        $create_fundaiser = new Create_Fundraiser( $user_ID );
        $new_fundraiser   = $create_fundaiser->create();
        do_action( 'dbt_fundraiser_created' , $new_fundraiser );        
        $redirectUrl      = get_bloginfo( 'url' ) . '/single-fundraiser/?fundraiser_id=' . $new_fundraiser;
        $result['status'] = true;
        $result['data']   = $redirectUrl;
        wp_send_json($result, 200);

    }
} else {
    $redirectUrl      = get_bloginfo( 'url' );
    $result['status'] = true;
    $result['data']   = $redirectUrl;
    die( json_encode( $result ) );
    exit();
}
