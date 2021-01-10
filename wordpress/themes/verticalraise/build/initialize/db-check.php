<?php

/**
 * Use this file to setup mysql database tables for the site
 */
if ( ! defined( 'ABSPATH' ) ) exit; 

$db_tables_files = load_config('db-tables.config.php');

class Wefund4u_DB_Check
{
    public function __construct($files) {
        $this->files = $files;
    }
    
    private function check() {  
        
    }
    
    private function setup($files) {
        
        // If post is a run tool
        
        if ( !is_admin() ) {
            return;
        }

        // Try to setup each table if it does not exist
        foreach ( $files as $file ) {
            try {  
                // Run the class setup for each table
                $this->load_table_class($file);
                $this->db_table = new $class(); 
                $this->db_table->run();
                    
            } catch (Exception $e) {

            }
        }
    }

    private function load_table_class($class) {
        include_once( get_template_directory() . '/classes/db-tables/' . $class );
    }
    
}

$db_setup = new Wefund4u_DB_Check($db_tables_files);