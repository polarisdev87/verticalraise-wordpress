<?php

/** 
 * Process Crons.
 */
function run_crons() {
    
    // If development server, do not run
    if ( _SERVER_TYPE == 'dev' ) return true;
    
    /** 
     * Cron includes.
     */
    include_once( TEMPLATEPATH . "/crons/potential-donors.php" );
    include_once( TEMPLATEPATH . "/crons/fundraisers-ended.php" ); 
    include_once( TEMPLATEPATH . "/crons/fundraisers-ended-coach.php" );
    include_once( TEMPLATEPATH . "/crons/low-participation.php" );
    include_once( TEMPLATEPATH . "/crons/low-participants.php" );
    
    /**
     * Emails to potential donors.
     */
    process_expiry(2); // 2 day
    process_expiry(7); // 7 days
    process_expiry(14); // 14 days
    
    /**
     * Emails to site admin of ended fundraisers.
     */
    process_ended_admin();
    
    /**
     * Emails to admin and coach of ended fundraisers.
     */
    process_ended_coach();
    
    /**
     * Emails to coaches showing them low participation.
     */
    process_low_participation();
    
    /**
     * Emails to coaches showing them low participation.
     */
    process_low_participants();
    
}

/**
 * Set the WP Cron Scheduled Event.
 */
if ( ! wp_next_scheduled( 'process_emails' ) ) {
  wp_schedule_event( 1505491200, 'daily', 'process_emails' ); // 9 AM PST
}

add_action( 'process_emails', 'process_crons' );

function process_crons() {
  run_crons();
}

function test_crons() {
    
    include_once( TEMPLATEPATH . "/crons/potential-donors.php" );
    include_once( TEMPLATEPATH . "/crons/fundraisers-ended.php" ); 
    include_once( TEMPLATEPATH . "/crons/fundraisers-ended-coach.php" );
    include_once( TEMPLATEPATH . "/crons/low-participation.php" );
    include_once( TEMPLATEPATH . "/crons/low-participants.php" );
    
    /**
     * Emails to coaches showing them low participation.
     */
    process_low_participation();
    
    /**
     * Emails to coaches showing them low participation.
     */
    process_low_participants();
    
    /**
     * Emails to site admin of ended fundraisers.
     */
    process_ended_admin();
    
    /**
     * Emails to admin and coach of ended fundraisers.
     */
    process_ended_coach();

}

//test_crons();