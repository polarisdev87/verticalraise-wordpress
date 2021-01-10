<?php


class Potential_Donors_Cron extends Cron_Email
{
    
    public function __construct() {
        $this->intervals = [
            '+1 day', '+2 days', '-3 days', '+7 days', '+14 days'
        ];
    }
    
    public function run_all() {
        foreach ( $this->intervals as $interval ) {
            $this->run($interval);
        }
    }

    public function run($type) {
        
        if ( !in_array($type, $this->intervals) ) {
            return "interval not in intervals array";
        }

        // Array of fundraisers
        $fundraisers = $this->process_fundraisers($type);
        
        $this->process($fundraisers, $type);
    }
    
    private function process($fundraisers, $type) {
        
        $subject = $this->get_subject($type);
        
        // for each fundraiser
        foreach ( $fundraisers as $key => $fundraiser ) {
            $potential_donors = $this->get_potential_donors($key);
            if ( $potential_donors != false ) {
                foreach ( $potential_donors as $p_donor ) {
                    try{
                        $this->custom_mail->send();
                    } catch(Exception $e) {
                        // new relic exception
                    }
                }
            }
        }
    }
    
    private function get_fundraisers($type) {
        
        $fundraisers = array();
        
        $time_constraints = $this->get_time_constraints($type);

        // Get the list of fundraisers
        // Query to get fundraisers by constraint
        
        $args = array(
            'post_type'        => 'fundraiser',
            'post_status'      => 'publish',
            'posts_per_page'   => 5,
            'offset'           => 0,
            'category'         => '',
            'category_name'    => '',
            'orderby'          => 'date',
            'order'            => 'DESC',
            'include'          => '',
            'exclude'          => '',
            'meta_key'         => '',
            'meta_value'       => '',
            
            'post_mime_type'   => '',
            'post_parent'      => '',
            'author'	       => '',
            'author_name'	   => '',
            
            'suppress_filters' => true 
        );
        
        // The Query
        $query = new WP_Query( $args );

        // The Loop
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $fundraisers[get_the_ID()] = get_the_title();
            }
            wp_reset_postdata();
        } else {
            return "no fundraisers found";
        }
        
        // return
        return $fundraisers;

    }
    
    /**
     * Get time constraints.
     */
    private function get_time_constraints($type) {
        $current_time = current_time( 'timestamp', 0 );
        
        if ( $type == "+1 day" ) {
            return strtotime(current_time("Ymd"));
        }

        return strtotime(date("Ymd", strtotime($type, $current_time)));

    }
    
    /**
     *
     */
    private function get_subject($type) {
        
        switch($type) {
            case "+1 day":
                $subject = "Please support my Fundraiser";
                break;
            case "-3 days":
                $subject = "Please support the [TITLE]";
                break;
            case "+14 days":
                $subject = "Only two weeks left for my [TITLE]";
                break;
            case "+7 days":
                $subject = "Only one week left for my [TITLE]";
                break;
            case "+2 days":
                $subject = "Only one day left for my [TITLE]";
                break;   
        }
                
        return $subject;
    }
    
    private function format_subject($line, $title) {
        return str_replace('[TITLE]', $title);
    }
    
    private function prep_email() {
        
    }

}