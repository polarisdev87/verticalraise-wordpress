<?php

/**
 * Create Reports menu and submenus to display fundraiser ended and run crons.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( is_admin() ) {
    add_action( 'admin_menu', 'admin_reports' );
}

function admin_reports() {
        add_menu_page('Reports', 'Reports', 'manage_options', 'fundraisers_ended', 'fundraisers_ended','dashicons-book-alt', 3);
        add_submenu_page('fundraisers_ended', 'Fundraisers Ended', 'Fundraisers Ended', 'manage_options', 'fundraisers_ended' );
        add_submenu_page('fundraisers_ended', 'Fundraisers Started', 'Fundraisers Started', 'manage_options','fundraisers_started','fundraisers_started' );
        add_submenu_page('fundraisers_ended', 'Fundraisers', 'Fundraisers', 'manage_options','fundraisers','fundraisers_page' );
        add_submenu_page('fundraisers_ended', 'Crons', 'Crons', 'manage_options','crons','crons_page' );
        add_submenu_page('fundraisers_ended', 'Fundraisers Reports', 'Fundraisers Reports', 'manage_options','fundraisers_reports','fundraisers_reports_page' );
		add_submenu_page('fundraisers_ended', 'Secondary End Date', 'Secondary End Date', 'manage_options', 'secondary_end_date', 'secondary_end_date');
}

function fundraisers_ended() {
    include( get_template_directory() . '/initialize/admin-tools/admin_reports/fundraiser_ended.php' );   
}

function crons_page() {
    include( get_template_directory() . '/initialize/admin-tools/admin_reports/crons.php' ); 
}
    
function fundraisers_page () {
    include( get_template_directory() . '/initialize/admin-tools/admin_reports/fundraisers.php' );   
}

function fundraisers_reports_page () {
    include( get_template_directory() . '/initialize/admin-tools/admin_reports/fundraisers_reports.php' );
}

function fundraisers_started () {
    include( get_template_directory() . '/initialize/admin-tools/admin_reports/fundraisers_started.php' );
}

function secondary_end_date() {
	include( get_template_directory() . '/initialize/admin-tools/admin_reports/secondary_end_date.php' );
}
