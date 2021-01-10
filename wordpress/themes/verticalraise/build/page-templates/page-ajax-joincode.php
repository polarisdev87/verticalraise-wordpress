<?php

/* Template Name: Ajax JoinCode */
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use classes\models\tables\Subgroups;

global $user_ID;
load_class('secondary_admins.class.php');
$sadmins = new Secondary_Admins();

if (isset($_POST['submit_joincode'])) {

    $i = 0;
    $k = 0;
    $args = array(
        'post_type' => 'fundraiser',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'no_found_rows' => true,
        'update_post_term_cache' => false,
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'join_code',
                'value' => $_POST['join_code'],
                'type' => 'CHAR',
                'compare' => '='
            )
        )
    );

    $fundraiser_query = new WP_Query($args);   

    // If there are fundraisers
    if ($fundraiser_query->have_posts()) {

        while ($fundraiser_query->have_posts()) : $fundraiser_query->the_post();

            $fundraiser_id = get_the_ID();

            // Check for secondary admin status
            $campaign_sadmin = json_decode(get_user_meta($user_ID, 'campaign_sadmin', true));

            // Get the author id
            $author_id = get_post_field('post_author', $fundraiser_id);

            // Cannot be the author
            if ($author_id != $user_ID) {

                // Cannot be a secondary admin
                if (!in_array_my($fundraiser_id, $campaign_sadmin)) {

                    $participations_array = array();
                    // Campaigns a user is connected to
                    $campaign_participations = get_user_meta($user_ID, 'campaign_participations', true);
                    // If the user is not connected to any campaigns, connect him
                    if (empty($campaign_participations)) {

                        array_push($participations_array, $fundraiser_id);

                        ### TODO: Remove this ###
                        update_user_meta($user_ID, 'campaign_participations', json_encode($participations_array));

                        // Create a record in `fundraiser_participants`
                        store_user_participation($fundraiser_id, $user_ID);

                        $k = 0;
                    } else {

                        // Update his campaign participations to include this campaign
                        $participations_array = json_decode($campaign_participations);

                        if (!in_array($fundraiser_id, $participations_array)) {

                            array_push($participations_array, get_the_ID());

                            ### TODO: Remove this ###
                            update_user_meta($user_ID, 'campaign_participations', json_encode($participations_array));

                            // Create a record in `fundraiser_participants`
                            store_user_participation($fundraiser_id, $user_ID);

                            $k = 0;
                        } else {
                            $k++;
                        }
                    }

                    $fundraiser_participants = array();

                    // Get the fundraiser participation post_meta
                    $fundraiser_participants_original = get_post_meta($fundraiser_id, 'campaign_participations', true);
//
                    // There are no fundraiser participants
                    if (empty($fundraiser_participants_original)) {

                        // Create the post_meta and add this initial user_id
                        $fundraiser_participants[] = $user_ID;

                        ### TODO: Remove this ###
                        // Update the post_meta
                        update_post_meta($fundraiser_id, 'campaign_participations', json_encode($fundraiser_participants), $fundraiser_participants_original);

                        $k = 0; // flag
                    } else {

                        // If there are existing fundraiser participants
                        $fundraiser_participants = json_decode($fundraiser_participants_original);

                        if (!in_array($user_ID, $fundraiser_participants)) {

                            $fundraiser_participants[] = $user_ID;

                            ### TODO: Remove this ###
                            // Update the post_meta
                            update_post_meta($fundraiser_id, 'campaign_participations', json_encode($fundraiser_participants), $fundraiser_participants_original);

                            $k = 0; // flag
                        } else {
                            $k++;
                        }
                    }
                    if ($k == 0) {

                        load_class('participant_records.class.php');

                        // Insert the initial sharing record for the new user
                        $participant_records = new Participant_Sharing_Totals();
                        $participant_records->insert_initial($fundraiser_id, $user_ID);

//                        header('Location: ' . get_the_permalink(195) . '?fundraiser_id=' . get_the_ID() . '&invitepopup=1');
                        $subgroups_table = new Subgroups();
	                    $subgroups       = $subgroups_table->getSubgroups( $fundraiser_id );
	                    if ( is_array( $subgroups ) && count( $subgroups ) ) {
		                    $result['data'] = "/participant-select-subgroup/?fundraiser_id=" . $fundraiser_id;
	                    } else {
		                    $result['data'] = get_the_permalink( 195 ) . '?fundraiser_id=' . get_the_ID() . '&invitepopup=1';
	                    }
                        $result['success'] = true;
                        die(json_encode($result));
                    } else {
                        $result['data'] = '<p class="warningMsg">You have already joined this Fundraiser. To view your dashboard page please select the fundraiser below.</p>';
                        $result['success'] = false;
                        die(json_encode($result));
                    }
                } else {
                    $result['success'] = false;
                    $result['data'] = '<p class="warningMsg">You have already joined this Fundraiser. To view your dashboard page please select the fundraiser below.</p>';
                    die(json_encode($result));
                }
            } else {
                $result['success'] = false;
                $result['data'] = '<p class="warningMsg">You have already joined this Fundraiser. To view your dashboard page please select the fundraiser below.</p>';

                die(json_encode($result));
            }

        endwhile;
    } else {
        $i++;
    }

    $args = array(
        'post_type' => 'fundraiser',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'no_found_rows' => true,
        'update_post_term_cache' => false,
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'join_code_sadmin',
                'value' => $_POST['join_code'],
                'type' => 'CHAR',
                'compare' => '='
            )
        )
    );

    $fundraiser_query = new WP_Query($args);

    if ($fundraiser_query->have_posts()) {

        while ($fundraiser_query->have_posts()) : $fundraiser_query->the_post();

            $fundraiser_id = get_the_ID();
            $author_id = get_post_field('post_author', get_the_ID());

            if ($author_id != $user_ID) {

                $campaign_participations = json_decode(get_user_meta($user_ID, 'campaign_participations', true));

                if (!in_array_my($fundraiser_id, $campaign_participations)) {

                    $sadmin_array = array();
                    $campaign_sadmin = get_user_meta($user_ID, 'campaign_sadmin', true);

                    if (empty($campaign_sadmin)) {

                        array_push($sadmin_array, $fundraiser_id);

                        ### TODO: Remove the user meta option ###
                        // Update the user's campaign_sadmin user_meta
                        update_user_meta($user_ID, 'campaign_sadmin', json_encode($sadmin_array));

                        // Store the secondary admin record in the db
                        $sadmins->store_secondary_admin($fundraiser_id, $user_ID);

                        $k = 0;
                    } else {

                        $sadmin_array = json_decode($campaign_sadmin);
                        if (!in_array($fundraiser_id, $sadmin_array)) {
                            array_push($sadmin_array, $fundraiser_id);

                            ### TODO: Remove the user meta option ###
                            // Update the user's campaign_sadmin user_meta
                            update_user_meta($user_ID, 'campaign_sadmin', json_encode($sadmin_array));

                            // Store the secondary admin record in the db
                            $sadmins->store_secondary_admin($fundraiser_id, $user_ID);

                            $k = 0;
                        } else {
                            $k++;
                        }
                    }
                    if ($k == 0) {
//                        header('Location: ' . get_the_permalink(125) . '?fundraiser_id=' . $fundraiser_id);

                        $result['success'] = true;
                        $result['data'] = get_the_permalink(125) . '?fundraiser_id=' . $fundraiser_id;
                        die(json_encode($result));
                    } else {
                        $result['success'] = false;
                        $result['data'] = '<p class="warningMsg">You have already joined this Fundraiser. To view your dashboard page please select the fundraiser below.</p>';
                        die(json_encode($result));
                    }
                } else {
                    $k = 1;
                    $result['success'] = false;
                    $result['data'] = '<p class="warningMsg">You have already joined this Fundraiser. To view your dashboard page please select the fundraiser below.</p>';
                    die(json_encode($result));
                }
            } else {
                $result['success'] = false;
                $result['data'] = '<p class="warningMsg">You have already joined this Fundraiser. To view your dashboard page please select the fundraiser below.</p>';
                die(json_encode($result));
            }
        endwhile;
    } else {
        $i++;
    }


    wp_reset_postdata();

    if ($i != 0 && $k == 0) {
        $result['success'] = false;
        $result['data'] = '<p class="errorMsg">Fundraiser does not exist. Please enter correct join code.</p>';
        die(json_encode($result));
    }
} else {
    $result['success'] = false;
    $result['data'] = '<p class="errorMsg">Please insert correct JoinCode</p>';
    die(json_encode($result));
}
