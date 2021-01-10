<?php

namespace classes\app\download_report;

use \classes\app\download_report\Subgroup_Participant_Data;
use \classes\app\download_report\Subgroup_Admin_Data;
use \classes\app\download_report\Donator_Data;
use \classes\app\download_report\Potential_Donator_Data;

class Subgroup_Pages
{
    
    /**
     * Class constructor.
     * @param int $f_id The fundraiser ID
     */
    public function __construct($f_id) {
        
        $this->fundraiser_id    = $f_id;
        
        $this->participant_data = new Subgroup_Participant_Data();
        $this->admin_data       = new Subgroup_Admin_Data();
        $this->donator_data     = new Donator_Data();
        $this->p_donator_data   = new Potential_Donator_Data();
    }
    
    /**
     * Get each page.
     */
    public function get_pages() {
        $data             = new \stdClass();

        $data->page1      = $this->page1();    
        $data->page2      = $this->page2();
        
        return $data;
    }

    /**
     * First Page: Participant sharing records.
     */
    public function page1() {
        $participant_data =  $this->participant_data->init($this->fundraiser_id);
        $admin_data       =  $this->admin_data->init($this->fundraiser_id);

        //return $participant_data;
        return array_merge($participant_data, $admin_data);
    }
    
    /**
     * Second Page: Donation records & potential donors list.
     */
    public function page2() {
        $section1         = $this->donator_data->init($this->fundraiser_id);
        $section2         = $this->p_donator_data->init($this->fundraiser_id);
        
        return array_merge($section1, $section2);
       
    }

}