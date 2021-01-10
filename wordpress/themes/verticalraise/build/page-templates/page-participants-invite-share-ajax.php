<?php 

/* Template Name: Participants invite Share Ajax */ 

/**
 * Load the Particpant Sharing Class
 */
load_class('participant_records.class.php');

/**
 * Instantiate the Participant Sharing Class
 */
$participant_records = new Participant_Sharing_Totals();

/**
 * Look for a successful sharing $_POST
 */
if ( $_POST['success'] == 1 ) {
    
    // Sharing or Donation?
    $source = ( !empty($_POST['source']) ) ? $_POST['source'] : '' ;
    
    if ( $source == 'invite' ) {
    
        // Grab the post params
        $post_id = $_POST['post_id'];
        $user_id = $_POST['user_id'];
        $type = ( !empty($_POST['type']) ) ? $_POST['type'] : '' ;

        // Facebook Share
        if ( $type == 'facebook' ) {
            $share_var = 'facebook_share';
            $share = 'facebook';
        }

        // Twitter Share
        if ( $type == 'twitter' ) {
            $share_var = 'twitter_share';
            $share = 'twitter';
        }

        // Retrieve the previous sharing details
        $previous_shares = json_decode(get_post_meta($post_id, $share_var, true), true);

        print_r($previous_shares);

        // No previous shares
        if ( empty($previous_shares) || empty($previous_shares['user_array']) ){

            $users['uid'] = $user_id;
            $users['total'] = 1;

            $just_shared['total'] = 1;
            $just_shared['user_array'][] = $users;

            // Store the new shares
            update_post_meta($post_id, $share_var, json_encode($just_shared));

            /**
             * Update/Insert the sharing record
             */
            if ( $source == 'invite' ) {
                $participant_records->adjust($post_id, $user_id, $share, 1);
            }
        } else {
            $flag = 0;

            // Increment shared by 1
            $just_shared['total'] = $previous_shares['total'] + 1;

            // Cycle through each of the previous users who shared
            foreach ( $previous_shares['user_array'] as $prev_share ) {

                // If this user exists previously
                if ( $prev_share['uid'] == $user_id ) {
                    $prev_share['uid'] = $prev_share['uid'];
                    $prev_share['total'] = $prev_share['total'] + 1;
                    $flag = 1;
                }
                $updated[] = $prev_share;

            }

            // No previous users were found
            if ( $flag == 0 ) {
                $new_user['uid'] = $user_id;
                $new_user['total'] = 1;
                $just_shared['user_array'] = $previous_shares['user_array'];
                $just_shared['user_array'][] = $new_user;
            } else {
                $just_shared['user_array'] = $updated;
            }

            // Store the updated shares
            update_post_meta($post_id, $share_var, json_encode($just_shared));

            /**
             * Update/Insert the sharing record
             */
            $participant_records->adjust($post_id, $user_id, $share, 1);
        }

        // Output results
        echo get_post_meta($post_id, $share_var, true);
    } else {
        echo "success";
    }
}