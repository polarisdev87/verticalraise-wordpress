<?php

namespace classes\models\tables;

/**
 * Retreive Payment records
 */
class Donations_Total
{

    /**
     * Class variables.
     */
    private $table_name = "donations_sum";  // Table name
    private $wpdb;                          // Wordpress Database Object

    /**
     * Class constructor.
     */

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    /**
     * Get the single record of a fundraiser_id.
     * @param  int   $fundraiser_id
     * @return mixed
     */
    public function get_single_sums_row( $fundraiser_id ) {
        return $this->wpdb->get_row("SELECT * FROM `{$this->table_name}` WHERE f_id = '" . $fundraiser_id ."' LIMIT 1", OBJECT);
        
    }

    /**
     * 
     * @global \classes\models\tables\type $wpdb
     * @param  object $myrow The result row object
     * @param  int    $value
     * @return void
     */
    public function update_donations_sum( $myrow, $value ) {
        $id     = $myrow->id;
        $amount = $myrow->amount + $value;
        $result = $this->wpdb->query("UPDATE `{$this->table_name}` SET amount = $amount WHERE id = $id");
    }

    /**
     * Insert a new record into the database
     * @param  int    $fundraiser_id
     * @param  int    $value
     * @return void
     */
    public function insert_donations_sum( $fundraiser_id, $value ) {
        $results = $this->wpdb->insert($this->table_name, array (
            'amount' => $value,
            'f_id'   => $fundraiser_id
                ), array ('%d', '%d')
        );
    }

    /**
     * Get TOTAL payment amount for a specifc fundraiser id.
     * @param  int   $fundraiser_id 
     * @return float $total
     */
    public function get_total_by_f_id( $fundraiser_id ) {
        $total = $this->wpdb->get_var($this->wpdb->prepare(
                        "
                    SELECT amount FROM `{$this->table_name}` WHERE f_id = '%d'
                 LIMIT 1", $fundraiser_id
        ));

        if ( $total == null || empty($total) ) {
            return 0;
        }
        return $total;
    }

}
