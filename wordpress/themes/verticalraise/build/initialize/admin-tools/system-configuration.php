<?php

use \classes\app\system_configuration\System_Configuration;
use \classes\app\System_configuration\Cloudflare;
use \classes\app\System_configuration\Constants;

/**
 * Page to see system configuration
 */
if ( !defined( 'ABSPATH' ) )
    exit;

if ( is_admin() ) {
    add_action( 'admin_menu', 'system_configuration_menu' );
}

function system_configuration_menu() {
    add_menu_page( 'System Configuration', 'System Configuration', 'manage_options', 'system-configuration', 'system_configuration_page', '
dashicons-admin-generic', 3 );
}

function system_configuration_page() {

    if ( !current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    $system = new System_Configuration();

    echo "<style>";
    echo ".wrap {margin: 10px 20px 0 2px;}";
    echo "table.system_status_table {margin-bottom: 1em;}";
    echo "table.system_status_table h3 {font-size: 14px; margin: 0;}";
    echo "table.system_status_table td:first-child {width: 33%;}";
    echo ".widefat * {word-wrap: break-word;}";
    echo ".expected{margin: 0;color: #7ad03a !important;}";
    echo ".unexpected{margin: 0;color: red !important;}";
    echo ".checkmark.unexpected{visibility:hidden}";
    echo ".checkmark{margin-left: -14px;}";
    echo "span.value{margin-right: 20px;}";
    echo "#email_settings .checkmark {
            margin-left: 0px;
        }";
    echo "table tr:nth-child(2n) td, table tr:nth-child(2n) th {
            background: #fcfcfc;
        }";
    echo ".nav-tab{
            margin-left:0
        }
        .nav-tabs li a.nav-tab {
            border-left: none !important;
        }
        .nav-tabs li a.nav-tab.errors {
            color:red;
        }
        .nav-tabs li.all a.nav-tab {
            border-left: 1px solid #ccc !important;
        }
        .nav-tabs li{
            position : relative;
        }
        .nav-tabs li span {
            display: inline-block;
            vertical-align: top;
            margin: 1px 0 0 2px;
            padding: 0 5px;
            min-width: 7px;
            height: 17px;
            border-radius: 50%;
            background-color: #f00; 
            border: solid 1px red;
            color: #fff;
            font-size: 9px;
            line-height: 17px;
            text-align: center;
            z-index: 26;
        }
        .nav-tab:focus{
            box-shadow: none !important;
        }
        .nav-tab.nav-tab-active{
            border-bottom: 1px solid #ffffff !important;
            background: #ffffff !important;
        }
        ";
    echo "
        
        table.ssl_verification, table.dns_records {
            width:100% !important;
            border: solid 1px #eeeeee;
        }
        
        table.ssl_verification tr td, table.dns_records tr td{
            width: auto !important;
            word-break: break-word;
        }       
        table.ssl_verification tr td:first-child, table.dns_records tr td:first-child,
        table.dns_records tr td:last-child{
            width: 12% !important;
        }
        ";
    echo "</style>";

    echo "<script>
        jQuery(function(){
            jQuery('.nav.nav-tabs li').click(function(e){
                e.preventDefault();
                var type = jQuery(this).attr('tab_type')
                var id = jQuery(this).find('a').attr('tab-id')
                jQuery('.nav.nav-tabs li').removeClass('active')
                jQuery('.nav.nav-tabs li a').removeClass('nav-tab-active')
                jQuery(this).addClass('active');
                jQuery(this).find('a').addClass('nav-tab-active');
                if(type == 'all') {
                    jQuery('.tab-content .tab-pane').show();
                } else {
                    jQuery('.tab-content .tab-pane').hide();
                    jQuery('#'+id).show();
                }
            })
            
            jQuery(function(){
                jQuery('.tab-pane').each(function(){
                    var tabId = jQuery(this).attr('id');
                    console.log(tabId);
                    var Obj = jQuery(this);
                    var count = 0;
                    Obj.find('table tr').each(function(){
                        
                        if (jQuery(this).find('span').hasClass('unexpected')){
                            count++;                            
                        }
                    })
                    
                    if (count>0){
                        jQuery('li a[tab-id='+tabId+']').addClass('errors')
                        jQuery('li a[tab-id='+tabId+']').append(jQuery('<span>'+count+'</span>'))
                    }
                })
            })
        })
        </script>";
    echo "<div class='wrap'>";

    echo "<h2>System Configuration</h2>";

    echo "<hr>";

    echo "<ul class='nav nav-tabs'>"
    . "<li class='all active' tab_type='all'><a class='nav-tab nav-tab-active' href='#'>Show All</a></li>"
    . "<li class=''><a class='nav-tab' tab-id='server_config' href='#'>Sever Configs</a></li>"
    . "<li class=''><a class='nav-tab' tab-id='newrelic' href='#'>New Relic</a></li>"
    . "<li class=''><a class='nav-tab' tab-id='wordpress_setting' href='#'>Wordpress Settings</a></li>"
    . "<li class=''><a class='nav-tab' tab-id='current_time' href='#'>Current Times</a></li>"
    . "<li class=''><a class='nav-tab' tab-id='cron_time' href='#'>Cron Times</a></li>"
    . "<li class=''><a class='nav-tab' tab-id='plugins' href='#'>Plugins</a></li>"
    . "<li class=''><a class='nav-tab' tab-id='cloudflare' href='#'>CloudFlare</a></li>"
    . "<li class=''><a class='nav-tab' tab-id='constants' href='#'>Test Constants</a></li>"
    . "<li class=''><a class='nav-tab' tab-id='email_settings' href='#'>Email Settings</a></li>"
    . "</ul>";

    // Server Configs
    echo "<div class='tab-content'>";
    echo "<div id='server_config' class='tab-pane'>";
    echo "<table class='system_status_table widefat' cellspacing='0' id='status'>";

    echo "<thead><tr><th colspan='2'><h3>Server Configs</h3></th></tr></thead>";

    echo "<tbody>";

    echo "<tr><td>Ubuntu Server Version: </td><td>{$system->get_ubuntu_version()}</td></tr>";

    echo "<tr><td>Ubuntu Server Latest Version: </td><td>{$system->get_latest_ubuntu_version()}</td></tr>";

    echo "<tr><td>PHP Version: </td><td>{$system->get_php_version()}</td></tr>";

    echo "<tr><td>PHP Latest Version: </td><td>{$system->get_latest_php_version()}</td></tr>";

    echo "<tr><td>Path to ini: </td><td>{$system->get_path_to_ini()}</td></tr>";

    echo "<tr><td>Server Software: </td><td>{$system->get_server_software()}</td></tr>";

    echo "<tr><td>Nginx Latest Stable Version: </td><td>{$system->get_nginx_latest_version()}</td></tr>";

    echo "<tr><td>MySQL Version: </td><td>{$system->get_mysql_version()}</td></tr>";

    echo "<tr><td>MySQL Latest Version: </td><td>{$system->get_mysql_latest_version()}</td></tr>";

    echo "<tr><td>cURL Installed: </td><td>{$system->get_curl()}</td></tr>";

    echo "<tr><td>cURL Version: </td><td>{$system->get_curl_version()}</td></tr>";

    echo "<tr><td>PHP Display Errors: </td><td>{$system->get_php_display_errors()}</td></tr>";

    echo "<tr><td>PHP Display Startup Errors: </td><td>{$system->get_php_display_startup_errors()}</td></tr>";

    echo "<tr><td>PHP Zip Enabled: </td><td>{$system->get_php_zip()}</td></tr>";

    echo "<tr><td>PHP Open SSL Enabled: </td><td>{$system->get_php_open_ssl()}</td></tr>";

    echo "<tr><td>PHP Mcrypt Installed: </td><td>{$system->get_php_mcrypt()}</td></tr>";

    echo "<tr><td>PHP GD Loaded: </td><td>{$system->get_php_gd()}</td></tr>";

    echo "<tr><td>PHP Mail Enabled: </td><td>{$system->get_php_mail()}</td></tr>";

    echo "<tr><td>PHP Upload Max Filesize: </td><td>{$system->get_upload_max_filesize()} </td></tr>";

    echo "<tr><td>PHP Post Max Size: </td><td>{$system->get_post_max_size()}</td></tr>";

    echo "<tr><td>PHP Memory Limit: </td><td>{$system->get_memory_limit()}</td></tr>";

    echo "<tr><td>PHP Max Upload Size: </td><td>{$system->get_max_upload_size()}</td></tr>";

    echo "<tr><td>PHP Max Input Vars: </td><td>{$system->get_max_input_vars()}</td></tr>";

    echo "<tr><td>PHP Max Input Time: </td><td>{$system->get_input_max_time()}</td></tr>";

    echo "<tr><td>PHP Max Execution Time: </td><td>{$system->get_execution_max_time()}</td></tr>";

    echo "<tr><td>PHP Timezone: </td><td>{$system->get_php_timezone()}</td></tr>";

    echo "<tr><td>PHP Auto Prepend File: </td><td>{$system->get_php_auto_prepend_file()}</td></tr>";

    echo "<tr><td>WP_CLI: </td><td>{$system->get_wp_cli()}</td></tr>";

    echo "<tr><td>WP_CLI Version: </td><td>{$system->get_wp_cli_version()}</td></tr>";

    echo "</tbody>";

    echo "</table>";
    echo "</div>";

    // New Relic
    echo "<div id='newrelic' class='tab-pane'>";
    echo "<table class='system_status_table widefat' cellspacing='0' id='status'>";

    echo "<thead><tr><th colspan='2'><h3>New Relic</h3></th></tr></thead>";

    echo "<tbody>";

    echo "<tr><td>New Relic Loaded: </td><td>{$system->get_new_relic()}</td></tr>";

    echo "<tr><td>New Relic PHP Agent Version: </td><td>{$system->get_newrelic_php_agent_version()}</td></tr>";

    echo "<tr><td>New Relic PHP Agent Latest Version: </td><td>{$system->get_newrelic_php_agent_latest_version()}</td></tr>";

    echo "<tr><td>New Relic PHP Up to Date: </td><td>{$system->check_current_php_argent_version()}</td></tr>";

    echo "<tr><td>New Relic Host Name: </td><td>{$system->get_newrelic_hostname()}</td></tr>";

    echo "</tbody>";

    echo "</table>";
    echo "</div>";

    // Wordpress Settings

    echo "<div id='wordpress_setting'  class='tab-pane'>";
    echo "<table class='system_status_table widefat' cellspacing='0' id='status'>";

    echo "<thead><tr><th colspan='2'><h3>Wordpress Settings</h3></th></tr></thead>";

    echo "<tbody>";

    echo "<tr><td><strong>Core Info</strong></td><td>&nbsp;</td></tr>";

    echo "<tr><td>Wordpress Version: </td><td>{$system->get_wp_version()}</td></tr>";

    echo "<tr><td>Latest Wordpress Version: </td><td>{$system->get_latest_wp_version()}</td></tr>";

    echo "<tr><td>Wordpress UpToDate: </td><td>{$system->get_wp_version_uptodate()}</td></tr>";

    echo "<tr><td>Wordpress Upload Max Filesize: </td><td>{$system->get_wordpress_upload_max_filesize()}</td></tr>";

    echo "<tr><td>ABSPATH: </td><td>{$system->get_abspath()}</td></tr>";

    echo "<tr><td>Home Path: </td><td>{$system->get_home_path()}</td></tr>";

    echo "<tr><td>Upload Directory: </td><td>{$system->get_upload_dir()}</td></tr>";

    echo "<tr><td style='padding-top: 10px;'><strong>General Settings</strong></td><td>&nbsp;</td></tr>";

    echo "<tr><td>Site Title: </td><td>{$system->get_site_title()}</td></tr>";

    echo "<tr><td>Tagline: </td><td>{$system->get_tagline()}</td></tr>";

    echo "<tr><td>Site URL (Wordpress Site (URL)): </td><td>{$system->get_site_url()}</td></tr>";

    echo "<tr><td>Home URL (Site Address (URL)): </td><td><p>{$system->get_home_url()}</td></tr>";

    echo "<tr><td>Site Admin Email: </td><td>{$system->get_site_admin_email()}</td></tr>";

    echo "<tr><td>Timezone: </td><td>{$system->get_timezone()}</td></tr>";

    echo "<tr><td>Date Format: </td><td>{$system->get_date_format()}</td></tr>";

    echo "<tr><td>Time Format: </td><td>{$system->get_time_format()}</td></tr>";

    echo "<tr><td>Default Role: </td><td>{$system->get_default_role()}</td></tr>";

    echo "<tr><td>Login URL: </td><td>{$system->get_login_url()}</td></tr>";

    echo "<tr><td>Charset: </td><td>{$system->get_charset()}</td></tr>";

    echo "<tr><td>HTML Type: </td><td>{$system->get_html_type()}</td></tr>";

    echo "<tr><td>Text Direction: </td><td>{$system->get_text_direction()}</td></tr>";

    echo "<tr><td>Language: </td><td>{$system->get_language()}</td></tr>";

    echo "<tr><td style='padding-top: 10px;'><strong>Theme</strong></td><td>&nbsp;</td></tr>";

    echo "<tr><td>Template: </td><td>{$system->get_template()}</td></tr>";

    echo "<tr><td>Template Directory: </td><td>{$system->get_template_directory()}</td></tr>";

    echo "<tr><td>Template URL: </td><td>{$system->get_template_url()}</td></tr>";

    echo "<tr><td>Stylesheet Directory: </td><td>{$system->get_stylesheet_directory()}</td></tr>";

    echo "<tr><td>Stylesheet URL: </td><td>{$system->get_stylesheet_url()}</td></tr>";

    echo "<tr><td style='padding-top: 10px;'><strong>Permalink Settings</strong></td><td>&nbsp;</td></tr>";

    echo "<tr><td>Permalinks Structure: </td><td>{$system->get_permalinks_structure()}</td></tr>";

    echo "<tr><td>Permalinks Structure (Returned by WP): </td><td>{$system->get_permalinks_structure_wp()}</td></tr>";

    echo "<tr><td>Rewrite Rules: </td><td>{$system->get_permalinks_rules()}</td></tr>";

    echo "<tr><td style='padding-top: 10px;'><strong>Config Constants</strong></td><td>&nbsp;</td></tr>";

    echo "<tr><td>WP_DEBUG: </td><td>{$system->get_wp_debug()}</td></tr>";

    echo "<tr><td>WP_DEBUG_LOG: </td><td>{$system->get_wp_debug_log()}</td></tr>";

    echo "<tr><td>WP_DEBUG_DISPLAY: </td><td>{$system->get_wp_debug_display()}</td></tr>";

    echo "<tr><td>WP_DEBUG_DISPLAY: </td><td>{$system->get_wp_debug_display()}</td></tr>";

    echo "<tr><td>DISABLE_WP_CRON: </td><td>{$system->get_disable_wp_cron()}</td></tr>";

    echo "<tr><td style='padding-top: 10px;'><strong>Folder Structure Permissions & Owners</strong></td><td>&nbsp;</td></tr>";

    $folders = $system->get_folder_struction();
    echo "<tr><td>/wp-content: </td><td>{$system->get_permission( $system->wp_content_root . '/wp-content/' )}    {$system->get_owner_user( $system->wp_content_root . '/wp-content/' )}</td></tr>";

    foreach ( $folders as $item ) {
        echo "<tr><td>" . $item['folder'] . ": </td><td>{$item['permision']}    {$item['owner']}</td></tr>";
    }

    echo "</tbody>";

    echo "</table>";
    echo "</div>";

    // Current Times
    echo "<div id='current_time'  class='tab-pane'>";
    echo "<table class='system_status_table widefat' cellspacing='0' id='status'>";

    echo "<thead><tr><th colspan='2'><h3>Current Times</h3></th></tr></thead>";

    echo "<tbody>";

    echo "<tr><td>PHP Date: </td><td>{$system->get_php_date()}</td></tr>";

    echo "<tr><td>Current Time (Mysql): </td><td>{$system->get_current_time_mysql()}</td></tr>";

    echo "<tr><td>Current Time (Formatted): </td><td>{$system->get_current_time_formatted()}</td></tr>";

    echo "<tr><td>Current Time (Timestamp): </td><td>{$system->get_current_time_timestamp()}</td></tr>";

    echo "</tbody>";

    echo "</table>";
    echo "</div>";


    // Cron times
    echo "<div id='cron_time'  class='tab-pane'>";
    echo "<table class='system_status_table widefat' cellspacing='0' id='status'>";

    echo "<thead><tr><th colspan='2'><h3>Cron Times</h3></th></tr></thead>";

    echo "<tbody>";
//    $cron_jobs = get_option('cron');

    $daily_cron_array = [
        'process_emails_9_am'    => '2 Days Cron',
        'process_emails_9_05_am' => '7 Days Cron',
        'process_emails_9_10_am' => '14 Days Cron',
        'process_emails_9_15_am' => "Fundraiser Ended Cron",
        'process_emails_9_20_am' => 'Low Participants Cron'
    ];

    foreach ( _get_cron_array() as $timestamp => $cron ) {
        if ( array_key_exists( key( $cron ), $daily_cron_array ) ) {
            echo "<tr><td>{$daily_cron_array[key( $cron )]}: </td><td>{$system->get_cron_time( $timestamp, key( $cron ) )}</td></tr>";
        }
    }

    echo "</tbody>";

    echo "</table>";
    echo "</div>";

    //Plugin section
    echo "<div id='plugins'  class='tab-pane'>";
    if ( !function_exists( 'get_plugins' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $all_plugins = $system->get_plugins_from_json();

    echo "<table class='system_status_table widefat' cellspacing='0' id='status'>";

    echo "<thead><tr><th colspan='2'><h3>Plugins</h3></th></tr></thead>";

    echo "<tbody>";
    foreach ( $all_plugins['plugins'] as $key => $value ) {
        echo "<tr><td>" . $value['name'] . "</td>"
        . "<td>{$system->plugin_active_status( $key, $value )} "
        . "{$system->plugin_install_status( $key, $value )}"
        . " {$system->plugin_version( $key, $value )}"
        . "</td></tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";


    //Cloud flare
    echo "<div id='cloudflare'  class='tab-pane'>";
    $cloudflare = new Cloudflare;

    echo "<table class='system_status_table widefat' cellspacing='0' id='status'>";

    echo "<thead><tr><th colspan='2'><h3>Cloud Flare</h3></th></tr></thead>";

    echo "<tbody>";

    //DNS
    echo "<tr><td style='padding-top: 10px;'><strong>DNS</strong></td><td>&nbsp;</td></tr>";

    echo "<tr><td>DNS Records: </td><td>{$cloudflare->check_cloudflare_dns_records( 'dns_records' )}</td></tr>";

    //Crypto
    echo "<tr><td style='padding-top: 10px;'><strong>Crypto</strong></td><td>&nbsp;</td></tr>";

    echo "<tr><td>SSL Setting: </td><td>{$cloudflare->check_cloudflare_api( 'ssl' )}</td></tr>";
    echo "<tr><td>SSL Verification Status: </td><td>{$cloudflare->check_cloudflare_api( 'ssl_verification' )}</td></tr>";
    echo "<tr><td>Always Use HTTPS: </td><td>{$cloudflare->check_cloudflare_api( 'always_use_https' )}</td></tr>";
    echo "<tr><td>HTTP Strict Transport Security (HSTS): </td><td>{$cloudflare->check_cloudflare_api( 'security_header' )}</td></tr>";
    echo "<tr><td>Authenticated Origin Pulls: </td><td>{$cloudflare->check_cloudflare_api( 'tls_client_auth' )}</td></tr>";
    echo "<tr><td>Minimum TLS Version: </td><td>{$cloudflare->check_cloudflare_api( 'min_tls_version' )}</td></tr>";
    echo "<tr><td>Opportunistic Encryption: </td><td>{$cloudflare->check_cloudflare_api( 'opportunistic_encryption' )}</td></tr>";
    echo "<tr><td>Onion Routing: </td><td>{$cloudflare->check_cloudflare_api( 'opportunistic_onion' )}</td></tr>";
    echo "<tr><td>TLS 1.3: </td><td>{$cloudflare->check_cloudflare_api( 'tls_1_3' )}</td></tr>";
    echo "<tr><td>Automatic HTTPS Rewrite: </td><td>{$cloudflare->check_cloudflare_api( 'automatic_https_rewrites' )}</td></tr>";

    //Firewall
    echo "<tr><td style='padding-top: 10px;'><strong>Firewall</strong></td><td>&nbsp;</td></tr>";

    echo "<tr><td>Rate Limiting: </td><td>{$cloudflare->check_cloudflare_api( 'rate_limits' )}</td></tr>";
    echo "<tr><td>Security Level: </td><td>{$cloudflare->check_cloudflare_api( 'security_level' )}</td></tr>";
    echo "<tr><td>Challenge Passage: </td><td>{$cloudflare->check_cloudflare_api( 'challenge_passage' )}</td></tr>";
    echo "<tr><td>Privacy Pass Support: </td><td>{$cloudflare->check_cloudflare_api( 'privacy_pass' )}</td></tr>";
    echo "<tr><td>Web Application Firewall (WAF): </td><td>{$cloudflare->check_cloudflare_api( 'waf' )}</td></tr>";
    echo "<tr><td>Browser Integrity Check: </td><td>{$cloudflare->check_cloudflare_api( 'browser_check' )}</td></tr>";

    //Speed
    echo "<tr><td style='padding-top: 10px;'><strong>Speed</strong></td><td>&nbsp;</td></tr>";

    echo "<tr><td>Auto Minify: </td><td>{$cloudflare->check_cloudflare_api( 'minify' )}</td></tr>";
    echo "<tr><td>Polish: </td><td>{$cloudflare->check_cloudflare_api( 'polish' )}</td></tr>";
    echo "<tr><td>Railgun: </td><td>{$cloudflare->check_cloudflare_api( 'railguns' )}</td></tr>";
    echo "<tr><td>Enable Accelerated Mobile Links: </td><td>{$cloudflare->check_cloudflare_api( 'viewer' )}</td></tr>";
    echo "<tr><td>Brotli: </td><td>{$cloudflare->check_cloudflare_api( 'brotli' )}</td></tr>";
    echo "<tr><td>Mirage: </td><td>{$cloudflare->check_cloudflare_api( 'mirage' )}</td></tr>";
    echo "<tr><td>Rocket Loader: </td><td>{$cloudflare->check_cloudflare_api( 'rocket_loader' )}</td></tr>";
    echo "<tr><td>Mobile Redirect: </td><td>{$cloudflare->check_cloudflare_api( 'mobile_redirect' )}</td></tr>";

    // Caching    
    echo "<tr><td style='padding-top: 10px;'><strong>Caching</strong></td><td>&nbsp;</td></tr>";

    echo "<tr><td>Cache Level: </td><td>{$cloudflare->check_cloudflare_api( 'cache_level' )}</td></tr>";
    echo "<tr><td>Browser Cache Expiration: </td><td>{$cloudflare->check_cloudflare_api( 'browser_cache_ttl' )}</td></tr>";
    echo "<tr><td>Always Online: </td><td>{$cloudflare->check_cloudflare_api( 'always_online' )}</td></tr>";
    echo "<tr><td>Development Mode: </td><td>{$cloudflare->check_cloudflare_api( 'development_mode' )}</td></tr>";

    // Page Rules
    echo "<tr><td style='padding-top: 10px;'><strong>Page Rules</strong></td><td>&nbsp;</td></tr>";

    echo "<tr><td>Page Rules: </td><td>{$cloudflare->check_cloudflare_page_rules( 'pagerules' )}</td></tr>";

    // Network
    echo "<tr><td style='padding-top: 10px;'><strong>Network</strong></td><td>&nbsp;</td></tr>";

    echo "<tr><td>IPv6 Compatibility: </td><td>{$cloudflare->check_cloudflare_api( 'ipv6' )}</td></tr>";
    echo "<tr><td>WebSockets: </td><td>{$cloudflare->check_cloudflare_api( 'websockets' )}</td></tr>";
    echo "<tr><td>PseudoIPv4: </td><td>{$cloudflare->check_cloudflare_api( 'pseudo_ipv4' )}</td></tr>";
    echo "<tr><td>IP Geolocation: </td><td>{$cloudflare->check_cloudflare_api( 'ip_geolocation' )}</td></tr>";
    echo "<tr><td>Response Buffering: </td><td>{$cloudflare->check_cloudflare_api( 'response_buffering' )}</td></tr>";
    echo "<tr><td>True-Client-IP-Header: </td><td>{$cloudflare->check_cloudflare_api( 'true_client_ip_header' )}</td></tr>";


    // Custom Pages
    echo "<tr><td style='padding-top: 10px;'><strong>Custom Pages</strong></td><td>&nbsp;</td></tr>";

//    $custom_pages = $cloudflare->check_cloudflare_api( 'custom_pages' ); 

    echo "<tr><td>IP/Country Block: </td><td>{$cloudflare->check_cloudflare_api( 'ip_block' )}</td></tr>";
    echo "<tr><td>WAF Block: </td><td>{$cloudflare->check_cloudflare_api( 'waf_block' )}</td></tr>";
    echo "<tr><td>500 Class Errors: </td><td>{$cloudflare->check_cloudflare_api( '500_errors' )}</td></tr>";
    echo "<tr><td>1000 Class Errors: </td><td>{$cloudflare->check_cloudflare_api( '1000_errors' )}</td></tr>";
    echo "<tr><td>Always Online Error: </td><td>{$cloudflare->check_cloudflare_api( 'always_online_page' )}</td></tr>";
    echo "<tr><td>Basic Security Challenge: </td><td>{$cloudflare->check_cloudflare_api( 'basic_challenge' )}</td></tr>";
    echo "<tr><td>WAF Challenge: </td><td>{$cloudflare->check_cloudflare_api( 'waf_challenge' )}</td></tr>";
    echo "<tr><td>Country Challenge: </td><td>{$cloudflare->check_cloudflare_api( 'country_challenge' )}</td></tr>";
    echo "<tr><td>I'm Under Attack Mode Challenge: </td><td>{$cloudflare->check_cloudflare_api( 'under_attack' )}</td></tr>";
    echo "<tr><td>429 Errors Challenge: </td><td></td></tr>";


    //Scrap Shield
    echo "<tr><td style='padding-top: 10px;'><strong>Scrape Shield</strong></td><td>&nbsp;</td></tr>";

    echo "<tr><td>Email Address Obfuscation: </td><td>{$cloudflare->check_cloudflare_api( 'email_obfuscation' )}</td></tr>";
    echo "<tr><td>Server-side Excludes: </td><td>{$cloudflare->check_cloudflare_api( 'server_side_exclude' )}</td></tr>";
    echo "<tr><td>Hotlink Protection: </td><td>{$cloudflare->check_cloudflare_api( 'hotlink_protection' )}</td></tr>";

    echo "</tbody>";

    echo "</table>";
    echo "</div>";





    // Test Constants
    $contants = new Constants;
    echo "<div id='constants'  class='tab-pane'>";
    echo "<table class='system_status_table widefat' cellspacing='0' id='status'>";

    echo "<thead><tr><th colspan='2'><h3>Test Constants</h3></th></tr></thead>";

    echo "<tbody>";

    echo "<tr><td><strong>Third Party API Credentials</strong></td><td>&nbsp;</td></tr>";

    echo "<tr><td>Facebook: </td><td>
            <p> {$contants->system_config( '_FACEBOOK_CLIENT_ID' )}</p>
            <p> {$contants->system_config( '_FACEBOOK_CLIENT_SECRET' )}</p>
            <p> {$contants->system_config( '_FACEBOOK_APP_ID' )}</p>
        </td></tr>";

    echo "<tr><td>SendGrid: </td><td><p> {$contants->system_config( '_SENDGRID_APIKEY' )}</p></td></tr>";

    echo "<tr><td>Twilio: </td><td>
            <p> {$contants->system_config( '_TWILIO_ACCOUNT_ID' )}</p>
            <p> {$contants->system_config( '_TWILIO_AUTH_TOKEN' )}</p>
            <p> {$contants->system_config( '_TWILIO_FROM_NUMBER' )}</p>
        </td></tr>";

    echo "<tr><td>MailGun: </td><td><p> {$contants->system_config( '_MAILGUN_PUBKEY' )}</p></td></tr>";

    echo "<tr><td>Stripe: </td><td>
            <p> {$contants->system_config( (( _SERVER_TYPE == 'dev' )) ? '_STRIPE_DEV_SECRET_KEY' : '_STRIPE_SECRET_KEY'  )}</p>
            <p> {$contants->system_config( (( _SERVER_TYPE == 'dev' )) ? '_STRIPE_DEV_PUBLISHABLE_KEY' : '_STRIPE_PUBLISHABLE_KEY'  )}</p>
        </td></tr>";

    echo "<tr><td>Google: </td><td>
            <p> {$contants->system_config( (( _SERVER_TYPE == 'dev' )) ? '_GOOGLE_URL_DEV_KEY' : '_GOOGLE_URL_LIVE_KEY'  )}</p>
            <p> {$contants->system_config( '_GOOGLE_API_KEY' )}</p>
            <p> {$contants->system_config( '_GOOGLE_CLIENT_ID' )}</p>
            <p> {$contants->system_config( '_GOOGLE_CLIENT_SECRET' )}</p>
            <p> {$contants->system_config( '_GOOGLE_REFRESH_TOKEN' )}</p>
            <p> {$contants->system_config( '_GOOGLE_UA_CODE' )}</p>
                
        </td></tr>";

    echo "<tr><td>EmailListVerify: </td><td><p> {$contants->system_config( '_EMAIL_LIST_VERIFY_API_KEY' )}</p></td></tr>";

    echo "<tr><td>TheChecker: </td><td><p> {$contants->system_config( '_THE_CHECKER_API_KEY' )}</p></td></tr>";

    echo "<tr><td>NeverBounce: </td><td>
            <p> {$contants->system_config( (( _SERVER_TYPE == 'dev' )) ? '_NEVERBOUNCE_API_DEV_KEY' : '_NEVERBOUNCE_API_LIVE_KEY'  )}</p>
        </td></tr>";

    echo "<tr><td>Envoyer: </td><td>
            <p> {$contants->system_config( (( _SERVER_TYPE == 'dev' )) ? '_ENVOYER_HEARTBEAT_ENDPOINT_DEV' : '_ENVOYER_HEARTBEAT_ENDPOINT'  )}</p>
        </td></tr>";

    echo "<tr><td>Cloudsponge: </td><td><p> {$contants->system_config( '_CLOUDSPONGE_API_KEY' )}</p></td></tr>";

    echo "<tr><td>ClouldFlare: </td><td>
            <p> {$contants->system_config( '_CLOUDFLARE_ZONE' )}</p>
            <p> {$contants->system_config( '_CLOUDFLARE_AUTH_KEY' )}</p>
            <p> {$contants->system_config( '_CLOUDFLARE_AUTH_EMAIL' )}</p>
        </td></tr>";

    echo "<tr><td style='padding-top: 10px;'><strong>Global Variables</strong></td><td>&nbsp;</td></tr>";

    echo "<tr><td>Theme Related: </td><td>
            <p> {$contants->system_config( '_THEME_FOLDER' )}</p>
            <p> {$contants->system_config( '_THEME_PATH' )}</p>
            <p> {$contants->system_config( '_THEME_IMAGES_PATH' )}</p>
        </td></tr>";

    echo "<tr><td>Fundraising: </td><td><p> {$contants->system_config( '_PARTICIPATION_GOAL' )}</p></td></tr>";

    echo "<tr><td>Mailing: </td><td>
            <p> {$contants->system_config( '_MAILGUN' )}</p>
            <p> {$contants->system_config( '_CHECK_EXTERNAL_EMAIL_VALIDATOR' )}</p>
            <p> {$contants->system_config( '_DEFAULT_FROM_NAME' )}</p>
            <p> {$contants->system_config( '_EMAIL_INVITE_LIMIT' )}</p>
        </td></tr>";

    echo "<tr><td>SMS: </td><td>
            <p> {$contants->system_config( '_SMS_INVITE_LIMIT' )}</p>
            <p> {$contants->system_config( '_ENCRYPTION_KEY' )}</p>
            <p> {$contants->system_config( '_RUN_DB_SETUP' )}</p>
        </td></tr>";

    echo "<tr><td>Social Media: </td><td>
            <p> {$contants->system_config( '_SOCIAL_MEDIA_FACEBOOK_PAGE_URL' )}</p>
            <p> {$contants->system_config( '_SOCIAL_MEDIA_GOOGLE_PLUS_URL' )}</p>
            <p> {$contants->system_config( '_SOCIAL_MEDIA_INSTAGRAM_URL' )}</p>
            <p> {$contants->system_config( '_SOCIAL_MEDIA_TWITTER_URL' )}</p>
            <p> {$contants->system_config( '_SOCIAL_MEDIA_LINKEDIN_URL' )}</p>
        </td></tr>";

    echo "<tr><td>Heartbeat: </td><td>
            <p> {$contants->system_config( (( _SERVER_TYPE == 'dev' )) ? '_HEARTBEAT_ENDPOINT_DEV' : '_HEARTBEAT_ENDPOINT'  )}</p>
        </td></tr>";

    echo "<tr><td>Short URLs: </td><td>
            <p> {$contants->system_config( (( _SERVER_TYPE == 'dev' )) ? '_SHORTURL_DEV' : '_SHORTURL'  )}</p>
            <p> {$contants->system_config( (( _SERVER_TYPE == 'dev' )) ? '_SHORTURL_BASE_DEV' : '_SHORTURL_BASE'  )}</p>           
        </td></tr>";

    echo "<tr><td>Cron Emails: </td><td>
            <p> {$contants->system_config( '_ENDED_CAMPAIGNS_TO_EMAIL' )}</p>
            <p> {$contants->system_config( '_CRON_FROM_EMAIL' )}</p>
        </td></tr>";

    echo "<tr><td>Transactional Emails: </td><td>
            <p> {$contants->system_config( '_TRANSACTIONAL_FROM_EMAIL' )}</p>
        </td></tr>";

    echo "<tr><td>Global to Emails: </td><td>
            <p> {$contants->system_config( '_ADMIN_TO_EMAIL' )}</p>
            <p> {$contants->system_config( '_SUPPORT_TO_EMAIL' )}</p>
        </td></tr>";

    echo "<tr><td>Email Signature: </td><td>
            <p> {$contants->system_config( '_SIGNATURE_OFFICE_PHONE_NUMBER' )}</p>
            <p> {$contants->system_config( '_SIGNATURE_FAX_NUMBER' )}</p>
            <p> {$contants->system_config( '_SIGNATURE_EMAIL' )}</p>
        </td></tr>";

    echo "</tbody>";

    echo "</table>";
    echo "</div>";

    //Email Settings
    echo "<div id='email_settings'  class='tab-pane'>";
    echo "<table class='system_status_table widefat' cellspacing='0' id='status'>";

    echo "<thead><tr><th colspan='2'><h3>Email Settings</h3></th></tr></thead>";

    echo "<tbody>";

    echo "<tr><td>Cron Emails: </td><td>
            <p> _ENDED_CAMPAIGNS_TO_EMAIL: {$system->get_system_emails( '_ENDED_CAMPAIGNS_TO_EMAIL' )}</p>
            <p> _CRON_FROM_EMAIL: {$system->get_system_emails( '_CRON_FROM_EMAIL' )}</p>
        </td></tr>";

    echo "<tr><td>Transactional Emails: </td><td>
            <p> _TRANSACTIONAL_FROM_EMAIL: {$system->get_system_emails( '_TRANSACTIONAL_FROM_EMAIL' )}</p>
        </td></tr>";

    echo "<tr><td>Global to Emails: </td><td>
            <p> _ADMIN_TO_EMAIL: {$system->get_system_emails( '_ADMIN_TO_EMAIL' )}</p>
            <p> _SUPPORT_TO_EMAIL: {$system->get_system_emails( '_SUPPORT_TO_EMAIL' )}</p>
        </td></tr>";

    echo "<tr><td>Email Signature: </td><td>
            <p> _SIGNATURE_OFFICE_PHONE_NUMBER: {$system->get_system_emails( '_SIGNATURE_OFFICE_PHONE_NUMBER' )}</p>
            <p> _SIGNATURE_FAX_NUMBER: {$system->get_system_emails( '_SIGNATURE_FAX_NUMBER' )}</p>
            <p> _SIGNATURE_EMAIL: {$system->get_system_emails( '_SIGNATURE_EMAIL' )}</p>
        </td></tr>";

    echo "</tbody>";

    echo "</table>";
    echo "</div>";

    echo "</div>";

    echo "</div>";
}
