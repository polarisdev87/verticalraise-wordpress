<?php

namespace classes\app\download_report;

use \classes\models\tables\Donations;
use \classes\models\tables\Fundraiser_Participants;
use \classes\app\download_report\Results;

class Participant_Data
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
        $this->results      = new Results();
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
    
}