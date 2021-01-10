<?php

use \classes\models\tables\Email_Input;

$email_input = new Email_Input();

// Migrate
$args = array(
    'post_type'      => 'fundraiser',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
);

$fundraiser_query = new WP_Query($args);

$f_count = 0;

if ( $fundraiser_query->have_posts() ) :

    while ( $fundraiser_query->have_posts() ) : $fundraiser_query->the_post();

        $fid = get_the_ID();

        $potential_donors = json_decode(get_post_meta($fid, 'potential_donors_array', true));

        if ( !empty( $potential_donors ) ) {

            $f_count++;

            foreach ( $potential_donors as $pd ) {
                
                $uid   = $pd[0]; // uid
                $email = $pd[1]; // email
                
                if ( empty($email_input->get_by_email_fid_uid($fid, $uid, $email)) ) {
                    $email_input->insert($email, $uid, $fid, 2);
                }


            }
            
        }

    endwhile;

endif;