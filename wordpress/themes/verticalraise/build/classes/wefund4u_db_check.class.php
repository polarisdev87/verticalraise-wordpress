<?php

/**
 * Use this file to setup mysql database tables for the site
 */
class Wefund4u_DB_Check
{
    
    private $files;
    private $db_table;
    
    /**
     * Class Constructor.
     */
    public function __construct() {
        $this->files = load_config('db-tables.config.php');
    }
    
    /**
     * Check to see if the tables exist.
     */
    public function check() {
        
        if ( !is_admin() ) {
            return;
        }
        
        $results = array();

        // Try to setup each table if it does not exist
        foreach ( $this->files as $file ) {
            try {  
                // Run the class setup for each table

                $this->db_table = new $ct_class[str_replace('.table.php', '', $file)]();

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

            }
        }
        
        return $results;
        
    }
    
}