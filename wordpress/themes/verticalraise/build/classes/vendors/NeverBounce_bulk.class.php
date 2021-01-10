<?php

namespace classes\vendors;

/**
 * This class connects to the Neverbounce API to validate emails on the fly
 * Documentation: https://api.neverbounce.com
 * 
 *
 */
class NeverBounce_Bulk
{

    /**
     * Class variables.
     */
    private $api_key;                                                // API Key
    private $success_statuses = ['valid', 'unknown', 'catchall'];     // Success statuses
    private $failure_statuses = ['invalid', 'disaposable'];            // Failure statuses
    private $block            = ['role_account', 'disposable_email']; // Responses to block from sending

    /**
     * Public constructor.
     */

    public function __construct() {
        // define( '_NEVERBOUNCE_API_DEV_KEY', 'secret_bfe0c26b9eb815e0f76f1b7491d68c8b');
        $this->api_key = (_SERVER_TYPE == 'dev') ? _NEVERBOUNCE_API_DEV_KEY : _NEVERBOUNCE_API_LIVE_KEY;
    }

    public function bulk( $emails ) {

        try {
            $create_job = $this->create_job($emails);

            if ( isset($create_job["status"]) && $create_job["status"] == 'success' ) {
                $job_id = $create_job["job_id"];

                //get status
                //$start = $this->job_start($job_id);
                $loop = $this->settimeout($job_id);
                if ( $loop == 'complete' ) {
                    //get result
                    $result = $this->get_result($job_id);                   
                } else {
                    $result = $loop;
                }

                return $result;
            }
        } catch ( Exception $e ) {
            if ( extension_loaded('newrelic') ) { // Ensure PHP agent is available
                newrelic_notice_error($e->getMessage(), $e);
            }
            return 'failure';
        }
    }

    public function settimeout( $job_id, $i = 0 ) {        
        if ($i > 10) {
            $data = 'timeout';
        }
        $status = $this->get_status($job_id);
        
        switch ($status['job_status']) {
            case 'under_review':
            case 'queued':
            case 'failed':
                $data = 'failed';
                break;
            case 'complete':
                $data = 'complete';
                break;
            case 'running':
                $data = $this->settimeout($job_id, $i);
                break;
            case 'parsing':
            case 'waiting':
            case 'waiting_analyzed':
            case 'uploading':
                break;
            default:
                break;
               
        }  
        return $data;
    }

    /**
     * Create job
     */
    public function create_job( $emails ) {
        try {
            $param_data = '{
                "key": "' . $this->api_key . '",
                "input_location": "supplied",
                "filename": "test.csv",
                "auto_start": true,
                "auto_parse": true,
                "input": ' . json_encode($emails) . '
            }';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.neverbounce.com/v4/jobs/create');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array (
                "Content-Type: application/json"
            ));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response, true);
        } catch ( Exception $e ) {
            if ( extension_loaded('newrelic') ) { // Ensure PHP agent is available
                newrelic_notice_error($e->getMessage(), $e);
            }
            return 'failure';
        }
    }

    public function job_start( $job_id ) {
        try {
            $param_data = '{
                "key": "' . $this->api_key . '",          
                "job_id": ' . $job_id . '
            }';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.neverbounce.com/v4/jobs/start');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array (
                "Content-Type: application/json"
            ));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response, true);
            
        } catch ( Exception $e ) {
            if ( extension_loaded('newrelic') ) { // Ensure PHP agent is available
                newrelic_notice_error($e->getMessage(), $e);
            }
            return 'failure';
        }
    }

    public function get_result( $job_id ) {

        try {
            $ch = curl_init();

            curl_setopt_array($ch, array (
                CURLOPT_URL            => 'https://api.neverbounce.com/v4/jobs/results?key=' . $this->api_key . '&job_id=' . $job_id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => "",
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => "GET"
            ));

            $response = curl_exec($ch);
            curl_close($ch);

            $results = json_decode($response, true);
            $results = $results["results"];
            $valid   = array ();
            $invalid = array ();
            foreach ( $results as $result ) {
                $verification = $result['verification'];
                if ( !$this->validate($verification) ) {
                    array_push($invalid, $result['data']['email']);
                }
            }

            $delete = $this->delete_job($job_id);
            return $invalid;
        } catch ( Exception $e ) {
            if ( extension_loaded('newrelic') ) { // Ensure PHP agent is available
                newrelic_notice_error($e->getMessage(), $e);
            }

            return 'failure';
        }
    }

    public function get_status( $job_id ) {
        try {
            $ch = curl_init();

            curl_setopt_array($ch, array (
                CURLOPT_URL            => 'https://api.neverbounce.com/v4/jobs/status?key=' . $this->api_key . '&job_id=' . $job_id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => "",
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => "GET"
            ));

            $_response = curl_exec($ch);

            $result = json_decode($_response, true);

            curl_close($ch);
            if ( $result['status'] == 'success' ) {
                return $result;
            }
        } catch ( Exception $e ) {
            if ( extension_loaded('newrelic') ) { // Ensure PHP agent is available
                newrelic_notice_error($e->getMessage(), $e);
            }

            return 'failure';
        }
    }

    public function download( $job_id ) {
        try {
            $ch = curl_init();

            curl_setopt_array($ch, array (
                CURLOPT_URL            => 'https://api.neverbounce.com/v4/jobs/status?key=' . $this->api_key . '&job_id=' . $job_id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => "",
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => "GET"
            ));

            $_response = curl_exec($ch);
            curl_close($ch);
            return $_response;
            
        } catch ( Exception $e ) {
            if ( extension_loaded('newrelic') ) { // Ensure PHP agent is available
                newrelic_notice_error($e->getMessage(), $e);
            }

            return 'failure';
        }
    }

    public function delete_job( $job_id ) {
        try {
            $param_data = '{
            "key": "' . $this->api_key . '",
            "job_id": ' . $job_id . '          
                
        }';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.neverbounce.com/v4/jobs/delete');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array (
                "Content-Type: application/json"
            ));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);
            
        } catch ( Exception $e ) {
            if ( extension_loaded('newrelic') ) { // Ensure PHP agent is available
                newrelic_notice_error($e->getMessage(), $e);
            }

            return 'failure';
        }
    }

    /**
     * Determine if we can send to the email or not based on Neverbounce response.
     * @param  array $data The response from Neverbounce
     *
     * @return bool
     */
    private function validate( $data ) {
        $pass = false;

        // First we want to check if the email is deliverable
        if ( $this->is_deliverable($data) ) {
            $pass = true;
        }

        // Then check if we are blocking one.
        if ( $this->is_blocked($data) ) {
            $pass = false;
        }

        return $pass;
    }

    /**
     * Is the result within our array of success statuses?
     * @return bool
     */
    private function is_deliverable( $data ) {
        if ( in_array($data['result'], $this->success_statuses) ) {
            return true;
        }

        return false;
    }

    /**
     * Are one of the blocked booleans set to true in the response?
     * @return bool
     */
    private function is_blocked( $data ) {
        // Check if any booleans are present defined in $this->block array
        foreach ( $this->block as $block ) {
            if ( in_array($block, $data['flags']) ) {
                return true;
            }
        }

        return false;
    }

}
