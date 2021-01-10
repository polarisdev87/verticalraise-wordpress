<?php

use \classes\models\mixed\Post_Author;
use \classes\models\tables\Secondary_Admins;
use \classes\models\tables\Fundraiser_Participants;

// Post author's fundraisers
$post_author = new Post_Author();
$author_fids = $post_author->get_posts_by_author_id($user_ID);

// Secondary admin's fundraisers
$s_admins = new Secondary_Admins();
$s_admin_ids = $s_admins->get_fids_by_sadmin_id($user_ID);

// Participant's fundraisers
$participants = new Fundraiser_Participants();
$my_fundraisers = $participants->get_fundraiser_ids_by_userid($user_ID);

if ( empty($my_fundraisers) ) {
    $my_fundraisers = array();
}

$authority_ids = array();
$authority_ids = array_merge($author_fids, $authority_ids);
$authority_ids = array_merge($s_admin_ids, $authority_ids);

$my_fundraisers = array_merge($my_fundraisers, $authority_ids);
$my_fundraisers = array_unique($my_fundraisers);

$f_count = count($my_fundraisers);

?>
<div class="acc_nav_wrap">
    <div class="acc_nav">
    <?php wp_nav_menu(array('theme_location' => 'myacc')); ?>
        <h3>Your Fundraisers</h3>
    <?php
        
        // 3 Brackets of Fundraisers
        $fundraisers['upcoming'] = array();
        $fundraisers['current']  = array();
        $fundraisers['past']     = array();

        if ( !empty($my_fundraisers) ) {

            $args = array(
                'post_type'              => 'fundraiser',
                'post_status'            => array('publish', 'pending'),
                'posts_per_page'         => $f_count,
                'post_count'             => $f_count,
                'post__in'               => $my_fundraisers,
                'no_found_rows'          => true,
                'update_post_term_cache' => false,
                //'fields' => 'ids',
            );
            $fundraiser_query = new WP_Query($args);

            $fundraiser_query->post_count = count($fundraiser_query->posts);
            
            if ( $fundraiser_query->have_posts() ) :
    


                // Loop through the fundraisers
                while ( $fundraiser_query->have_posts() ) : $fundraiser_query->the_post();

                    // Get date info
                    $format_in    = 'Ymd';

                    $start_date   = get_post_meta(get_the_ID(), 'start_date', true);
                    $start_date   = DateTime::createFromFormat($format_in, $start_date);

                    $end_date     = get_post_meta(get_the_ID(), 'end_date', true);
                    $end_date     = DateTime::createFromFormat($format_in, $end_date);

                    $current_date = new DateTime();

                    // The fundraiser is upcoming
                    if ( $current_date < $start_date ) {
                        $fundraisers['upcoming'][] = get_the_ID();
                    }
                    // The fundraiser is current
                    if ( $current_date >= $start_date && $current_date < $end_date ) {
                        $fundraisers['current'][] = get_the_ID();
                    }
                    // The fundraiser is old
                    if ( $current_date >= $end_date ) {
                        $fundraisers['past'][] = get_the_ID();
                    }

                endwhile;
        endif;    
            
    }
        
    $icons = [
        'upcoming' => 'fa fa-fast-forward',
        'current'  => 'fa fa-check-circle',
        'past'     => 'fa fa-fast-backward'
    ];
                            
    ?>
        <ul>
            <?php 
            
            // Iterate out the list of fundraisers the user is attached to
            foreach ( $fundraisers as $key => $fundraiser) { 
            
            ?>

            <li><a href="javascript: void(0);"><i class="<?php echo $icons[$key];?>"></i><?php echo ucwords($key);?> </a>
                <?php if ( !empty($fundraiser) ) { ?>
                    <ul>
                        <?php foreach ( $fundraiser as $f ) {
                                
                                if ( in_array($f, $authority_ids) ) { ?>
                        <li><a href="<?php bloginfo('url'); ?>/single-fundraiser/?fundraiser_id=<?php echo $f; ?>"><?php echo get_the_title($f); ?></a></li>
                        <?php } else { ?>
                        <li><a href="<?php bloginfo('url'); ?>/participant-fundraiser/?fundraiser_id=<?php echo $f; ?>"><?php echo get_the_title($f); ?></a></li>
                        
                        <?php } 
                            } ?>
                    </ul>
                <?php } ?>
            </li>
    
            <?php } ?>

        </ul>
    <?php
        wp_reset_postdata();
    ?>
</div>
</div>
<script>
    jQuery(document).ready(function() {
        jQuery('.matchheight').matchHeight();
    });
</script>