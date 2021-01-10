<?php

namespace classes\models\tables;

class GiveAmply {
	private $table_name = 'giveamply';

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
		$results = $wpdb->get_results( "SHOW TABLES LIKE '{$this->table_name}'" );
		if ( count( $results ) ) {
			return true;
		}

		return false;
	}

	public function insert( $f_id, $org_id, $request, $response ) {
		$this->wpdb->insert(
			$this->table_name,
			array(
				'f_id' => $f_id,
				'org_id' => $org_id,
				'request' => $request,
				'response' => $response,
			),
			array(
				"%d",
				"%d",
				"%s",
				"%s",
			)
		);

		$insert_id = $this->wpdb->insert_id;

		return $insert_id;
	}

	public function get_org_id( $f_id ) {
        return $this->wpdb->get_var("SELECT `org_id` FROM `{$this->table_name}` WHERE f_id = " . $f_id . " LIMIT 1");
    }


}
