<?php



if ( isset($_GET["see_ending"]) && $_GET["see_ending"] == 'see_ending' ) { 
    
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
    
    echo "see_ending:<br>";
    
    $fundraiser_query = new WP_Query($args);

    if ( $fundraiser_query->have_posts() ) :

        while ( $fundraiser_query->have_posts() ) : $fundraiser_query->the_post();

            $fid = get_the_ID();
            echo get_the_title();
            $end_date = get_post_meta( get_the_ID(), 'end_date', true );
            echo " ";
            echo $end_date;
            echo "<br>";

        endwhile;

    endif;
    
    exit();
    
}