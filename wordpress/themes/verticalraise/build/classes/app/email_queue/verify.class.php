<?php

namespace classes\app\email_queue;

use \classes\models\tables\Email_Queue as Email_Queue_Verify;
//use \classes\vendors\TheChecker;
use \classes\vendors\NeverBounce;

class Verify
{

    public function __construct() {
        $this->email_queue = new Email_Queue_Verify();
    }

    // runs every minute
    public function verify() {

        // Every Minute: Grab the last 500 records and process
		$records = $this->email_queue->get_ready_to_verify();

		echo "email records";
		echo "<pre>";
		print_r($records);
		echo "</pre>";


        // Send the records for verification
        $results = $this->api($records);

        // Process results
        $this->process($results);

    }

    // Process results
    private function process($results) {
        // Process valid results
        if ( !empty($results['valid']) ) {
            $this->update($results['valid'], 1);
        }
        // Process invalid results
        if ( !empty($results['invalid']) ) {
            $this->update($results['invalid'], 2);
        }
    }

    private function update($results, $status) {
        foreach ( $results as $id => $result ) {
            $this->email_queue->update_verified($id, $status, $result);
        }
    }

    private function api($records) {
        $this->neverbounce = new NeverBounce();
        $results = $this->neverbounce->verify_multiple($records);

        return $results;
    }

}
