<?php

// Define the name of the class for easy access
$ct_class[basename(__FILE__)] = 'Site_Admin_Table';

// Load the parent class
load_class('db_custom_tables.class.php');

class Site_Admin_Table extends DB_Custom_Tables
{
    
    /**
     * @var string $table_name The name of the table
     */
    protected $table_name = "site_admin";
    
    /**
     * @var string $version    The version of this table
     */
    protected $version    = '1.0.1';

    /**
     * Class Constructor.
     */
    public function __construct() {
        parent::__construct($this->table_name, $this->version);
    }

    /**
     * Run the Wordpress custom table setup. The table 'cron_log' will be added to the database if it does not already exist.
     * @return void
     */
    public function run() {
       
        // See if the table was just created
        if ( $this->table_exists($this->table_name) == false || ($this->table_exists($this->table_name) && $this->get_old_version() != $this->version) ) {
                 // Gather the SQL
            $sql = $this->sql($this->table_name, $this->wpdb->get_charset_collate());
            
            // Run the SQL
            $created = $this->create($sql);
                
            // Store the table version
            $this->add_version($this->table_name, $this->version);

            // Store the upgraded date
            $this->add_upgraded_date($this->table_name);

            return 1;
        }
        return 0;
    }

    /**
     * Return the Wordpress custom table specific SQL.
     *
     * @param  string $table_name      The table name
     * @param  string $charset_collate The charset collate value
     *
     * @return string $sql             The SQL to run
     */
    private function sql($table_name, $charset_collate) {

        $sql = "CREATE TABLE {$table_name} (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          uid mediumint(11) NOT NULL,
          PRIMARY KEY  (id)
        ) ENGINE=InnoDB {$charset_collate};";

        return $sql;
    }
    
}