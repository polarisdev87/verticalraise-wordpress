<?php

namespace classes\models\mixed;

class Post_Author
{
    
    public function get_posts_by_author_id($uid) {

        $args = array(
            'post_type'              => 'fundraiser',
            'post_status'            => array('publish', 'pending'),
            'no_found_rows'          => true,
            //'update_post_term_cache' => false,
            'author'                 => $uid,
            'fields'                 => 'ids',
            'posts_per_page'         => -1
        );

        $fundraiser_query = new \WP_Query($args);
        
        $post_ids = array();

        if ( $fundraiser_query->have_posts() ) :
        
            // Loop through the fundraisers
            while ( $fundraiser_query->have_posts() ) : $fundraiser_query->the_post();

                $post_ids[] = get_the_ID();
        
            endwhile;

        endif;
        
        return $post_ids;
        
    }
    
}