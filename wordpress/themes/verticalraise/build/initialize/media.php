<?php

// Add custom image sizes
add_image_size( 'fundraiser-logo-thumb', 270, 270, true );
add_image_size( 'fundraiser-gallery-thumb', 680, 400, true );
add_image_size( 'fundraiser-logo-small', 96, 96, true );
add_image_size( 'fundraiser-logo-mobile-small', 72, 72, true );
add_image_size( 'fundraiser-logo-mobile-medium', 130, 130, true );
add_image_size( 'fundraiser-logo-large', 400, 400, true );
add_image_size( 'fundraiser-logo-medium', 220, 220, true );
add_image_size( 'testimonial-logo-thumb', 282, 282, array( 'center', 'top' ) );

function save_video_thumb( $post_id, $post, $update ) {
    if ( $post->post_type != 'fundraiser' ) {
        return;
    }
    $images = get_field( 'image_gallery', $post_id );
}

/**
 * @param $wp_query_obj
 */
function hide_profile_image_attachments( $wp_query_obj ) {
    global $current_user, $pagenow;

    if ( !is_a( $current_user, 'WP_User' ) )
        return;

    if ( !in_array( $pagenow, array( 'upload.php', 'admin-ajax.php' ) ) )
        return;

    if ( !current_user_can( 'delete_pages' ) ) {
        $wp_query_obj->set( 'author', $current_user->ID );
    } else {
        $wp_query_obj->set( 'author', $current_user->ID );
    }

    return;
}

add_action( 'pre_get_posts', 'hide_profile_image_attachments' );



//updata featured iamge on wp-admin
/*add_action( 'save_post', 'my_save_post_function', 10, 3 );

function my_save_post_function( $post_ID, $post, $update ) {

    $upload_dir = wp_upload_dir();
    $file_path  = $upload_dir['basedir'] . '/teamlogo_img/';


    $image_name = false;
    $image_url  = wp_get_attachment_image_src( get_post_thumbnail_id( $post_ID ), "fundraiser-logo-thumb" );

    if ( $image_url ) {
        $image_name = unexist_file( $file_path, $post_ID );

        $file       = $file_path . $image_name;
        // Create the image  file on the server
        $image_data = file_get_contents( $image_url[0] ); // Get image data
        file_put_contents( $file, $image_data );

        // Check image file type
        $wp_filetype = wp_check_filetype( $image_name, null );

        // Set attachment data
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title'     => sanitize_file_name( $image_name ),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        // Create the attachment
        $attach_id = wp_insert_attachment( $attachment, $file, $post_ID );

        // Include image.php
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // Define attachment metadata
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );

        // Assign metadata to attachment
        wp_update_attachment_metadata( $attach_id, $attach_data );

        // And finally assign featured image to post
        set_post_thumbnail( $post_ID, $attach_id );
    }
}*/

function unexist_file( $file_path, $post_ID ) {
    //new file name
    $index      = 0;
    $file_exist = true;
    $image_name = "";
    while ( $file_exist ) {
        $index++;
        $image_name = $post_ID . "_" . $index . ".jpg";
        $file       = $file_path . $image_name;
        $file_exist = file_exists( $file );
    }
    return $image_name;
}
