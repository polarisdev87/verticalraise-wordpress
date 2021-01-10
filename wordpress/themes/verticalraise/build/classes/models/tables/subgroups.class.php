<?php

namespace classes\models\tables;

class Subgroups {
	private $table_name = 'subgroups';

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

	public function insert( $name, $f_id ) {
		$this->wpdb->insert(
			$this->table_name,
			array(
				'name' => $name,
				'f_id' => $f_id,
			),
			array(
				"%s",
				"%d"
			)
		);

		$insert_id = $this->wpdb->insert_id;

		return $insert_id;
	}

	public function update($name, $s_id, $f_id){
		return $this->wpdb->update(
			$this->table_name,
			array(
				'name' => $name,
			),
			array(
				'id'=> $s_id,
				'f_id' => $f_id,
			)
		);
	}

	public function getSubgroups($f_id){
		$subgroups = $this->wpdb->get_results( $this->wpdb->prepare( " SELECT id, name FROM {$this->table_name} WHERE f_id = %s ", array($f_id) ), ARRAY_A );
		return $subgroups;
	}

	public function isFundraiserSubgroup($s_id, $f_id){
		$found = $this->wpdb->get_var( $this->wpdb->prepare( " SELECT count(id) as found FROM {$this->table_name} WHERE id = %s AND f_id = %s ", array($s_id, $f_id) ) );
		return $found;
	}

}
