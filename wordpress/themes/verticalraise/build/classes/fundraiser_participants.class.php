<?php

/**
 * Insert a 'participation' user's fundraiser participation record into `fundraiser_participants` table
 * @param int f_id fundraiser id
 * @param int f_uid user id
 * @return void
 */
function store_user_participation($f_id, $u_id) {
    global $wpdb;

    if (!is_int($f_id))
        return false;
    if (!is_int($u_id))
        return false;

    $table_name = "fundraiser_participants";
    if ($wpdb->get_row("SELECT * FROM $table_name WHERE ( `f_id` = '$f_id' AND `u_id` = '$u_id' ) LIMIT 1", ARRAY_N) == null) {
        $wpdb->insert(
                $table_name, array(
            'f_id' => $f_id,
            'u_id' => $u_id,
                )
        );
    }
}

/**
 * Retreive all participants attached to fundraiser
 * @param int f_id fundraiser id
 * @return mixed object of results or false
 */
function get_fundraiser_participant_ids($f_id) {
    global $wpdb;
    if (!empty($f_id)) {
        $table_name = "fundraiser_participants";
        return $wpdb->get_results("SELECT `u_id` as 'u_id' FROM $table_name WHERE `f_id` = '$f_id'", ARRAY_N);
    }
}

/**
 * Return list of users (ID, display_name) attached to fundraiser
 * @param int f_id fundraiser id
 * @return object users
 */
function get_fundraiser_participants($f_id) {
    global $wpdb;

    $participant_results = get_fundraiser_participant_ids($f_id); // Get the participant ids
    $p_count = 1;

    if (!empty($participant_results)) {
        // Build our array of particpant ids
        foreach ($participant_results as $participant) {
            $participants[] = $participant[0];
            $p_count++;
        }

        // Return ids and display_names
        $args = array(
            'include' => $participants,
            'orderby' => 'display_name',
            'order' => 'ASC',
            'number' => $p_count,
            'count_total' => false,
            'fields' => array('ID', 'display_name'),
        );

        return get_users($args);
    } else {
        return array();
    }
}


