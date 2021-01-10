<?php

namespace classes\app\download_report;

use \classes\app\download_report\Sheet;
use \classes\app\download_report\Pages;
use \classes\app\download_report\Output;

class Download_Report
{
    
    private $fundraiser_id;
    
    /**
     * Class constructor.
     * @param int $f_id The fundraiser ID
     */
    public function __construct($f_id) {
        
        $this->fundraiser_id    = $f_id;

        $this->sheet            = new Sheet($this->fundraiser_id);
        $this->pages            = new Pages($this->fundraiser_id);
        $this->output           = new Output();

    }
    
    /**
     * Connect the data and output the Spreadsheet for download.
     */
    public function init() {
        
        // Wrapper
        $sheet = $this->sheet->get_sheet();
        
        // Page data
        $pages = $this->pages->get_pages();
        
        // Output the spreadsheet
        $this->output->output($sheet, $pages);
        
    }
    
}