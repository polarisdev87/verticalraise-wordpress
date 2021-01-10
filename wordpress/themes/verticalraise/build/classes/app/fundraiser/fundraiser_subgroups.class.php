<?php

namespace classes\app\fundraiser;

use classes\models\tables\Subgroups;
use classes\models\tables\Subgroup_Users;


class Fundraiser_Subgroups {

	const MAX_SUBGROUPS = 30;

	public static function participant_select_subgroup() {

		global $wpdb;

		$f_id        = $_POST['f_id'];
		$u_id        = $_POST['u_id'];
		$subgroup_id = $_POST['subgroup_id'];


		$subgroup_users = new Subgroup_Users();

		//validate subgroup is from fundraiser

		//validate user is not in any subgroup of that fundraiser
		$isInSubgroups = $subgroup_users->userInSubgroupsOfFundraiser( $u_id, $f_id );
		if ( $isInSubgroups ) {
			wp_send_json( array( 'error' => 'You are already in a subgroup' ), 400 );
		}

		//store user in subgroup
		$insert_id = $subgroup_users->insert( $u_id, $subgroup_id );
		if ( $insert_id === 0 ) {
			wp_send_json( [ 'message' => 'Success' ], 200 );
		}

	}
}
