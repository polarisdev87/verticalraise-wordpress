<?php

/**
 * Migrate data
 */
### TODO: Delete old supporter custom posts after we migrate data

/**
 * Migrate all of the supporters from the supporter post type to the donations table.
 * Make sure to match the # of supporter posts to the donations table afterwards.
 * @param int $interval How many records to process in one sitting.
 */
function move_donations_to_new_table($start = 0, $interval = 20) {

    // Only run if admin
    if (is_admin() == false)
        return false;

    global $wpdb;

    // Run through every record
    $args = array(
        'post_type' => 'supporter',
        'post_status' => 'publish',
        'posts_per_page' => $interval,
        'offset' => $start,
        'orderby' => 'ID',
        'order' => 'ASC'
    );
    $supporter_query = new WP_Query($args);

    $count = 0;
    $insert_count = 0;
    $skip_count = 0;

    if ($supporter_query->have_posts()) :
        while ($supporter_query->have_posts()) : $supporter_query->the_post();

            $count++;

            // Get post meta
            $meta = get_post_meta(get_the_ID());

            $params['fundraiser_id'] = wp_get_post_parent_id(get_the_ID());

            // If there is no fundraiser id, skip
            if (empty($params['fundraiser_id']))
                continue;

            $params['media'] = (!empty($meta['media'][0]) ) ? $meta['media'][0] : '';
            $params['uid'] = (!empty($meta['uid'][0]) ) ? $meta['uid'][0] : '';
            $params['email'] = (!empty($meta['email'][0]) ) ? $meta['email'][0] : '';

            $params['full_name'] = get_the_title();
            $params['amount'] = (!empty($meta['amount'][0]) ) ? (float) $meta['amount'][0] : '';

            $params['anonymous'] = (!empty($meta['anonymous'][0]) ) ? $meta['anonymous'][0] : 0;
            $params['time'] = get_the_date('Y-m-d h:m:s', get_the_ID());
            $params['transaction_id'] = '';

            /* echo "<pre>";
              print_r($params);
              echo "</pre>"; */

            // First check to see if the record exists

            $query = "SELECT * FROM `donations` WHERE f_id = '{$params['fundraiser_id']}' AND ( amount = '{$params['amount']}' AND ( name = '{$params['full_name']}' AND time = '{$params['time']}' ));";
            $results = $wpdb->get_row($query);

            if (null == $results) {
                echo "no results<br>";
            } else {
                $skip_count++;
                echo "skipped<br>";
                continue;
            }

            try {

                // Begin transaction
                $wpdb->query('BEGIN');

                // Insert query
                $insert = $wpdb->query($wpdb->prepare(
                                "
                    INSERT INTO `donations` (f_id, media, uid, email, name, anonymous, amount, time, transaction_id) VALUES ( %d, %s, %d, %s, %s, %d, %f, %s, %s)
                ", array(
                            $params['fundraiser_id'], // fundraiser id
                            $params['media'], // media type (ie. f, flyer, sms, email)
                            $params['uid'], // participant's user id
                            $params['email'],
                            $params['full_name'], // donator's full name
                            $params['anonymous'], // donator's preference to be anonymous
                            $params['amount'], // the amount donated returned by Stripe
                            $params['time'], // time stamp
                            $params['transaction_id'] // transaction id returned by Stripe
                                )
                ));

                if (empty($insert)) {
                    // Error occured, don't save any changes
                    $wpdb->query('ROLLBACK'); // Roll back the transaction
                    throw new Exception('SQL Error: ' . $wpdb->print_error()); // Log error message
                } else {
                    // Success
                    $wpdb->query('COMMIT');
                    $insert_count++;
                    echo "Inserted<br>";
                }
            } catch (Exception $e) {
                if (extension_loaded('newrelic')) { // Ensure PHP agent is available
                    newrelic_notice_error($e->getMessage(), $e);
                }
                echo "insert_payment failed:  {$e->getMessage()}<br>";
            }

            // check to see if the record exist?
            // if not, insert it

            if ($count > $interval)
                exit();

        endwhile;
    endif;

    echo "<br><br>";

    echo "{$skip_count} records skipped<br>";
    echo "{$insert_count} records migrated<br>";
}

if (is_admin()) {
    //move_donations_to_new_table(23499,10000);
}

// remove all of the supporter posts, and associated post meta

function copy_donations_archive_to_new_table() {
    global $wpdb;

    $res = $wpdb->query('TRUNCATE TABLE donations_archive');

    $wpdb->query('BEGIN');

    $insert = $wpdb->query("INSERT INTO donations_archive SELECT * FROM donations");


    if (empty($insert)) {
        // Error occured, don't save any changes
        $wpdb->query('ROLLBACK'); // Roll back the transaction
        throw new Exception('SQL Error: ' . $wpdb->print_error()); // Log error message
    } else {
        // Success
        $wpdb->query('COMMIT');
        echo "Inserted";
    }
    exit();
}

if (isset($_GET['donations_copy']) && $_GET['donations_copy'] == 'donations_copy') {
//    copy_donations_archive_to_new_table();
}

function add_donations_sum() {

    global $wpdb;

    //Clear donations_sum table
    $res = $wpdb->query('TRUNCATE TABLE donations_sum');

    if ($res) {
        $results = $wpdb->get_results("SELECT f_id, SUM(amount) as amount FROM `donations` where `refunded` = '0' GROUP BY f_id", ARRAY_A);

        foreach ($results as $row) {
            $wpdb->insert('donations_sum', array(
                'amount' => $row['amount'],
                'f_id' => $row['f_id']
                    ), array('%d', '%d')
            );
        }
    } else {
        echo "Query error!";
    }
    echo "Success!";
    exit();
}

if (isset($_GET['sum_migrate']) && $_GET['sum_migrate'] == 'migrate') {
    add_donations_sum();
}

/*
  // get ids
  function populate_sharing_records_specific_id($f_id){
  load_class('participant_records.class.php');
  $participant_records = new Participant_Sharing_Totals();

  $ids = get_fundraiser_participant_ids($f_id);
  foreach ( $ids as $id ) {
  $participant_records->insert_initial($f_id, $id[0]);
  }
  }

  if ( is_user_logged_in() ) {
  if ( get_current_user_id() == 1 ) {
  populate_sharing_records_specific_id(57441);
  }
  } */

/**
 * Populate the `fundraiser_participants` table from live data
 */
function populate_fundraiser_participants_table() {
    global $wpdb;

    $table_name = "fundraiser_participants";
    $num_users = 4000; // return 300 at a time
    $offset = 0; // starting position
    $count = 0;

    $user_query = new WP_User_Query(array('role' => '', 'number' => $num_users, 'offset' => $offset));

    if (!empty($user_query->results)) {

        foreach ($user_query->results as $user) {
            $fundraisers = json_decode(get_user_meta($user->ID, 'campaign_participations', true));

            if (!empty($fundraisers)) {

                // for each fundraiser the user is a part of
                foreach ($fundraisers as $key => $f_id) {
                    // if a record does not already exist
                    if ($wpdb->get_row("SELECT * FROM $table_name WHERE ( `f_id` = '$f_id' AND `u_id` = '$user->ID' ) LIMIT 1", ARRAY_N) == null) {
                        $wpdb->insert(
                                $table_name, array(
                            'f_id' => $f_id,
                            'u_id' => $user->ID,
                                )
                        );

                        $count++;
                    }
                }
            }
        }
    }

    echo "inserted {$count} records";
}

if (is_admin()) {
    //populate_fundraiser_participants_table();
}

/**
 * Populate the `fundraiser_sadmin` table from live data
 */
function populate_fundraiser_sadmin_table() {
    global $wpdb;

    $table_name = "fundraiser_sadmin";
    $num_users = 4000; // return 300 at a time
    $offset = 0; // starting position
    $count = 0;

    $user_query = new WP_User_Query(array('role' => '', 'number' => $num_users, 'offset' => $offset));

    if (!empty($user_query->results)) {

        foreach ($user_query->results as $user) {
            $fundraisers = json_decode(get_user_meta($user->ID, 'campaign_sadmin', true));

            if (!empty($fundraisers)) {

                // for each fundraiser the user is a part of
                foreach ($fundraisers as $key => $f_id) {
                    // if a record does not already exist
                    if ($wpdb->get_row("SELECT * FROM $table_name WHERE ( `f_id` = '$f_id' AND `u_id` = '$user->ID' ) LIMIT 1", ARRAY_N) == null) {
                        $wpdb->insert(
                                $table_name, array(
                            'f_id' => $f_id,
                            'u_id' => $user->ID,
                                )
                        );

                        $count++;
                    }
                }
            }
        }
    }

    echo "inserted {$count} records";
}

if (is_admin()) {
    //populate_fundraiser_sadmin_table();
}


//populate fundraiser logo
if (isset($_GET['populate_teamlogo']) && $_GET['populate_teamlogo'] == 'run') {
    populate_fundraiser_team_logo();
}

function populate_fundraiser_team_logo() {
    $args = array(
        'post_type' => 'fundraiser',
        'posts_per_page' => -1,
    );

    $fundraiser_query = new WP_Query($args);

    // If there are fundraisers

    if ($fundraiser_query->have_posts()) {
        $n = 0;
        $image_url = array();
        $upload_dir = wp_upload_dir();

        while ($fundraiser_query->have_posts()) : $fundraiser_query->the_post();
//            $n++;
//            if ($n == 100) {
//                break;
//            }
            $fundraiser_id = get_the_ID();
            $meta_attachment = wp_get_attachment_metadata(get_post_thumbnail_id($fundraiser_id));

            if (!empty($meta_attachment)) {
                $file_string = $meta_attachment['file'];
                $name = explode("/", $file_string);
                if ($name[0] != 'teamlogo_img') {
                    $pref_path = $name[0] . "/" . $name[1] . "/";

                    $current_file = $upload_dir['basedir'] . "/" . $pref_path . $name[2];
                    $new_file_name = $fundraiser_id . "-1.jpg";
                    $new_file = $upload_dir['basedir'] . "/" . '/teamlogo_img/' . $new_file_name;
                    $uploaded = false;
                    if (file_exists($current_file)) {
                        $uploaded = copy($current_file, $new_file);
                    }

                    if ($uploaded == true) {
                        $wp_filetype = wp_check_filetype($new_file_name, null);

                        // Set attachment data
                        $attachment = array(
                            'post_mime_type' => $wp_filetype['type'],
                            'post_title' => sanitize_file_name($new_file_name),
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );
                        // Create the attachment
                        $attach_id = wp_insert_attachment($attachment, $new_file);

                        // Add meta to hide it from the wordpress media library
                        add_post_meta($attach_id, 'hide_form_library', 1);

                        // Include image.php
                        require_once(ABSPATH . 'wp-admin/includes/image.php');

                        // Define attachment metadata
                        $attach_data = wp_generate_attachment_metadata($attach_id, $new_file);

                        // Assign metadata to attachment
                        wp_update_attachment_metadata($attach_id, $attach_data);
                        set_post_thumbnail($fundraiser_id, $attach_id);
                    } else {
                        // There was some sort of problem
                        echo "There was some sort of problem -- could not upload.";
//                    exit();
                    }
                }
            }

        endwhile;
    }
}

if (isset($_GET['populate_profile']) && $_GET['populate_profile'] == 'run') {
    
    $count = (!isset($_GET['count']))?100:$_GET['count'];
   
    if (isset($_GET['start']) ) {
        populate_profile_pic($_GET['start'], $count);
    } else {
        echo "Please set start number : &start = 0&count=100";
    }
}

function populate_profile_pic($start, $count) {
  
//    $all_users = get_users();
    global $wpdb;
    
    $result = $wpdb->get_results("select id from wp_users order by id limit {$start}, {$count}", ARRAY_A);
    echo "Start Number: {$start}   Count: {$count} <br>"; 
    foreach ($result as $user) {      
        $attach_id = get_user_meta($user['id'], 'wp_user_avatar', true);
        if (!empty($attach_id)) {

            $user_metas = wp_get_attachment_metadata($attach_id);
            $user_updir = wp_upload_dir();
            $user_path = pathinfo($user_metas['file']);
            $user_path_name = $user_path['dirname'];
            $user_updir = wp_upload_dir();

            $curr_name = explode("/", $user_metas['file'])[1];

            $file_name = $user['id'] . ".jpg";

            if ($curr_name != $file_name) {
                echo "userID:  " . $user['id'] . "<br>";
                echo "current file name:  " . $curr_name . "<br>";
                echo "new file name:  " . $file_name . "<br>";
                foreach ($user_metas as $user_meta => $user_meta_val) {
                    if ($user_meta === "sizes") {
                        foreach ($user_meta_val as $user_sizes => $user_size) {
                            $user_original_filename = $user_updir['basedir'] . "/" . $user_path_name . "/" . $user_size['file'];

                            if (file_exists($user_original_filename)) {
                                unlink($user_original_filename);
                            }
                        }
                    }
                }

                $origin_file = $user_updir['basedir'] . "/" . $user_metas['file'];
                $new_file = $user_updir['basedir'] . "/profile_img_thumb/" . $file_name;
                $renamed = false;
                if (file_exists($origin_file)) {
                    if (file_exists($new_file)) {
                        unlink($new_file);
                    } else {
                        try {
                            $renamed = rename($origin_file, $new_file);
                            echo "renamed- " . $user['id'] . ":  " . $file_name . " Success<br><br>";
                        } catch (Exception $ex) {
                            echo "error : " . $user['id'] . "<br>";
                            var_dump($ex);
                            $renamed = false;
                        }
                    }
                } else {
                    echo "renamed- " . $user['id'] . ":  FILE DOES NOT EXIST <br><br>";
                }


                if ($renamed) {
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
                    $attach_id = wp_insert_attachment($attachment, $new_file);

                    // Add meta to hide it from the wordpress media library
                    add_post_meta($attach_id, 'hide_form_library', 1);

                    // Include image.php
                    require_once(ABSPATH . 'wp-admin/includes/image.php');

                    // Define attachment metadata
                    $attach_data = wp_generate_attachment_metadata($attach_id, $new_file);

                    // Assign metadata to attachment
                    wp_update_attachment_metadata($attach_id, $attach_data);

                    global $wpdb;

                    delete_metadata('post', null, '_wp_attachment_wp_user_avatar', $user['id'], true);
                    update_user_meta($user['id'], '_wp_attachment_wp_user_avatar', $attach_id);
                    update_user_meta($user['id'], $wpdb->get_blog_prefix() . 'user_avatar', $attach_id);
                }
            }
        }
    }

    exit;
}