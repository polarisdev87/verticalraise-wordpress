<?php

namespace classes\models\tables;

/**
 * Retreive Payment records
 */
class Donations_Count
{

    /**
     * Class variables.
     */
    private $table_name = "donations_count";  // Table name
    private $wpdb;                            // Wordpress Database Object

    /**
     * Class constructor.
     */

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    /**
     * This method checks if database table exist
     * @return boolean
     */

    public function table_exist(){
        global $wpdb;
        $results = $wpdb->get_results("SHOW TABLES LIKE '{$this->table_name}'");
        if ( count ( $results ) ){
            return true;
        }
        return false;
    }

    /**
     * Get the single record of a fundraiser_id.
     * @param  int   $fundraiser_id
     * @return mixed
     */
    public function get_single_count_row( $fundraiser_id ) {
        global $wpdb;
        $count = $wpdb->get_row("SELECT * FROM `{$this->table_name}` WHERE f_id = " . $fundraiser_id, OBJECT);
    }

    /**
     * 
     * @global \classes\models\tables\type $wpdb
     * @param  object $myrow The result row object
     * @param  int    $value
     * @return void
     */
    public function update_donations_count( $myrow, $value ) {
        global $wpdb;
        $id     = $myrow->id;
        $count = $myrow->count + $value;
        $result = $wpdb->query("UPDATE `{$this->table_name}` SET count = $count WHERE id = $id");
    }

    /**
     * Insert a new record into the database
     * @param  int    $fundraiser_id
     * @param  int    $value
     * @return void
     */
    public function insert_donations_count( $fundraiser_id, $value ) {
        global $wpdb;
        $results = $wpdb->insert($this->table_name, array (
            'count' => $value,
            'f_id'   => $fundraiser_id
                ), array ('%d', '%d')
        );
    }

    /**
     * Get donation count TOTAL for a specific fundraiser id.
     * @param  int   $fundraiser_id 
     * @return float $total
     */
    public function get_total_by_f_id( $fundraiser_id ) {
        $total = $this->wpdb->get_var($this->wpdb->prepare(
                        "
                    SELECT count FROM `{$this->table_name}` WHERE f_id = '%d'
                ", $fundraiser_id
        ));

        if ( $total == null || empty($total) ) {
            return 0;
        }
        return $total;
    }

}
