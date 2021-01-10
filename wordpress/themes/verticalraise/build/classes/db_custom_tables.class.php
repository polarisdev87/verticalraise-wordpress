<?php

/**
 * This class serves as parent class for adding and updating Wordpress custom table database schema
 */
class DB_Custom_Tables
{
    
    protected $wpdb;    // Wordpress database object
    
    protected $table;   // Table name
    
    protected $version; // Version number
    
    /**
     * Class Constructor.
     */
    public function __construct($table, $version) {
        global $wpdb;
        
        $this->wpdb    = $wpdb;
        $this->table   = $table;
        $this->version = $version;

        if ( !isset($GLOBALS['phpunit_test_running']) ) {
            $this->permission();
        }
    }
    
    /**
     * Check if permission is set to run the database upgade.
     */
    protected function permission() {
        // is admin?
        if ( !is_admin() ) {
            // throw an error
            throw new Exception("Error: User is not an admin");
        }
    }
        
    /**
     * Check if a specific table exists.
     * @param  string $table The table name
     * @return bool
     */
    protected function table_exists($table) {
        if ( $this->wpdb->get_var("SHOW TABLES LIKE '{$table}'") == $table ) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Create the table.
     * @param  string $sql The sequel to run
     * @return The result of the query
     */
    protected function create($sql) {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        return dbDelta( $sql );
    }
    
    /**
     * Store a version for the table.
     * @param  string $table   The table name
     * @param  string $version The version number
     * @return bool
     */
    protected function add_version($table, $version) {
        $updated = update_option( '_table_' . $table . '_version', $version );
        return $updated;
    }
    
    /**
     * Store an upgraded date for the table.
     * @param string $table
     */
    protected function add_upgraded_date($table) {
        $updated = update_option( '_table_' . $table . '_upgraded_date', current_time('timestamp') );
        return $updated;
    }
    
    /**
     * Return the table name.
     * @return string $this->table
     */
    public function get_table() {
        return $this->table;
    }
    
    /**
     * Return table's new version.
     * @return string $this->version
     */
    public function get_new_version() {
        return $this->version;
    }
    
    /**
     * Return table's currently stored version.
     * @return string $this->version
     */
    public function get_old_version() {
        $option = get_option( '_table_' . $this->table . '_version' );
        if ( $option ) {
            return $option;
        } else {
            return 'missing';
        }
    }
    
    public function get_upgraded_date() {
        $option = get_option( '_table_' . $this->table . '_upgraded_date' );
        if ( $option ) {
            return $option;
        } else {
            return 'missing';
        }
    }
    
    /**
     * Return the table exists status.
     * @return bool
     */
    public function get_table_exists() {
        return $this->table_exists($this->table);
    }

}