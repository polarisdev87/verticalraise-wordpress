<?php

namespace classes\models\tables;

class Stripe_Transfers
{
    const TRANSFER_BY_CRON = '0';
	const TRANSFER_BY_CONNECT_PAGE = '1';
	const TRANSFER_ERROR = '0';
	const TRANSFER_SUCCESS = '1';
	
    private $table_name = 'stripe_transfers';

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

	public function insert( $f_id, $message, $success, $transfer_type ) {
		$this->wpdb->insert(
			$this->table_name,
			array(
				'f_id'          => $f_id,
				'message'       => $message,
				'success'       => $success,
				'transfer_type' => $transfer_type,
			)
		);

		$insert_id = $this->wpdb->insert_id;

		return $insert_id;
	}

   
}
