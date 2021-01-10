<?php

namespace classes\app\download_report;

use \classes\models\tables\Email_Input;

/**
 * Get the potential donor data from the `email_input` table by fundraiser id and prepare for Excel Spreadsheet output
 */

class Potential_Donator_Data
{

    private $email_input; // Email_Input object

    /**
     * Class Constructor.
     */
    public function __construct() {
        $this->email_input  = new Email_Input();
    }
    
    public function init($fundraiser_id) {
        // Grab all the potential donor records
	    $potential_donors  = $this->email_input->get_potential_donors_by_fid($fundraiser_id);

        if ( !empty($potential_donors) ) {
            
            $data = array();
            
            /*echo "<pre>";
            print_r($potential_donors);
            echo "</pre>";
            exit();*/
            
            // Setup to output for spreadsheet
            
            // Set Headers
            $headers = $this->set_headers();
            $data = array_merge($data, $headers);
            
            // Set Body
            $body = $this->set_body($potential_donors);
            $data = array_merge($data, $body);
            
            return $data;

        }
        
        return array();
    }
    
    /**
     * Set the spreadsheet headers for this data.
     * @return array
     */
    private function set_headers() {
        $data[] = array('', '', '', '', '');
        $data[] = array('Potential Donors', '', '', '', '');
        $data[] = array('Participant', 'Potential Donor', '', '', '');
        
        return $data;
    }
    
    /**
     * Set the body using the potential donor data.
     * @param array $potential_donors The potential donor records.
     * @return array $data            The body
     */
    private function set_body($potential_donors) {
        $data = array();
        
        foreach ( $potential_donors as $pd ) {
            $user_info = get_userdata($pd['u_id']);

            if ( $pd['u_id'] == 0 ) {
                $data[] = array('', $pd['email'], '', '', '');
            } else {
                if ( !empty($user_info->display_name) && !empty($user_info->user_email) ) {
                    $det = $user_info->display_name . ' <' . $user_info->user_email . '>';
                    $data[] = array($det, $pd['email'], '', '', '');
                } else {
                    $data[] = array('', $pd['email'], '', '', '');
                }
            }
        }
        
        return $data;
    }

}