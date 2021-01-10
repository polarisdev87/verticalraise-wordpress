<?php

namespace classes\app\download_report;

use \classes\app\download_report\Results;

class Admin_Data
{

    private $results;

    /**
     * Class Constructor.
     */
    public function __construct() {
        $this->results = new Results();
    }

    public function init( $fundraiser_id ) {
        //get admin data

        $admin_data[] = $this->build_admin_row('0', $fundraiser_id);
        return $admin_data;
    }

    private function build_admin_row( $participant, $fundraiser_id ) {

        // Get the admin donation results
        $results = $this->results->get_results($fundraiser_id, $participant);

        // Prepare the row
        return $this->construct_row($results);
    }

    private function construct_row( $results ) {
        return array(
            'Admins', '', '', '', '', '', $results->supporters, $results->net_amount
        );
    }

}
