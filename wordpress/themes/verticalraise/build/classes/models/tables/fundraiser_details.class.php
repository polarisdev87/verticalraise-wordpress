<?php
namespace classes\models\tables;
/**
 * Stores Fundraiser Details
 */
class Fundraiser_Details
{
    /**
     * Class variables.
     */
    private $table_name = "fundraiser_details";  // Table name.
    private $wpdb;                               // Wordpress Database Object.
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
    public function table_exist() {
        global $wpdb;
        $results = $wpdb->get_results("SHOW TABLES LIKE '{$this->table_name}'");
        if ( count ( $results ) ) {
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
        return $wpdb->get_row("SELECT * FROM `{$this->table_name}` WHERE f_id = " . $fundraiser_id, OBJECT);
    }

    /**
     * Insert a new record into the database
     * @param array $fundraiser
     * @return void
     */
    public function insert_fundraiser( $fundraiser ) {
        global $wpdb;
        $results = $wpdb->insert( $this->table_name, array (
            'f_id'               => $fundraiser['id'],
            'start_date'         => $fundraiser['start_date'],
			'end_date'           => $fundraiser['end_date'],
			'secondary_end_date' => $fundraiser['sec_date'],
            'goal'               => $fundraiser['goal'],
            'transferred'        => $fundraiser['transferred'],
		),
		array ('%d', '%s', '%s', '%f', '%d')
        );
    }

    /**
     * Updates record in database
     * @param array $fundraiser
     * @return void
     */
    public function update_fundraiser( $fundraiser ) {
		global $wpdb;

		$sec_date = ( ! empty( $fundraiser['sec_date'] ) ) ? $fundraiser['sec_date'] : null;

        $results = $wpdb->update($this->table_name, array (
            'start_date'   		 => $fundraiser['start_date'],
			'end_date'     		 => $fundraiser['end_date'],
			'secondary_end_date' => $sec_date,
            'goal'         		 => $fundraiser['goal'],
                ), array (
                    'f_id' => $fundraiser['id']
                ), array ('%d', '%s', '%s', '%f')
        );
    }

}
