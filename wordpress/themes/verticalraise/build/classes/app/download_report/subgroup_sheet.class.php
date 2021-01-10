<?php

namespace classes\app\download_report;

class Subgroup_Sheet
{

    private $fundraiser_id;

    /**
     * Class constructor.
     * @param int $f_id The fundraiser ID
     */
    public function __construct( $f_id ) {

        $this->fundraiser_id = $f_id;
    }

    /**
     * Create the sheet object.
     */
    public function get_sheet() {
        $sheet = new \stdClass();

        $sheet->author   = "VerticalRaise";
        $sheet->filename = $this->get_filename();
        $sheet->header1  = $this->get_header1();
        $sheet->header2  = $this->get_header2();

        return $sheet;
    }

	public function get_header1() {
		return array(
			'Participant Name'  => 'string',
			'Participant Email' => 'string',
			'Subgroup'          => 'string',
			'Parent Shares'     => 'integer',
			'Email Shares'      => 'integer',
			'Facebook Shares'   => 'integer',
			'SMS Donations'     => 'dollar',
			'Total Donations'   => 'integer',
			'Total Raised'      => 'dollar'
		);
	}

    public function get_header2() {
        return array (
            'Donor Name'  => 'string',
            'Donor Email' => 'string',
            'Total'       => 'dollar',
            'Recipient'   => 'string',
            'Date'        => 'string'
        );
    }

    public function get_filename() {
        $date      = date('d/m/Y');
        $file_name = "Report-{$this->fundraiser_id}-{$date}.xlsx";

        return $file_name;
    }

}
