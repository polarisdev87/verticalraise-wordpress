<?php

namespace classes\app\download_report;

use \classes\models\tables\Donations;
use \classes\models\tables\Fundraiser_Participants;
use \classes\app\download_report\Subgroup_Results;
use \classes\models\tables\Subgroups;

class Subgroup_Participant_Data
{

	private $payments;
	private $participants;
	private $results;

	/**
	 * Class Constructor.
	 */
	public function __construct() {
		$this->payments     = new Donations();
		$this->participants = new Fundraiser_Participants();
		$this->results      = new Subgroup_Results();
	}

	public function init($fundraiser_id) {
		// Get list of participants
		$participants = $this->participants->get_filtered_participant_ids_by_fid($fundraiser_id);  // Participant IDs

		if ( empty($participants) ) {
			return array($this->return_empty());
		}

		$data = array();

		// Build the rows of results for each participant
		foreach ( $participants as $participant ) {
			$data[] = $this->build_row($participant, $fundraiser_id);
		}

		return $data;
	}

	private function build_row($participant, $fundraiser_id) {
		// Get the participant's user record
		$user_info = get_userdata($participant);

		// Get the participant's fundraiser results
		$results   = $this->results->get_results($fundraiser_id, $participant);

		// Prepare the row
		return $this->construct_row($results, $user_info);

	}

	private function construct_row($results, $user_info) {
		return [
			$user_info->display_name,
			$user_info->user_email,
			$results->subgroup,
			$results->parents,
			$results->email,
			$results->facebook,
			$results->smsp,
			$results->supporters,
			$results->net_amount
		];
	}

	private function return_empty() {
		return array('No Participants Found', '', '', '', '', '', '', '', '');
	}

	public function get_subgroup_aggregation_for( $fid ) {

		$subgroups_table = new Subgroups();
		$subgroups_list  = $subgroups_table->getSubgroups( $fid );
		if ( empty( $subgroups_list ) ) {
			return false;
		}

		$data = $this->init( $fid );

		$subgroups = [];
		foreach ( $subgroups_list as $row ) {
			$subgroups[ $row['name'] ] = (object) array(
				'parents'    => 0,
				'email'      => 0,
				'facebook'   => 0,
				'smsp'       => 0,
				'supporters' => 0,
				'net_amount' => 0
			);
		}

		if(isset ($data[0][0]) && $data[0][0] != "No Participants Found"){
			foreach ( $data as $row ) {
				$subgroups[ $row[2] ]->parents    += $row[3];
				$subgroups[ $row[2] ]->email      += $row[4];
				$subgroups[ $row[2] ]->facebook   += $row[5];
				$subgroups[ $row[2] ]->smsp       += $row[6];
				$subgroups[ $row[2] ]->supporters += $row[7];
				$subgroups[ $row[2] ]->net_amount += $row[8];
			}
		}

		return $subgroups;
	}

}
