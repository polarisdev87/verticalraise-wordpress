<?php

use \classes\models\tables\Email_Input;
use \classes\models\tables\Potential_Donors_Email;

$email_input = new Email_Input();
$potential_donors_email = new Potential_Donors_Email();

if ( isset($_GET["migrate_pd"]) && $_GET["migrate_pd"] == 'migrate_pd' ) { 
    
    ini_set('memory_limit', '-1');
    
    $offset = ( empty( $_GET['offset']) ) ? 0 : $_GET['offset'];

    // Migrate
    $args = array(
        'post_type'      => 'fundraiser',
        'post_status'    => 'publish',
        'post_count'     => 1000,
        'posts_per_page' => 1000,
        //'offset'         => $offset,
        // Date
        'date_query'    => array(
            'column'  => 'post_date',
            'after'   => '- 90 days'
        )
    );

    $fundraiser_query = new WP_Query($args);

    if ( $fundraiser_query->have_posts() ) :

        while ( $fundraiser_query->have_posts() ) : $fundraiser_query->the_post();

            $fid = get_the_ID();
            echo get_the_title();
            echo "<br>";

            $potential_donors = json_decode(get_post_meta($fid, 'potential_donors_array', true));

            if ( !empty( $potential_donors ) ) {

                foreach ( $potential_donors as $pd ) {

                    $uid   = $pd[0]; // uid
                    $email = $pd[1]; // email

                    // Add to email input
                    /*if ( empty($email_input->get_by_email_fid_uid($fid, $uid, $email)) ) {
                        $email_input->insert($email, $uid, $fid, 2);
                    }*/
                    
                    // Add to potential donors table
                    if ( empty($potential_donors_email->get_by_email_uid_fid($email, $uid, $fid)) ) {
                        $potential_donors_email->insert($email, $uid, $fid, 2);
                    }
                    

                    
                }

            }

        endwhile;

    endif;
    
}