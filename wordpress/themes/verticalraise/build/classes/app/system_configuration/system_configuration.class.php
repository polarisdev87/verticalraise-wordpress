<?php

namespace classes\app\System_configuration;

// Get specific libraries: http://sg.php.net/manual/en/function.extension-loaded.php

use classes\app\System_configuration\Config_Comparations;
use classes\app\System_configuration\Load_Json_File;

class System_Configuration
{

    public $wp_version;
    public $wp_latest_version;
    public $wp_content_root;

    public function __construct() {

        $this->compare           = new Config_Comparations;
        $this->plugin_json       = new Load_Json_File;
        $this->wp_version        = $this->get_wp_version();
        $this->wp_latest_version = $this->_get_latest_wp_version();
        $this->wp_content_root   = $this->get_wpcontent_root();
    }

    private function get_wpcontent_root() {
        $template_directory = get_template_directory();
        $content_dir        = explode( '/wp-content', $template_directory );
        return $content_dir[0];
    }

    private function value_wrapper( $key, $value, $type = null ) {

        $check = $this->compare->check( $key, $value, $type );

        if ( $check ) {
            $class = "expected";
        } else {
            $class = "unexpected";
        }

        $size_key    = [ 'upload_max_filesize', 'post_max_size', 'memory_limit', 'max_upload_size', 'wordpress_upload_max_filesize' ];
        $seconds_key = [ 'input_max_time', 'execution_max_time' ];

        //size 
        if ( in_array( $key, $size_key ) ) {
            $value = $value . " MB";
        }
        if ( in_array( $key, $seconds_key ) ) {
            $value = $value . " s";
        }

        //dev & prod check
        if ( strpos( $key, 'dev_' ) !== false ) {
            $value = 'Dev: ' . $value;
        }
        if ( strpos( $key, 'prod_' ) !== false ) {
            $value = 'Prod: ' . $value;
        }
        return '<span class="checkmark ' . $class . '" style="font-weight:900">&#x2713; </span> <span class="value ' . $class . '" >' . $value . '</span>';
    }

    public function value_wrapper_plugin( $key, $check, $val, $type ) {
        if ( $check ) {
            $class = "expected";
        } else {
            $class = "unexpected";
        }
        //plugin check
        if ( !empty( $type ) && $type == "install_status" ) {
            $label = ($check) ? 'Installed' : 'Missing';
            return '<span class="' . $class . '">' . $label . '</span>';
        }

        if ( !empty( $type ) && $type == "active_status" ) {
            if ( $check ) {
                return '<span class="checkmark ' . $class . '" style="font-weight:900">&#x2713; </span>';
            } else {
                return '<span></span>';
            }
        }

        if ( !empty( $type ) && $type == "version" ) {

            return '<span class="' . $class . '" >V.' . $val . '</span>';
        }
        //plugin check
    }

    public function get_ubuntu_version() {
        $re = '/\d{2}.\d{2}(?:.\d{1})?/m';
        if ( preg_match( $re, phpversion(), $matches ) ) {
            $current = $matches[0];
            return $this->value_wrapper( 'os_version', $current );
        } else {
            return "Failed to get Ubuntu version";
        }
    }

    public function get_latest_ubuntu_version() {

        $url = "http://cdimage.ubuntu.com/releases/";

        $curl = curl_init();
        curl_setopt_array( $curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(),
            CURLOPT_FOLLOWLOCATION => true,
        ) );

        $response = curl_exec( $curl );

        if ( isset( $_SESSION['sc_cache_ubuntu']['version'] ) ) {
            return $_SESSION['sc_cache_ubuntu']['version'];
        }

        if ( $response ) {
            $htmlObj = new \DOMDocument;
            @$htmlObj->loadHTML( $response );
            $a_list = $htmlObj->getElementsByTagName( 'a' );
            $re = '/\d{2}.\d{2}(?:.\d{1})?/m';
            $versions = [];
            foreach ( $a_list as $a ) {
                if ( preg_match( $re, $a->nodeValue, $matches ) ) {
                    $versions[] = $matches[0];
                }
            }
            if ( count( $versions ) > 0 ) {

                $re = '/(?\'mayor\'\d{2}).(?\'minor\'\d{2})(?:.\d{1})?/m';
                if ( preg_match( $re, phpversion(), $matches ) ) {
                    $current = $matches;
                }

                sort( $versions );
                $versions = array_filter( $versions, function ( $element ) use ( $current, $re ) {
                    if ( preg_match( $re, $element, $matches ) ) {
                        if ( intval( $matches['mayor'] == $current['mayor'] && $matches['minor'] == $current['minor'] ) ) {
                            return true;
                        }
                    }
                    return false;
                } );

                $last_version = array_pop( $versions );

                $_SESSION['sc_cache_ubuntu'] = [
                    'ts' => time(),
                    'version' => $last_version,
                ];

                return $last_version;
            } else {
                return "Failed to determinate Latest Ubuntu version";
            }
        } else {
            return "Failed to get Latest Ubuntu version";
        }
    }

    public function get_php_version() {

        $re = '/^(\d{1}.\d{1,2}.\d{1,2})/m';
        if ( preg_match( $re, phpversion(), $matches ) ) {
            $current = $matches[0];
            return $this->value_wrapper( 'php_version', $current );
        } else {
            return "Failed to get PHP version";
        }

        return $this->value_wrapper( 'php_version', phpversion() );
    }

    public function get_latest_php_version() {


        $url = "http://php.net/releases/feed.php";

        $curl = curl_init();
        curl_setopt_array( $curl, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "GET",
            CURLOPT_HTTPHEADER     => array(),
            CURLOPT_FOLLOWLOCATION => true,
        ) );

        $response = curl_exec( $curl );
        $response = str_replace('xmlns=', 'ns=', $response);
        $xml      = simplexml_load_string( $response );
        try {
            return @$xml->xpath( '/feed/entry[1]/php:version' )[0]->__toString();
        } catch ( \Throwable $e ) {
            return "Failed to get latest PHP version";
        }
    }

    public function get_path_to_ini() {
        return $this->value_wrapper( 'path_to_ini', php_ini_loaded_file() );
    }

    public function get_server_software() {
        return $this->value_wrapper( 'server_software', $_SERVER['SERVER_SOFTWARE'] );
    }

    public function get_nginx_latest_version() {
        

        $url = "http://nginx.org/en/download.html";

        $curl = curl_init();
        curl_setopt_array( $curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(),
            CURLOPT_FOLLOWLOCATION => true,
        ) );

        $response = curl_exec( $curl );
        if ( $response ) {
            $htmlObj = new \DOMDocument;
            @$htmlObj->loadHTML( $response );
            $a_list = $htmlObj->getElementsByTagName( 'a' );
            $re = '/^nginx-(\d{1}.\d{1,2}.\d{1,2})/';
            $versions = [];
            foreach ( $a_list as $a ) {
                if ( preg_match( $re, $a->nodeValue, $matches ) ) {
                    $versions[] = $matches[0];
                }
            }
            if ( count( $versions ) > 0 ) {
                sort( $versions , SORT_NATURAL);
                $last_version = array_pop( $versions );
                return $last_version;
            } else {
                return "Failed to determinate Latest Nginx version";
            }
        } else {
            return "Failed to get Latest Nginx version";
        }
    }

    public function get_mysql_version() {
        global $wpdb;
        return $this->value_wrapper( 'mysql_version', $wpdb->db_version() );
    }

    public function get_mysql_latest_version() {


        //https://en.wikipedia.org/wiki/MySQL
        $curl = curl_init();
        curl_setopt_array( $curl, array(
            CURLOPT_URL            => 'https://en.wikipedia.org/wiki/MySQL',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "GET",
            CURLOPT_HTTPHEADER     => array()
        ) );

        $response = curl_exec( $curl );

        // Create a DOM object    
        $version = 'false';
        $htmlObj = new \DOMDocument;
        libxml_use_internal_errors( true );
        $htmlObj->loadHTML( $response );
        libxml_clear_errors();

        $xpath = new \DOMXpath( $htmlObj );

        $classname = "wikitable";
        $wikitable = $xpath->query( "//table[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]//tbody//tr[6]" );

        if ( $wikitable->length == 1 ) {
            $td = $xpath->query( 'td', $wikitable[0] );
            if ( $td->length == 4 ) {
                $version = substr( $xpath->query( 'td', $wikitable[0] )[1]->textContent, 0, 6 );
            }
        }

        return $version;
    }

    public function get_curl() {
        return $this->value_wrapper( 'curl', function_exists( 'curl_version' ) ? 'true' : 'false' );
    }

    public function get_curl_version() {
        if ( function_exists( 'curl_version' ) ) {
            $array       = curl_version();
            $version     = $array['version'];
            $ssl_version = $array['ssl_version'];
            return $this->value_wrapper( 'curl_version', "{$version}, {$ssl_version}" );
        }

        return $this->value_wrapper( 'curl_version', '-' );
    }

    public function get_php_zip() {
        return $this->value_wrapper( 'php_zip', extension_loaded( 'zip' ) ? 'true' : 'false' );
    }

    public function get_php_open_ssl() {
        return $this->value_wrapper( 'php_open_ssl', extension_loaded( 'openssl' ) ? 'true' : 'false' );
    }

    public function get_php_mcrypt() {
        return $this->value_wrapper( 'php_mcrypt', function_exists( 'mcrypt_encrypt' ) ? 'true' : 'false' );
    }

    public function get_php_gd() {
        return $this->value_wrapper( 'php_gd', extension_loaded( 'gd' ) ? 'true' : 'false' );
    }

    public function get_php_mail() {
        return $this->value_wrapper( 'php_mail', function_exists( 'mail' ) ? 'true' : 'false' );
    }

    public function get_php_display_errors() {

        return $this->value_wrapper( ( _SERVER_TYPE == 'dev' ) ? 'dev_php_display_error' : 'prod_php_display_error', (int) (ini_get( 'display_errors' )) );
    }

    public function get_php_display_startup_errors() {
        return $this->value_wrapper( ( _SERVER_TYPE == 'dev' ) ? 'dev_php_startup_err' : 'prod_php_startup_err', (int) (ini_get( 'display_startup_errors' )) );
    }

    public function get_upload_max_filesize() {
        return $this->value_wrapper( 'upload_max_filesize', (int) (ini_get( 'upload_max_filesize' )) );
    }

    public function get_input_max_time() {
        return $this->value_wrapper( 'input_max_time', (int) (ini_get( 'max_input_time' )) );
    }

    public function get_execution_max_time() {
        return $this->value_wrapper( 'execution_max_time', (int) (ini_get( 'max_execution_time' )) );
    }

    public function get_post_max_size() {
        return $this->value_wrapper( 'post_max_size', (int) (ini_get( 'post_max_size' )) );
    }

    public function get_memory_limit() {
        return $this->value_wrapper( 'memory_limit', (int) (ini_get( 'memory_limit' )) );
    }

    public function get_max_upload_size() {
        $upload_max_filesize = $this->get_upload_max_filesize();
        $post_max_size       = $this->get_post_max_size();
        $memory_limit        = $this->get_memory_limit();

        preg_match( '!\d+!', $upload_max_filesize, $uploadMax );
        preg_match( '!\d+!', $post_max_size, $postMax );
        preg_match( '!\d+!', $memory_limit, $memLimit );
        $min = min( (int) $uploadMax[0], (int) $postMax[0], (int) $memLimit[0] );
        if ( $memory_limit == -1 ) {
            $min = min( (int) $uploadMax[0], (int) $postMax[0] );
        }

        return $this->value_wrapper( 'max_upload_size', $min );
    }

    public function get_max_input_vars() {
        return $this->value_wrapper( 'max_input_vars', (int) (ini_get( 'max_input_vars' )) );
    }

    public function get_new_relic() {
        return $this->value_wrapper( 'newrelic_loaded', extension_loaded( 'newrelic' ) ? 'true' : 'false' );
    }

    public function get_newrelic_php_agent_version() {
        $php_agent_version = 'false';
        if ( extension_loaded( 'newrelic' ) ) {
            $php_agent_version = phpversion( "newrelic" );
        }
        return $this->value_wrapper( 'newrelic_php_agent_version', $php_agent_version );
    }

    public function get_newrelic_php_agent_latest_version() {
        $curl = curl_init();
        curl_setopt_array( $curl, array(
            CURLOPT_URL            => 'https://download.newrelic.com/php_agent/release/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "GET",
            CURLOPT_HTTPHEADER     => array()
        ) );

        $response = curl_exec( $curl );

        // Create a DOM object    
        $htmlObj = new \DOMDocument();
        $htmlObj->loadHTML( $response );
        $trObj   = $htmlObj->getElementsByTagName( "tr" );
        $version = 'false';

        foreach ( $trObj as $tr ) {
            $text = explode( "-", $tr->nodeValue );
            if ( isset( $text[2] ) && !empty( $text[2] ) ) {
                $version = $text[2];
                break;
            }
        }

        return $this->value_wrapper( 'newrelic_php_agent_latest_version', $version );
    }

    public function check_current_php_argent_version() {
        $checked         = 'false';
        $current_version = $this->get_newrelic_php_agent_version();
        if ( $current_version == 'false' ) {
            
        } else {
            $new_version = $this->get_newrelic_php_agent_latest_version();
            if ( $current_version !== $new_version ) {
                
            } else {
                $checked = "true";
            }
        }

        return $this->value_wrapper( 'newrelic_php_up_to_date', $checked );
    }

    public function get_newrelic_hostname() {
        $newrelic_hostname = 'false';
        if ( extension_loaded( 'newrelic' ) ) {
            $newrelic_hostname = ini_get( 'newrelic.appname' );
        }

        return $this->value_wrapper( ( _SERVER_TYPE == 'dev' ) ? 'dev_newrelic_hostname' : 'prod_newrelic_hostname', $newrelic_hostname );
    }

    public function get_php_auto_prepend_file() {
        $auto_prepend_file = ini_get( 'auto_prepend_file' );
        if ( $auto_prepend_file == NULL ) {
            $auto_prepend_file = "false";
        } else {
            $auto_prepend_file = "true";
        }

        return $this->value_wrapper( 'php_auto_prepend_file', $auto_prepend_file );
    }

    public function get_wp_cli() {
        if ( defined( 'WP_CLI' ) ) {
            return $this->value_wrapper( 'wp_cli', WP_CLI ? 'true' : 'false' );
        }

        return $this->value_wrapper( 'wp_cli', 'false' );
    }

    public function get_wp_cli_version() {
        if ( defined( 'WP_CLI_VERSION' ) ) {
            return $this->value_wrapper( 'wp_cli_version', WP_CLI_VERSION );
        }

        return $this->value_wrapper( 'wp_cli_version', 'false' );
    }

    public function get_wp_version() {
        global $wp_version;
        return $wp_version;
    }

    public function get_wp_version_uptodate() {
        return $this->wp_version == $this->wp_latest_version ? 'true' : 'false';
    }

    public function _get_latest_wp_version() {
        global $wp_version;
        $url      = "https://api.wordpress.org/core/version-check/1.7/";
        $response = wp_remote_get( $url );
        if ( $response ) {
            $json = $response['body'];
            $obj  = json_decode( $json );

            $upgrade = $obj->offers[0];
            return $upgrade->version;
        }

        return 'no response';
    }

    public function get_latest_wp_version() {
        return $this->wp_latest_version;
    }

    public function get_php_date() {
        return date( 'Y-m-d h:i:s' );
    }

    public function get_php_timezone() {
        return $this->value_wrapper( 'php_timezone', date_default_timezone_get() );
    }

    public function get_wordpress_upload_max_filesize() {
        $size = (int) wp_max_upload_size() / 1024 / 1024;
        return $this->value_wrapper( 'wordpress_upload_max_filesize', $size );
    }

    public function get_site_admin_email() {
        return $this->value_wrapper( ( _SERVER_TYPE == 'dev' ) ? 'dev_site_admin_email' : 'prod_site_admin_email', get_option( 'admin_email' ) );
    }

    public function get_site_title() {
        return $this->value_wrapper( 'site_title', get_bloginfo( 'name' ) );
    }

    public function get_tagline() {
        $tagline = htmlspecialchars_decode(get_bloginfo( 'description' ), ENT_QUOTES);
        return $this->value_wrapper( 'tagline', ($tagline) ? $tagline : 'false' );
    }

    public function get_site_url() {
        if ( _SERVER_TYPE == 'dev' ) {
            return $this->value_wrapper( 'dev_site_url', get_option( 'siteurl' ) );
        } else {
            return $this->value_wrapper( 'prod_site_url', get_option( 'siteurl' ) );
        }
    }

    public function get_home_url() {
        if ( _SERVER_TYPE == 'dev' ) {
            return $this->value_wrapper( 'dev_home_url', get_option( 'home' ) );
        } else {
            return $this->value_wrapper( 'prod_home_url', get_option( 'home' ) );
        }
    }

    public function get_template() {
        return $this->value_wrapper( 'template', get_option( 'template' ) );
    }

    public function get_wp_debug() {
        return $this->value_wrapper( ( _SERVER_TYPE == 'dev' ) ? 'dev_wp_debug' : 'prod_wp_debug', WP_DEBUG ? 'true' : 'false' );
    }

    public function get_wp_debug_log() {
        return $this->value_wrapper( ( _SERVER_TYPE == 'dev' ) ? 'dev_wp_debug_log' : 'prod_wp_debug_log', WP_DEBUG_LOG ? 'true' : 'false' );
    }

    public function get_wp_debug_display() {
        return $this->value_wrapper( ( _SERVER_TYPE == 'dev' ) ? 'dev_wp_debug_display' : 'prod_wp_debug_display', WP_DEBUG_DISPLAY ? 'true' : 'false' );
    }

    public function get_current_time_mysql() {
        return current_time( 'mysql' );
    }

    public function get_current_time_formatted() {
        return current_time( 'Y-m-d H:i:s' );
    }

    public function get_current_time_timestamp() {
        return current_time( 'timestamp' );
    }

    public function get_timezone() {
        return $this->value_wrapper( 'timezone', get_option( 'timezone_string' ) );
    }

    public function get_date_format() {
        return $this->value_wrapper( 'date_format', get_option( 'date_format' ) );
    }

    public function get_time_format() {
        return $this->value_wrapper( 'time_format', get_option( 'time_format' ) );
    }

    public function get_default_role() {
        return $this->value_wrapper( 'default_role', get_option( 'default_role' ) );
    }

    public function get_locale() {
        return get_locale();
    }

    public function get_disable_wp_cron() {
        return $this->value_wrapper( ( _SERVER_TYPE == 'dev' ) ? 'dev_disable_wp_cron' : 'prod_disable_wp_cron', DISABLE_WP_CRON ? 'true' : 'false' );
    }

    public function get_template_directory() {
        return $this->value_wrapper( 'template_directory', get_template_directory() );
    }

    public function get_stylesheet_url() {
        if ( server_type() == "prod" ) {
            $key = 'stylesheet_url';
        } else {
            $key = 'stylesheet_url_dev';
        }
        return $this->value_wrapper( $key, get_bloginfo( 'stylesheet_url' ) );
    }

    public function get_stylesheet_directory() {
        if ( server_type() == "prod" ) {
            $key = 'stylesheet_directory';
        } else {
            $key = 'stylesheet_directory_dev';
        }
        return $this->value_wrapper( $key, get_bloginfo( 'stylesheet_directory' ) );
    }

    public function get_template_url() {
        if ( server_type() == "prod" ) {
            $key = 'template_url';
        } else {
            $key = 'template_url_dev';
        }
        return $this->value_wrapper( $key, get_bloginfo( 'template_url' ) );
    }

    public function get_home_path() {
        return $this->value_wrapper( 'home_path', get_home_path() );
    }

    public function get_abspath() {
        return $this->value_wrapper( 'abspath', ABSPATH );
    }

    public function get_upload_dir() {
        $uploads = wp_upload_dir();
        return $this->value_wrapper( 'upload_directory', $uploads['basedir'] );
    }

    public function get_charset() {
        return $this->value_wrapper( 'charset', get_bloginfo( 'charset' ) );
    }

    public function get_login_url() {
        if ( server_type() == "prod" ) {
            $key = 'login_url';
        } else {
            $key = 'login_url_dev';
        }
        return $this->value_wrapper( $key, wp_login_url() );
    }

    public function get_html_type() {
        return $this->value_wrapper( 'html_type', get_bloginfo( 'html_type' ) );
    }

    public function get_text_direction() {
        return $this->value_wrapper( 'text_direction', get_bloginfo( 'text_direction' ) );
    }

    public function get_language() {
        return $this->value_wrapper( 'language', get_bloginfo( 'language' ) );
    }

    public function get_permalinks_structure() {
        $permalinks_structure = get_option( 'permalink_structure' );
        return $this->value_wrapper( 'permalink_structure', !empty( $permalinks_structure ) ? $permalinks_structure : 'missing' );
    }

    public function get_permalinks_structure_wp() {
        global $wp_rewrite;
        return $this->value_wrapper( 'permalink_structure_wp', $wp_rewrite->permalink_structure );
    }

    public function get_permalinks_rules() {
        global $wp_rewrite;
        return $this->value_wrapper( 'rewrite_rules', json_encode( $wp_rewrite->extra_rules_top ) );
    }

    //plugins setting
    public function get_plugins_from_json() {
        $all_plugins = $this->plugin_json->plugin_config_load();
        return $all_plugins;
    }

    public function plugin_active_status( $key, $json_val ) {
        //get installed status
        $actived_plugins = get_option( 'active_plugins' );
        $actived_arr     = array();
        foreach ( $actived_plugins as $plugin ) {
            array_push( $actived_arr, explode( "/", $plugin )[0] );
        }
        return $this->value_wrapper_plugin( $key, (in_array( $key, $actived_arr )) ? true : false, '', 'active_status' );
    }

    public function plugin_version( $key, $json_val ) {
        $check = $this->check_install_status( $key, $json_val );
        return $this->value_wrapper_plugin( $key, ($check['version'] == $json_val['version']) ? true : false, $json_val['version'], 'version' );
    }

    public function plugin_install_status( $key, $json_val ) {
        $check = $this->check_install_status( $key, $json_val );
        return $this->value_wrapper_plugin( $key, $check['install_status'], '', 'install_status' );
    }

    public function check_install_status( $key, $json_val ) {
        $all_plugins       = get_plugins();
        $installed_plugins = array();
        foreach ( $all_plugins as $checkkey => $value ) {
            $title                        = $value['Name'];
            $version                      = $value['Version'];
            $checkkey                     = explode( "/", $checkkey )[0];
            $installed_plugins[$checkkey] = array(
                "key"     => $checkkey,
                "name"    => $title,
                "version" => $version
            );
        }

        $installed = (array_key_exists( $key, $installed_plugins )) ? true : false;
        $version   = ($installed) ? $installed_plugins[$key]['version'] : null;
        $name      = ($installed) ? $installed_plugins[$key]['name'] : false;

        return array( 'install_status' => $installed, 'version' => $version, 'name' => $name );
    }

    public function get_cron_time( $timestamp, $cronKey ) {
//        $time = date_i18n( 'H:i:s', $timestamp, 1 );
        $time = get_date_from_gmt( date( 'Y-m-d H:i:s', $timestamp ), 'H:i:s' );
        return $time;
    }

    public function get_folder_struction() {
        $folders = array();
        $items   = glob( $this->wp_content_root . '/wp-content/*' );

        for ( $i = 0; $i < count( $items ); $i++ ) {
            if ( is_dir( $items[$i] ) ) {
                $add       = $items[$i];
                $folders[] = array(
                    'folder'    => str_replace( $this->wp_content_root, '', $add ),
                    'permision' => $this->get_permission( $items[$i] ),
                    'owner'     => $this->get_owner_user( $items[$i] )
                );
            }
        }
        return $folders;
    }

    public function get_permission( $path ) {
        $permission = substr( sprintf( '%o', fileperms( $path ) ), -3 );
        return $this->value_wrapper( 'permission', $permission );
    }

    public function get_owner_user( $path ) {
        $user = (function_exists( 'posix_getpwuid' )) ? posix_getpwuid( fileowner( $path ) ) : '';
        return $this->value_wrapper( 'owner', $user['name'] );
    }

    public function get_system_emails( $const ) {
        $value = (defined( $const )) ? constant( $const ) : "false";
        return $this->value_wrapper( $const, $value );
    }

}
