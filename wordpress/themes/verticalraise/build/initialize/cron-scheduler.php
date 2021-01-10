<?php


/**
 * Custom Schedules for the email queue system
 */
function add_1_minute_interval( $schedules ) {

    $schedules['every_1_minute'] = array(
            'interval'  => 60,
            'display'   => __( 'Every 1 Minute', 'textdomain' )
    );

    return $schedules;
}
add_filter( 'cron_schedules', 'add_1_minute_interval' );

function add_2_minute_interval( $schedules ) {

    $schedules['every_2_minutes'] = array(
            'interval'  => 120,
            'display'   => __( 'Every 2 Minutes', 'textdomain' )
    );

    return $schedules;
}
add_filter( 'cron_schedules', 'add_2_minute_interval' );

function add_5_minute_interval( $schedules ) {

    $schedules['every_5_minutes'] = array(
            'interval'  => 300,
            'display'   => __( 'Every 5 Minutes', 'textdomain' )
    );

    return $schedules;
}
add_filter( 'cron_schedules', 'add_5_minute_interval' );

/**
 * Process Crons
 */

/**
 * The cron that runs at 9:00 AM PST.
 */
function run_crons_9_am() {
    if ( _SERVER_TYPE == 'dev' ) return false;

    if ( time_delimiter() == false ) {
        die("Cannot run");
    }

    include_once( TEMPLATEPATH . "/crons/potential-donors.php" );

    /**
     * Emails to potential donors (2).
     */
    $process_expiry = new Process_Expiry();

    $process_expiry->process_expiry(2); // 2 days

}

/**
 * The cron that runs at 9:05 AM PST.
 */
function run_crons_9_05_am() {
    if ( _SERVER_TYPE == 'dev' ) return false;

    if ( time_delimiter() == false ) {
        die("Cannot run");
    }

    include_once( TEMPLATEPATH . "/crons/potential-donors.php" );

    /**
     * Emails to potential donors.
     */
    $process_expiry = new Process_Expiry();

    $process_expiry->process_expiry(7); // 7 days

}

/**
 * The cron that runs at 9:10 AM PST.
 */
function run_crons_9_10_am() {
    if ( _SERVER_TYPE == 'dev' ) return false;

    if ( time_delimiter() == false ) {
        die("Cannot run");
    }

    include_once( TEMPLATEPATH . "/crons/potential-donors.php" );

    /**
     * Emails to potential donors.
     */
    $process_expiry = new Process_Expiry();

    $process_expiry->process_expiry(14); // 14 days

}

/**
 * The cron that runs at 9:15 AM PST.
 */
function run_crons_9_15_am() {
    if ( _SERVER_TYPE == 'dev' ) return false;

    if ( time_delimiter() == false ) {
        die("Cannot run");
    }
    include_once( TEMPLATEPATH . "/crons/fundraisers-ended-coach.php" );

    /**
     * Emails to admin and coach of ended fundraisers.
     */

    process_ended_coach();

}

/**
 * The cron that runs at 9:20 AM PST.
 */
function run_crons_9_20_am() {
    if ( _SERVER_TYPE == 'dev' ) return false;

    if ( time_delimiter() == false ) {
        die("Cannot run");
    }

    include_once( TEMPLATEPATH . "/crons/low-participants.php" );
    include_once( TEMPLATEPATH . "/crons/low-participation.php" );
    include_once( TEMPLATEPATH . "/crons/resend-parent-share.php" );

    /**
     * Emails to coaches showing them low participation.
     */
    process_low_participation(10);
    process_low_participation(17);
    process_low_participation(19);


    /**
     * Emails to participants who have low participation.
     */
    process_low_participants(10);
    //resend_parent_share(17);
    process_low_participants(19);

}



/**
 * Crons that run every minute.
 */

use \classes\app\email_queue\Cron;

// Verify the emails in the email queue
function run_email_verify() {
    $cron = new Cron();
    $cron->run('verify');
}

// Send the emails
function run_email_send() {
    $cron = new Cron();
    $cron->run('send');
}

// Move the emails from `email_queue` table to `potential_donors_email` table if criteria is met
function run_email_move() {
    $cron = new Cron();
    $cron->run('move');
}

// Remove the unverified emails and moved emails from `email_queue` table to `potential_donors_email` table if criteria is met
//function run_email_clear() {
//    $cron = new Cron();
//    $cron->run('clear');
//}

// Heartbeat
use \classes\app\heartbeat\Heartbeat;

function run_heartbeat() {
    $heartbeat = new Heartbeat();
    $heartbeat->run();
}


//Delete any donations older than 1 year from donations table.
//use \classes\app\donation\Donations_Cron;
//
//function run_delete_old_donations () {
//    $delete_donations = new Donations_Cron();
//    $delete_donations->run();
//}

//Delete shortcodes older than 6 months from shorturls table.
use \classes\app\shorturls\Shorturl_Cron;

function run_delete_shortcodes () {
    //$delete_shortcodes = new Shorturl_Cron();
    //$delete_shortcodes->run();
}

//Delete older taan 2 months sendgrid_log table
use \classes\app\emails\sendgrid_log;

function run_delete_old_sendgrid_logs () {
    $delete_logs = new SendGrid_Log();
    $delete_logs->delete_logs();
}

/**
 * Set the WP Cron Scheduled Events.
 */

/************** 9:00 AM ***************/
if ( ! wp_next_scheduled( 'process_emails_9_am' ) ) {
  wp_schedule_event( 1520956811, 'daily', 'process_emails_9_am' );
}
add_action( 'process_emails_9_am', 'run_crons_9_am' );

/************** 9:05 AM ***************/
if ( ! wp_next_scheduled( 'process_emails_9_05_am' ) ) {
  wp_schedule_event( 1509725100, 'daily', 'process_emails_9_05_am' );
}
add_action( 'process_emails_9_05_am', 'run_crons_9_05_am' );

/************** 9:10 AM ***************/
if ( ! wp_next_scheduled( 'process_emails_9_10_am' ) ) {
 wp_schedule_event( 1509725400, 'daily', 'process_emails_9_10_am' );
}
add_action( 'process_emails_9_10_am', 'run_crons_9_10_am' );

/************** 9:15 AM ***************/
if ( ! wp_next_scheduled( 'process_emails_9_15_am' ) ) {
  wp_schedule_event( 1509725700, 'daily', 'process_emails_9_15_am' );
}
add_action( 'process_emails_9_15_am', 'run_crons_9_15_am' );

/************** 9:20 AM ***************/
if ( ! wp_next_scheduled( 'process_emails_9_20_am' ) ) {
  wp_schedule_event( 1509726000, 'daily', 'process_emails_9_20_am' );
}
add_action( 'process_emails_9_20_am', 'run_crons_9_20_am' );

/************** 1:00 AM ***************/
if ( ! wp_next_scheduled( 'run_delete_old_sendgrid_logs' ) ) {
    wp_schedule_event( 1509696000, 'daily', 'run_delete_old_sendgrid_logs' );
  }
add_action( 'run_delete_old_sendgrid_logs', 'run_delete_old_sendgrid_logs' );

/************** 1:30 AM ***************/
if ( ! wp_next_scheduled( 'run_delete_shortcodes' ) ) {
  wp_schedule_event( 1511496000, 'daily', 'run_delete_shortcodes' );
}
add_action( 'run_delete_shortcodes', 'run_delete_shortcodes' );

/************** 9:30 AM (delete donations older than 1 year) ***************/
//if ( ! wp_next_scheduled( 'run_delete_old_donations' ) ) {
//  wp_schedule_event( 1509726600, 'daily', 'run_delete_old_donations' );
//}
//add_action( 'run_delete_old_donations', 'run_delete_old_donations' );



/************** Every 2 Minutes ***************/
if ( ! wp_next_scheduled( 'run_email_verify' ) ) {
  wp_schedule_event( time(), 'every_2_minutes', 'run_email_verify' );
}
add_action( 'run_email_verify', 'run_email_verify' );

//if ( ! wp_next_scheduled( 'run_email_clear' ) ) {
//  wp_schedule_event( time(), 'every_5_minutes', 'run_email_clear' );
//}
//add_action( 'run_email_clear', 'run_email_clear' );

/************** Every 5 Minutes ***************/
if ( ! wp_next_scheduled( 'run_email_send' ) ) {
 wp_schedule_event( time(), 'every_5_minutes', 'run_email_send' );
}
add_action( 'run_email_send', 'run_email_send' );

if ( ! wp_next_scheduled( 'run_heartbeat' ) ) {
  wp_schedule_event( time(), 'every_5_minutes', 'run_heartbeat' );
}
add_action( 'run_heartbeat', 'run_heartbeat' );

/************** Every Minute ***************/
if ( ! wp_next_scheduled( 'run_email_move' ) ) {
  wp_schedule_event( time(), 'every_1_minute', 'run_email_move' );
}
add_action( 'run_email_move', 'run_email_move' );

/************** Every Hour ***************/
if ( ! wp_next_scheduled( 'transfer_connect_funds_task' ) ) {
    wp_schedule_event( time(), 'hourly', 'transfer_connect_funds_task' );
}
add_action( 'transfer_connect_funds_task', 'classes\app\fundraiser\transfer_connect_funds' );

/************** Every Hour ***************/
// Turn off this cron since we handle automatically in Stripe now
/* if ( ! wp_next_scheduled( 'payout_connect_funds_task' ) ) {
	wp_schedule_event( time(), 'hourly', 'payout_connect_funds_task' );
}
add_action( 'payout_connect_funds_task', 'classes\app\fundraiser\payout_connect_funds' );*/


/**
 * Time Delimiter.
 */
function time_delimiter() {

    // Current timestamp
    /*$current_time = current_time( 'timestamp', 0 );

    // Check to see if the current time is between 8:55 AM PST and 10:00 AM PST
    if ( $current_time < strtotime( "8:55am", $current_time ) || $current_time > strtotime( "10am", $current_time  ) ) {
        return false;
    } else {
        return true;
    }*/

    return true;

}

if ( isset($_GET['runcron']) && $_GET['runcron'] == 'runcron' ) {

    ini_set( 'memory_limit', '-1' );
    ini_set( 'max_execution_time', 0);

    /**
     * Emails to potential donors (2).
     */
    include_once( TEMPLATEPATH . "/crons/potential-donors.php" );

    $process_expiry = new Process_Expiry();
    $process_expiry->process_expiry(2); // 2 days
    $process_expiry->process_expiry(7);
    $process_expiry->process_expiry(14);

    include_once( TEMPLATEPATH . "/crons/fundraisers-ended.php" );
    include_once( TEMPLATEPATH . "/crons/fundraisers-ended-coach.php" );

    /**
     * Emails to site admin of ended fundraisers.
     */
    process_ended_admin();

    /**
     * Emails to admin and coach of ended fundraisers.
     */
    process_ended_coach();

    include_once( TEMPLATEPATH . "/crons/low-participants.php" );
    include_once( TEMPLATEPATH . "/crons/low-participation.php" );
    include_once( TEMPLATEPATH . "/crons/resend-parent-share.php" );

    /**
     * Emails to coaches showing them low participation.
     */
    process_low_participation(10);
    process_low_participation(17);
    process_low_participation(19);

    /**
     * Emails to participants who have low participation.
     */
    process_low_participants(10);
    //resend_parent_share(17);
    process_low_participants(19);


    run_email_verify();
    run_email_send();
    run_email_move();
//  run_email_clear();
//  run_delete_old_donations();
	\classes\app\fundraiser\transfer_connect_funds();
    exit();
}

if ( isset($_GET['cron_email']) && $_GET['cron_email'] == 'cron_email' ) {
    run_email_verify();
    run_email_send();
    run_email_move();
    exit();
}

//test shorturl cron
if (isset($_GET['shortcode']) && $_GET['shortcode'] == 'run') {
    run_delete_shortcodes();
}

function test_crons() {

    include_once( TEMPLATEPATH . "/crons/potential-donors.php" );
    include_once( TEMPLATEPATH . "/crons/fundraisers-ended.php" );
    include_once( TEMPLATEPATH . "/crons/fundraisers-ended-coach.php" );
    include_once( TEMPLATEPATH . "/crons/low-participation.php" );
    include_once( TEMPLATEPATH . "/crons/low-participants.php" );
    $process_expiry = new Process_Expiry();
    $process_expiry->process_expiry(2); // 2 days
    $process_expiry->process_expiry(7);
//    $process_expiry->process_expiry(14);

    /**
     * Emails to coaches showing them low participation.
     */
    //process_low_participation();

    /**
     * Emails to coaches showing them low participation.
     */
    //process_low_participants();

    /**
     * Emails to site admin of ended fundraisers.
     */
    //process_ended_admin();

    /**
     * Emails to admin and coach of ended fundraisers.
     */
    //process_ended_coach();

}

//test_crons();

//run_email_verify();
//run_email_send();
//run_email_move();
//run_email_clear();
//run_delete_old_donations();
