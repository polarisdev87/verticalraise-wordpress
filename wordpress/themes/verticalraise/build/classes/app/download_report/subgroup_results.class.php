<?php

namespace classes\app\download_report;

use \classes\models\tables\Donations;
use \classes\models\tables\Participant_Fundraiser_Details;
use \classes\models\tables\Subgroup_Users;

class Subgroup_Results
{
    
    /**
     * Class Constructor.
     */
    public function __construct() {
        load_class('participant_records.class.php');
        $this->payments         = new Donations();
        $this->sharing          = new Participant_Fundraiser_Details();
    }
    
    public function get_results($fid, $uid) {
        // Initialize structure and baseline values
        $data = $this->initialize();

        // Sharing values
        $data = $this->get_sharing_values($data, $fid, $uid);
        
        // Payment values
        $data = $this->get_payment_values($data, $fid, $uid);

        // Subgroup name
	    $data = $this->get_subgroup_name($data, $fid, $uid);

        return $data;
    }
    
    private function get_payment_values($data, $fid, $uid) {
        // Get the total of all payments for the user
        $data->net_amount = $this->payments->get_total_by_user_id($uid, $fid);

        // Get the total number of supporters for the user
        $data->supporters = $this->payments->get_number_supporters_by_user_id($uid, $fid);
        
        return $data;
    }
    
    private function get_sharing_values($data, $fid, $uid) {
        $results = $this->sharing->get_single_row($fid, $uid);
        
        if ( $results == null )
            return $data;
        
        $data->email    = $results->email;
        $data->smsp     = $results->sms;
        $data->flyerp   = $results->flyers;
        $data->parents  = $results->parents;
        $data->twitter  = $results->twitter;
        $data->facebook = $results->facebook;
        
        return $data;
    }

	private function get_subgroup_name( $data, $fid, $uid ) {

    	$subgroup_users_table = new Subgroup_Users();

    	$data->subgroup          = $subgroup_users_table->getUserSubgroupName( $uid, $fid );

    	return $data;
	}

	private function initialize() {
		$data = new \stdClass();

		$data->net_amount = 0;
		$data->supporters = 0;
		$data->parents    = 0;
		$data->email      = 0;
		$data->facebook   = 0;
		$data->twitter    = 0;
		$data->sms        = 0;
		$data->flyerp     = 0;
		$data->smsp       = 0;
		$data->subgroup   = "";

		return $data;
	}
    
}