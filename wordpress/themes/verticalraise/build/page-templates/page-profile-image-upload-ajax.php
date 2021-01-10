<?php

/* Template Name: Profile Image Upload Ajax */
global $user_ID;
ini_set('gd.jpeg_ignore_warning', 1);

$file_size_limit = '3145728'; // 3 MB

$upload_dir = wp_upload_dir();
$file_path = $upload_dir['basedir'] . '/profile_img_thumb/';

// Accept the incoming file
if (!empty($_FILES)) {

    // Check for Nonce
    // Check if the user is logged in
    if (is_user_logged_in() == FALSE) {
        echo "User must be logged in";
        // redirect to login page
        exit();
    }

    // Check that it's the correct submission
    if (empty($_FILES['blob_file'])) {
        echo "Missing file submission";
        exit();
    }

    if (function_exists('exif_imagetype') == FALSE) {
        echo "exif_imagetype function not enabled";
        exit();
    }

    // Check image type (2 = JPEG)
    if (exif_imagetype($_FILES['blob_file']['tmp_name']) != 2) {
        echo "This file type is not supported";
        exit();
    }

    $file_check = filesize($_FILES['blob_file']['tmp_name']);

    // Check file size
    if ($file_check != FALSE) {
        if ($file_check > $file_size_limit) {
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

    //get original filename
    preg_match('/src="(.+?)"/', get_avatar( $user_ID), $matches);
    $src        = $matches[1];
    $originalFilename = explode("-", basename($src))[0];
    $fileIndexArr = explode("_", $originalFilename);
    $pIndex = isset($fileIndexArr[1])?(int)$fileIndexArr[1]:0;
//    $image_name = 'thumb_' . md5(uniqid() . time()) . ".jpg";
    $image_name = $user_ID . "_" . floatval($pIndex + 1) .  ".jpg";
    $tmp = $_FILES['blob_file']['tmp_name'];

    $image_location = $file_path . $image_name;

    // Move the file onto the server
    $uploaded = move_uploaded_file($tmp, $file_path . $image_name);

    if ($uploaded == true) {
        // It successfully uploaded
    } else {
        // There was some sort of problem
        echo "There was some sort of problem -- could not upload.";
        exit();
    }

    // Check if the file is there
    if (file_exists($image_location) == FALSE) {
        echo "There was some sort of problem -- file does not exist.";
        exit();
    }

    if (!function_exists('wp_handle_upload')) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }

    $file_name = $image_name;

    // Upload the image
    $upload_dir = wp_upload_dir();
    $upload_path_rel = $upload_dir['basedir'] . '/profile_img_thumb/';
    $upload_path = get_bloginfo('url') . '/wp-content/uploads/profile_img_thumb/';

    $thumb_image_location = $upload_path_rel . $file_name;
    $thumb_image_url = $upload_path . $file_name;

    $image_data = file_get_contents($thumb_image_url);

    // Check image file type
    $wp_filetype = wp_check_filetype($file_name, null);

    // Set attachment data
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($file_name),
        'post_content' => '',
        'post_status' => 'inherit'
    );

    // Create the attachment
    $attach_id = wp_insert_attachment($attachment, $thumb_image_location);

    // Add meta to hide it from the wordpress media library
    add_post_meta($attach_id, 'hide_form_library', 1);

    // Include image.php
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    // Define attachment metadata
    $attach_data = wp_generate_attachment_metadata($attach_id, $thumb_image_location);

    // Assign metadata to attachment
    wp_update_attachment_metadata($attach_id, $attach_data);

    global $user_ID;
//    global $wpdb;

    delete_metadata('post', null, '_wp_attachment_wp_user_avatar', $user_ID, true);
    update_user_meta($user_ID, '_wp_attachment_wp_user_avatar', $attach_id);

    update_user_meta($user_ID, $wpdb->get_blog_prefix() . 'user_avatar', $attach_id);

    // Check to see if added


    echo "added successfully";

    exit();
}
if ($_POST['facebook_img'] == "Upload") {

    $file_formats = array("jpg", "jpeg", "png", "gif", "bmp", "JPG", "JPEG", "PNG");

    $upload_dir = wp_upload_dir();
    $filepath = $upload_dir['basedir'] . '/profile_img_thumb/';
    $preview_width = "400";
    $preview_height = "300";

    $url = $_POST['imagefile'];
    //$name = basename($url);
    $imagename = md5(uniqid() . time()) . ".jpg";
    $upload = file_put_contents($filepath . $imagename, file_get_contents($url));
    if ($upload) {
        echo trim($imagename);
    }
}