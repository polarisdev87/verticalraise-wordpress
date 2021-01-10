<?php

namespace classes\models\tables;

class Subgroup_Users {
	private $table_name = 'subgroup_users';

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

	public function insert( $u_id, $subgroup_id ) {
		$this->wpdb->insert(
			$this->table_name,
			array(
				'subgroup_id' => $subgroup_id,
				'u_id'        => $u_id,
			)
		);

		$insert_id = $this->wpdb->insert_id;

		return $insert_id;
	}


	public function userInSubgroupsOfFundraiser($u_id, $f_id) {
		$found = $this->wpdb->get_var( $this->wpdb->prepare( " SELECT count(subgroup_id) AS found FROM {$this->table_name} WHERE u_id = %s AND subgroup_id IN ( SELECT id FROM subgroups WHERE f_id = %s ) ", array($u_id, $f_id ) ));
		return $found;
	}

	public function getUserSubgroupName( $u_id, $f_id ) {

		$query = $this->wpdb->prepare( " SELECT s.name FROM subgroups s LEFT JOIN {$this->table_name} su on s.id = su.subgroup_id WHERE su.u_id = %s AND s.f_id = %s ", array(
			$u_id,
			$f_id
		));

		$subgroup_name = $this->wpdb->get_var( $query );

		return $subgroup_name;
	}

	public function getUserSubgroupId($u_id, $f_id){

		$query = $this->wpdb->prepare( " SELECT s.id FROM subgroups s LEFT JOIN {$this->table_name} su on s.id = su.subgroup_id WHERE su.u_id = %s AND s.f_id = %s ", array(
			$u_id,
			$f_id
		));

		$subgroup_id = $this->wpdb->get_var($query);

		return $subgroup_id;

	}


	public function delete_user_from_subgroup( $u_id, $f_id ) {

		$subgroup_id = $this->getUserSubgroupId( $u_id, $f_id );

		if ( $subgroup_id ) {
			return $this->wpdb->delete( $this->table_name, array(
				'subgroup_id' => $subgroup_id,
				'u_id'        => $u_id
			), array( '%d', '%d' ) );
		}

		return false;

	}

}
