<?php

namespace classes\app\fundraiser;

use classes\models\tables\Donations;
use classes\models\tables\Fundraiser_Participants as Fundraiser_Participants_Table;
use classes\models\tables\Participant_Fundraiser_Details;
use classes\models\tables\Subgroup_Users;

class Fundraiser_Participants {

	public static function delete_participant() {

		$donations_table                      = new Donations();
		$subgroups_users_table                = new Subgroup_Users();
		$fundraiser_participants_table        = new Fundraiser_Participants_Table();
		$participant_fundraiser_details_table = new Participant_Fundraiser_Details();

		$f_id = $_POST['f_id'];
		$u_id = $_POST['u_id'];

		$supporters = $donations_table->get_number_supporters_by_user_id( $u_id, $f_id );
		if ( $supporters == 0 ) {

			$user_fundraisers = get_user_meta( $u_id, 'campaign_participations', true );
			$user_fundraisers = json_decode( $user_fundraisers, true );
			$index            = array_search( $f_id, $user_fundraisers );
			if ( $index !== false ) {
				unset( $user_fundraisers[ $index ] );
				$user_fundraisers = array_values( $user_fundraisers );
				update_user_meta( $u_id, 'campaign_participations', json_encode( $user_fundraisers ) );
			}

			$fundraiser_participants = get_post_meta( $f_id, 'campaign_participations', true );
			$fundraiser_participants = json_decode( $fundraiser_participants, true );
			$index                   = array_search( $u_id, $fundraiser_participants );
			if ( $index !== false ) {
				unset( $fundraiser_participants[ $index ] );
				$fundraiser_participants = array_values( $fundraiser_participants );
				update_post_meta( $f_id, 'campaign_participations', json_encode( $fundraiser_participants ) );
			}

			$success = $subgroups_users_table->delete_user_from_subgroup( $u_id, $f_id );
			$success = $fundraiser_participants_table->delete_fundraiser_participant( $u_id, $f_id );
			$success = $participant_fundraiser_details_table->delete_participant_fundraiser_details( $u_id, $f_id );

			wp_send_json( [ 'message' => 'Success, participant deleted.' ], 200 );

		} else {
			wp_send_json( [ 'message' => 'Error, participant has donations.' ], 400 );
		}

	}

}
