<?php

/**
 * Use this file to load database custom table classes
 */
class Wefund4u_DB_Custom_Tables
{
    
    private $files;
    private $classes;
    private $db_table;
    
    /**
     * Class Constructor.
     */
    public function __construct() {
        $this->files = load_config('db-tables.config.php');
        $this->classes = array();
        $this->load();
    }
    
    /**
     * Load each custom table class file from the config.
     */
    private function load() {

        // Load the classes
        foreach ( $this->files as $file ) {
            try {  
                include_once( get_template_directory() . '/classes/db-tables/' . $file );
                
                // Collect class names
                $this->classes[$file] = $ct_class[$file];
                
            } catch (Exception $e) {
                if ( extension_loaded('newrelic') ) { // Ensure PHP agent is available
                    newrelic_notice_error($e->getMessage(), $e);
                }
            }
        }
    }
    
    /**
     * Run through the list of custom tables from the congif and setup the ones that do not exist.
     * @return int $result The number of tables added
     */
    public function setup() {
        
        $result = 0;

        // Try to setup each table if it does not exist
        foreach ( $this->files as $file ) {
            try { 
                
                // Run the class setup for each table
                $this->db_table = new $this->classes[$file]();
                $_result = $this->db_table->run();
                $result = $result + $_result;
                
                    
            } catch (Exception $e) {
                if ( extension_loaded('newrelic') ) { // Ensure PHP agent is available
                    newrelic_notice_error($e->getMessage(), $e);
                }
            }
        }
        if (! isset($GLOBALS['phpunit_test_running']) ){
            set_transient('table_updated', $result, 5);
            wp_redirect( admin_url( '/admin.php?page=database-upgrades' ), 301 );
            exit;
        }
        return $result;
    }
    
    /**
     * Cross compare config array of custom tables against Wefund4u database to see which exist.
     * @return array Array of results
     */
    public function check() {

        $results = array();

        // Try to setup each table if it does not exist
        foreach ( $this->files as $file ) {
            try {  
                // Run the class setup for each table
                $this->db_table = new $this->classes[$file]();

                // Table name
                $table = $this->db_table->get_table();
                
                // Check for table
                $check = $this->db_table->get_table_exists();
                
                // Versions
                $new_version = $this->db_table->get_new_version();
                $old_version = $this->db_table->get_old_version();
                
                // Upgraded date
                $upgraded_date = $this->db_table->get_upgraded_date();
                
                if ( $check ) {
                    $results[$table]['exists'] = 1;
                } else {
                    $results[$table]['exists'] = 0;
                }
                
                $results[$table]['table_name'] = $table;
                $results[$table]['new_version'] = $new_version;
                $results[$table]['old_version'] = $old_version;
                $results[$table]['upgraded_date'] = $upgraded_date;
                    
            } catch (Exception $e) {
                if ( extension_loaded('newrelic') ) { // Ensure PHP agent is available
                    newrelic_notice_error($e->getMessage(), $e);
                }
            }
        }
        
        return $results;
        
    }
    
    public function avaiable_count () {
        $tables = $this->check();
        $count = 0;
        foreach ($tables as $key => $table) {
            if ($table['exists'] == 0 ||( $table['old_version'] != $table['new_version'] )) {
                $count ++;
            }
        }
        return $count;
    }
}