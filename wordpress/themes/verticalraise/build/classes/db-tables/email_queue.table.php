<?php

// Define the name of the class for easy access
$ct_class[basename(__FILE__)] = 'Email_Queue_Table';

// Load the parent class
load_class('db_custom_tables.class.php');

class Email_Queue_Table extends DB_Custom_Tables
{
    
    /**
     * @var string $table_name The name of the table
     */
    protected $table_name = "email_queue";
    
    /**
     * @var string $version    The version of this table
     */
    protected $version    = '1.2.1';

    /**
     * Class Constructor.
     */
    public function __construct() {
        parent::__construct($this->table_name, $this->version);
    }

    /**
     * Run the Wordpress custom table setup. The table 'email_queue' will be added to the database if it does not already exist.
     * @return void
     */
    public function run() {
        
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
          f_id mediumint(9) NOT NULL,
          u_id mediumint(9) NOT NULL,
          email varchar(255) NOT NULL,
          type tinyint(1) NOT NULL,
          channel tinyint(1) NOT NULL,
          share_type tinyint(1) DEFAULT 0 NOT NULL,
          from_name varchar(255) NOT NULL,
          role tinyint(1) DEFAULT 0 NOT NULL,
          disposable tinyint(1) DEFAULT 0 NOT NULL,
          accept_all tinyint(1) DEFAULT 0 NOT NULL,
          verified tinyint(1) DEFAULT 0 NOT NULL,
          sent tinyint(1) DEFAULT 0 NOT NULL,
          attempts tinyint DEFAULT 0 NOT NULL,
          moved tinyint(1) DEFAULT 0 NOT NULL,
          result varchar(255) NOT NULL,
          reason varchar(255) NOT NULL,
          logged_in tinyint(1) DEFAULT -1 NOT NULL,
          logged_in_u_id mediumint(9) DEFAULT -1 NOT NULL,
          parent tinyint(1) DEFAULT -1 NOT NULL,
          created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY  (id)
        ) ENGINE=InnoDB {$charset_collate};";

        return $sql;
    }
    
}