<?php

/**
 * Admin tools
 */

if ( ! defined( 'ABSPATH' ) ) exit; 

if ( is_admin() ) {
    include( get_template_directory() . '/initialize/admin-tools/edit-log.php' );
    include( get_template_directory() . '/initialize/admin-tools/database-upgrade.php' );
    include( get_template_directory() . '/initialize/admin-tools/email-queue.php' );
    include( get_template_directory() . '/initialize/admin-tools/system-configuration.php' );
    include( get_template_directory() . '/initialize/admin-tools/add-rep-landing-page.php' );
    include( get_template_directory() . '/initialize/admin-tools/admin_reports/reports.php' );
    include( get_template_directory() . '/initialize/admin-tools/refunds.php');
	include( get_template_directory() . '/initialize/admin-tools/echeck.php');
	include( get_template_directory() . '/initialize/admin-tools/connect-accounts.php');

}