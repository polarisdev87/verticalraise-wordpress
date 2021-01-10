<?php

/* Template Name: Team Logo Upload Ajax */

use classes\app\fundraiser\Fundraiser_Media;  //Fundraiser Media Class Object

ini_set( 'gd.jpeg_ignore_warning', 1 );

$file_size_limit = '3145728'; // 3 MB

$upload_dir = wp_upload_dir();
$file_path  = $upload_dir['basedir'] . '/teamlogo_img/';

if ( is_dir( $file_path ) === false ) {
    $old = umask( 0 );
    mkdir( $file_path, 0777 );
    umask( $old );
}
//$file_path  = str_replace('/', DIRECTORY_SEPARATOR, $upload_dir['path']) . DIRECTORY_SEPARATOR;
//var_dump($_POST['fundraiser_id']);
// Accept the incoming file
if ( !empty( $_FILES ) ) {

    // Check for Nonce
    // Check if the user is logged in
    if ( is_user_logged_in() == FALSE ) {
        echo "User must be logged in";
        // redirect to login page
        exit();
    }

    // Check that it's the correct submission
    if ( empty( $_FILES['blob_file'] ) ) {
        echo "Missing file submission";
        exit();
    }

    if ( function_exists( 'exif_imagetype' ) == FALSE ) {
        echo "exif_imagetype function not enabled";
        exit();
    }

    // Check image type (2 = JPEG)
    if ( exif_imagetype( $_FILES['blob_file']['tmp_name'] ) != 2 ) {
        echo "This file type is not supported";
        exit();
    }

    $file_check = filesize( $_FILES['blob_file']['tmp_name'] );

    // Check file size
    if ( $file_check != FALSE ) {
        if ( $file_check > $file_size_limit ) {
            echo "The filesize is too big: {$file_check} (limit {$file_size_limit})";
            exit();
        }
    } else {
        echo "Can't read filesize";
        exit();
    }
// Grab the incoming file
    $file = $_FILES['blob_file'];
    $name = $_FILES['blob_file']['name'];
    $size = $_FILES['blob_file']['size'];


    //get original fundraiser image filename   

    $fundraise_mediaObj = new Fundraiser_Media();
    $image_url          = $fundraise_mediaObj->get_fundraiser_logo( $_POST['fundraiser_id'] );

    $oldTeamlogo  = explode( "-", basename( $image_url ) )[0];
    $fileIndexArr = explode( "_", $oldTeamlogo );
    $pIndex       = isset( $fileIndexArr[1] ) ? (int) $fileIndexArr[1] : 0;
    //new file name
    $image_name   = $_POST['fundraiser_id'] . "_" . floatval( $pIndex + 1 ) . ".jpg";

//    $image_name = $_POST['fundraiser_id'] . "-" . date('s') . ".jpg";
    $tmp = $_FILES['blob_file']['tmp_name'];

    $image_location = $file_path . $image_name;

    // Move the file onto the server
    $uploaded = move_uploaded_file( $tmp, $file_path . $image_name );

    if ( $uploaded == true ) {
        // It successfully uploaded
    } else {
        // There was some sort of problem
        echo "There was some sort of problem -- could not upload.";
        exit();
    }

    // Check if the file is there
    if ( file_exists( $image_location ) == FALSE ) {
        echo "There was some sort of problem -- file does not exist.";
        exit();
    }

    if ( !function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }

    $file_name = $image_name;

    // Upload the image
    $upload_dir      = wp_upload_dir();
    $upload_path_rel = $upload_dir['basedir'] . '/teamlogo_img/';
//    $upload_path_rel  = str_replace('/', DIRECTORY_SEPARATOR, $upload_dir['path']) . DIRECTORY_SEPARATOR;

    $upload_location = $upload_path_rel . $file_name;
    // Check image file type
    $wp_filetype     = wp_check_filetype( $file_name, null );

    // Set attachment data
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title'     => sanitize_file_name( $file_name ),
        'post_content'   => '',
        'post_status'    => 'inherit'
    );

    // Create the attachment
    $attach_id = wp_insert_attachment( $attachment, $upload_location );

    // Add meta to hide it from the wordpress media library
    add_post_meta( $attach_id, 'hide_form_library', 1 );

    // Include image.php
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    // Define attachment metadata
    $attach_data = wp_generate_attachment_metadata( $attach_id, $upload_location );

    // Assign metadata to attachment
    wp_update_attachment_metadata( $attach_id, $attach_data );
    set_post_thumbnail( $_POST['fundraiser_id'], $attach_id );

    $post = get_post( $_POST['fundraiser_id'] );
    if ( $post && $attach_id && get_post( $attach_id ) ) {
        update_post_meta( $post->ID, '_thumbnail_id', absint( $attach_id ) );
    } else {
        add_post_meta( $post->ID, '_thumbnail_id', absint( $attach_id ) );
    }

    $thumb_image_url = $fundraise_mediaObj->get_fundraiser_logo( $_POST['fundraiser_id'] );
    echo $thumb_image_url;

    exit();
}
