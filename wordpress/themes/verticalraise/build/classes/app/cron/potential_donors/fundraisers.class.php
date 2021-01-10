<?php

namespace classes\app\cron\potential_donors;

class Fundraisers
{

    public function get() {
        
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
            endwhile;
        
        endif;
        
        return;

    }

    private function get_dates() {




        $current_date_start = '';
        $current_date_end   = '';

        $current_time = ''; //????????????????
    }
    
    private function get_start_date($fid) {
        $start_date = get_post_meta( $fid, 'start_date', true );
        
        return strtotime( $start_date );
    }
    
    private function get_end_date($fid) {
        $end_date   = get_post_meta( get_the_ID(), 'end_date', true );
        
        return strtotime( $end_date );
    }
    
    private function get_current_time() {
        return current_time( 'timestamp', 0 );
    }



}