<?php

// Temporary code to insert data into shortcodes table
/*if ( isset($_GET['restore_number']) ) {

    // Target table name
    $target_table_name = "shorturls";

    // Set limit
    $limit = "";
    if ( isset($_GET['limit']) ) {
        $limit = "LIMIT " . $_GET['limit'];
    }

    if (isset($_GET['limit']) && isset($_GET['start'])) {
        $limit = "LIMIT " . $_GET['start'] . ", " . $_GET['limit'];;
    }

    // Set table name
    $tbl_prefix = "shorturls_";
    $tbl_suffix = $_GET['restore_number'];
    $table_name = $tbl_prefix . "restore" . $tbl_suffix;

    $x = 0;

    $results = $wpdb->get_results("SELECT * FROM `{$table_name}` {$limit}", ARRAY_A);

    $numResults = count($results);

    echo "results: {$numResults}<br>";

    if ( count($results) > 0) {
        foreach ( $results as $result ) {
            $check = $wpdb->get_results("SELECT * FROM `{$target_table_name}` where `code` = '{$result['code']}' LIMIT 1", ARRAY_A);
            if ( count($check) == 0 || empty($check) ) {
                // Insert the record
                $insert = $wpdb->insert($target_table_name,
                    array(
                        'code' => $result['code'],
                        'fid' => $result['fid'],
                        'uid' => $result['uid'],
                        'channel' => $result['channel'],
                        'parent' => $result['parent']
                    ),
                    array('%s', '%d', '%d', '%s', '%d')
                );

                // Return the results
                if ( empty($insert) ) {

                } else {
                    $x++;
                }
            }
        }
    }

    echo "{$x} records inserted";


}*/


/**
 * VerticalRaise Theme
 * Version 0.2.0
 */
define( 'THEME_VERSION', '0.2.0' );

/**
 * Get Server Type: Development or Production
 * @return boolean true = development, false = production
 */
function server_type() {
	$production_domain  = ['verticalraise.com'];
	$development_domain = ['verticalraise_stripe.test','local.wordpress.test', 'verticalraise-dev.com', 'verticalraise-loc.com'];
	$base_domain        = str_replace('https://', '', str_replace('http://', '', get_site_url()));

	return ( in_array( $base_domain, $development_domain, true ) ) ? 'dev' : 'prod';
}

define( '_SERVER_TYPE', server_type() );

/**
 * Get Local Dev
 * @return boolean true = local, false = non-local
 */
function is_local_dev() {
	$local_domain = ['verticalraise-loc.com'];

	$base_domain = str_replace( 'https://', '', str_replace( 'http://', '', get_site_url() ) );
	return in_array( $base_domain, $local_domain, true );
}

function get_cache_dir() {
    if ( _IS_LOCAL_DEV ) {
        return '/srv/www/wordpress-default/cache';
    } else {
        return '/var/www/cache';
    }
}

define( '_IS_LOCAL_DEV', is_local_dev() );


/**
 * Custom function to load a class.
 * @param string $class Class file
 */
function load_class( $class_file ) {
    include_once( get_template_directory() . '/classes/' . $class_file );
}

/**
 * Custom function to load a config.
 * @param string $config Config file
 */
function load_config( $config_file ) {
	include get_template_directory() . '/config/' . $config_file;
	return $config;
}

//require_once( __DIR__ . '/vendor/autoload.php' );

/**
 * Includes.
 */
include( get_template_directory() . '/config/config.php' );                                 // Set Configs
include( get_template_directory() . '/config/variables.php' );                                 // Set Variables
include( get_template_directory() . '/initialize/autoloader.php' );                         // Class autoloader
include( get_template_directory() . '/initialize/shorturls.php' );                          // Process shorturls
include( get_template_directory() . '/initialize/menus-sidebars.php' );                     // Initialize menus and sidebars
include( get_template_directory() . '/initialize/rewrites.php' );                           // Initialize rewrites
include( get_template_directory() . '/initialize/media.php' );                              // Initialize media
include( get_template_directory() . '/initialize/misc-functions.php' );                     // Initialize misc functions
//include( get_template_directory() . '/initialize/newrelic.php' );                           // Initialize new relic functionality
include( get_template_directory() . '/initialize/migrate.php' );                            // Initialize db migrations
include( get_template_directory() . '/initialize/cron-scheduler.php' );                     // Initialize cron schedule
include( get_template_directory() . '/initialize/admin-tools.php' );                        // Admin tools
include( get_template_directory() . '/initialize/admin-tools/admin_reports/ajaxlog.php' );  // Admin reports ajax
include( get_template_directory() . '/initialize/set-user-role.php' );                      // Set admin user role
include( get_template_directory() . '/initialize/ab-tests.php' );                           // Initialize AB Tests


/**
 * Classes.
 */
//include( get_template_directory() . '/classes/mail.class.php' );                 // Email related
include( get_template_directory() . '/classes/reports.class.php' );                // Reporting
include( get_template_directory() . '/classes/fundraiser_crud.class.php');         // Insert/Update Fundraisers
//include( get_template_directory() . '/classes/participant_records.class.php');   // Insert/Update Participant Fundraising Records
include( get_template_directory() . '/classes/fundraiser_participants.class.php'); // Fundraiser => Participant Records
include( get_template_directory() . '/classes/debug.class.php');                   // Debug

include( get_template_directory() . '/classes/migrate_potential_donors.php' );
include( get_template_directory() . '/classes/see_ending.php' );
include( get_template_directory() . '/classes/see_ending.php' );

function wpse158898_posts_clauses( $pieces, $query ) {

    global $wpdb;
    $relation           = isset($query->meta_query->relation) ? $query->meta_query->relation : 'AND';
    if ( $relation != 'OR' )
        return $pieces; // Only makes sense if OR.
    $prepare_args       = array ();
    $key_value_compares = array ();
    foreach ( $query->meta_query->queries as $key => $meta_query ) {
        if ( !is_array($meta_query) )
            continue;
        // Doesn't work for IN, NOT IN, BETWEEN, NOT BETWEEN, NOT EXISTS.
        if ( $meta_query['compare'] === 'EXISTS' ) {
            $key_value_compares[] = '(pm.meta_key = %s)';
            $prepare_args[]       = $meta_query['key'];
        } else {
            if ( !isset($meta_query['value']) || is_array($meta_query['value']) )
                return $pieces; // Bail if no value or is array.
            $key_value_compares[] = '(pm.meta_key = %s AND pm.meta_value ' . $meta_query['compare'] . ' %s)';
            $prepare_args[]       = $meta_query['key'];
            $prepare_args[]       = $meta_query['value'];
        }
    }
    $sql            = ' JOIN ' . $wpdb->postmeta . ' pm on pm.post_id = ' . $wpdb->posts . '.ID'
            . ' AND (' . implode(' ' . $relation . ' ', $key_value_compares) . ')';
    array_unshift($prepare_args, $sql);
    $pieces['join'] = call_user_func_array(array ($wpdb, 'prepare'), $prepare_args);
    // Zap postmeta clauses.
    $wheres         = explode("\n", $pieces['where']);
    foreach ( $wheres as &$where ) {
        $where = preg_replace(array (
            '/ +\( +' . $wpdb->postmeta . '\.meta_key .+\) *$/',
            '/ +\( +mt[0-9]+\.meta_key .+\) *$/',
            '/ +mt[0-9]+.meta_key = \'[^\']*\'/',
                ), '(1=1)', $where);
    }
    $pieces['where']   = implode('', $wheres);
    $pieces['orderby'] = str_replace($wpdb->postmeta, 'pm', $pieces['orderby']); // Sorting won't really work but at least make it not crap out.
    return $pieces;
}

add_filter( 'wpcf7_validate_tel*', array('classes\app\filters\Contact_Form_7', 'validate_tel') , 20, 2 );
add_action( 'send_email_to_participant', array('classes\app\donation\Donations_Notification', 'notify_participant') );
add_action( 'dbt_fundraiser_created', array('classes\app\fundraiser\Fundraiser_Details', 'add_fundraiser') );
add_action( 'dbt_fundraiser_updated', array('classes\app\fundraiser\Fundraiser_Details', 'update_fundraiser') );
add_filter( 'vr_format_tax_id', array('classes\app\filters\Tax_ID', 'format') );
add_filter( 'page_template_hierarchy', array('classes\app\filters\Page_Templates', 'add_subdir') );

add_action( 'wp_ajax_participant_select_subgroup', array('classes\app\fundraiser\Fundraiser_Subgroups', 'participant_select_subgroup'));
add_action( 'wp_ajax_fundraiser_delete_participant', array( 'classes\app\fundraiser\Fundraiser_Participants', 'delete_participant' ) );

add_action( 'wp_ajax_save_echeck_donation',  array( 'classes\app\donation\Donation', 'save_e_check_donation' ) );
add_action( 'wp_ajax_change_donor_name', array('classes\app\donation\Donation', 'change_donor_name'));
add_action( 'wp_ajax_get_f_participants', array('classes\app\donation\Donation', 'get_fundraiser_participants') );
add_action( 'wp_ajax_change_donation_participant', array( 'classes\app\donation\Donation', 'change_donation_recipient') );
add_action( 'wp_ajax_delete_donation',  array( 'classes\app\donation\Donation', 'delete_donation') );
add_action( 'pending_to_publish', array('classes\app\fundraiser\Sport_Scope_Integrated','on_publish_pending_fundraiser'), 10, 2 );
add_action( 'dbt_fundraiser_updated_to_ssi', array('classes\app\fundraiser\Sport_Scope_Integrated', 'on_edit_sport_scope_integrated_fundraiser') );

add_action( 'pending_to_publish', array('classes\app\amply\Amply','create_organization'), 10, 1 );

$_args_2 = array( false );
if ( ! wp_next_scheduled( 'wp_cron_sport_scope_17_days_left_check' , $_args_2) ) {
	wp_schedule_event(time(), 'daily', 'wp_cron_sport_scope_17_days_left_check', $_args_2 );
}
add_action( 'wp_cron_sport_scope_17_days_left_check', array('classes\app\fundraiser\Sport_Scope_Integrated', 'check_17_days_left'), 10, 1 );
add_action( 'init', 'check_pages_live' );
function check_pages_live() {
	if ( get_page_by_title( 'webhook' ) == null ) {
		$createPage = array(
			'post_title'   => 'webhook',
			'post_content' => '',
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_type'    => 'page',
			'post_name'    => 'webhook'
		);

		// Insert the post into the database
		wp_insert_post( $createPage );

		require_once( TEMPLATEPATH . '/stripe-php/config.php' );
		\Stripe\Stripe::setApiVersion( "2019-12-03" );

		$webhook = \Stripe\WebhookEndpoint::create( array(
			'url'            => get_bloginfo( 'url' ) . '/webhook',
			'enabled_events' => array(
				'checkout.session.completed',
				'payment_intent.succeeded',
			),
		) );

	}
}

add_action( 'init', 'check_pages_live2' );
function check_pages_live2() {
	if ( get_page_by_title( 'How-to-mail-a-check' ) === null ) {
		$create_page = array(
			'post_title'   => 'How-to-mail-a-check',
			'post_content' => '',
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_type'    => 'page',
			'post_name'    => 'how-to-mail-a-check'
		);

		// Insert the post into the database.
		wp_insert_post( $create_page );
	}
}

add_action( 'init', 'check_pages_live3' );
function check_pages_live3() {
	if ( get_page_by_title( 'Participant select subgroup' ) == null ) {
		$createPage = array(
			'post_title'   => 'Participant select subgroup',
			'post_content' => '',
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_type'    => 'page',
			'post_name'    => 'participant-select-subgroup'
		);

		// Insert the post into the database
		wp_insert_post( $createPage );
	}
}

use classes\models\tables\Reports_Fundraisers_Reference;


// Functions for Reports Fundraiser Reference Table
function update_fundraiser_name( $post_id ) {
  global $post;
  if ($post->post_type === 'fundraiser') {
    $reference = new Reports_Fundraisers_Reference();
    $reference->update($post_id, 'name', sanitize_text_field($_POST['post_title']));
  }
}



add_action('save_post', 'update_fundraiser_name', 10, 3);

function update_fundraiser_details( $post_id ) {
	$fundraiser = new classes\app\fundraiser\Fundraiser_Details();

	$fundraiser::update_fundraiser( $post_id );
}

add_action('acf/save_post', 'update_fundraiser_details'); // Work with ACF.
//add_action('post_updated', 'update_fundraiser_details', 10, 3);

function acf_update_start_date( $value, $post_id, $field  ) {
    $reference = new Reports_Fundraisers_Reference();
    $reference->update($post_id, 'start_date', $value);

	// return
    return $value;
}

function acf_update_end_date( $value, $post_id, $field  ) {
    $reference = new Reports_Fundraisers_Reference();
    $reference->update($post_id, 'end_date', $value);

	// return
    return $value;
}



// acf/update_value/name={$field_name} - filter for a specific field based on it's name
add_filter('acf/update_value/name=start_date', 'acf_update_start_date', 10, 3);
add_filter('acf/update_value/name=end_date', 'acf_update_end_date', 10, 3);


// acf/update_value - filter for every field
//add_filter('acf/update_value', 'my_acf_update_value', 10, 3);

// acf/update_value/type={$field_type} - filter for a specific field based on it's type
//add_filter('acf/update_value/type=select', 'my_acf_update_value', 10, 3);

// acf/update_value/name={$field_name} - filter for a specific field based on it's name
//add_filter('acf/update_value/name=my_select', 'my_acf_update_value', 10, 3);

// acf/update_value/key={$field_key} - filter for a specific field based on it's name
//add_filter('acf/update_value/key=field_508a263b40457', 'my_acf_update_value', 10, 3);


add_action( 'wp_ajax_check_fundraiser_name', array('classes\app\fundraiser\Fundraiser_Details', 'check_name'));


/**
 * Process to run to repull/process Fundraisers into Amply.
 */
use classes\app\amply\Amply;

if ( isset( $_GET['process_amply'] ) && $_GET['process_amply'] == 'true' && is_user_logged_in() ) {

	$offset = ( !empty( $_GET['offset'] ) ) ? $_GET['offset'] : 0;

	$args = array(
        'post_type' => 'fundraiser',
        'post_status' => 'publish',
        'posts_per_page' => 30,
        'orderby' => 'publish_date',
		'order' => 'DESC',
		'offset' => $offset
	);

	global $post;

    $amply_posts = get_posts( $args );

    if ( $amply_posts ) {
		$x = 1;
        foreach ( $amply_posts as $amply_post ) {
			echo "{$x}: f_id: {$amply_post->ID} ";
			Amply::create_organization($amply_post, true);
			$x++;
		}
        wp_reset_postdata();
	}

	exit();

}
