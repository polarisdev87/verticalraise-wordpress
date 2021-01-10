<?php

namespace classes\vendors;

/**
 * This class connects to the TheChecker.co API to validate emails on the fly
 * Documentation: https://thechecker.co/api
 *
 * Responses:
 *     1. result       string        - The verification result: deliverable, undeliverable, risky, unknown
 *     2. email        string        - Normalized version of the provided email address.
 *     3. user         string        - The user (a.k.a local part) of the provided email address.
 *     4. domain       string        - The domain of the provided email address.
 *     5. role         boolean       - true if the email address is a role address.
 *     6. disposable   boolean       - true if the email address uses a disposable domain.
 *     7. accept_all   boolean       - true if the email was accepted, but the domain appears to accept all emails addressed to that domain.
 *     8. did_you_mean null | string - Returns a suggested email if a possible spelling error was detected.
 *
 */
class TheChecker
{

    /**
     * Class variables.
     */
    private $api_key          = _THE_CHECKER_API_KEY;                // API Key
    private $success_statuses = ['deliverable', 'unknown', 'risky']; // Success statuses
    private $failure_statuses = ['undeliverable'];                   // Failure statuses
    private $block            = ['role', 'disposable'];              // Responses to block from sending

    /**
     * Public constructor.
     */

    public function __construct() {
        
    }

    /**
     * Verify a single email through TheChecker.co verification service.
     * @param  string $email The email
     * @return bool
     */
    public function verify_single( $email ) {
        try {
            $results = json_decode($this->request($email), true);
            if ( in_array($results['result'], $this->success_statuses) ) {
                return true;
            }

            return false;
        } catch ( Exception $e ) {
            if ( extension_loaded('newrelic') ) { // Ensure PHP agent is available
                newrelic_notice_error($e->getMessage(), $e);
            }
        }

        return true;
    }

    /**
     * Verify multiple emails at once through TheChecker.co verification service.
     * @param  array $emails  The array of emails
     * @return array $results The invalid and valid emails
     */
    public function verify_multiple( $emails ) {

        // Empty arrays to hold our results
        $results['valid']   = array ();
        $results['invalid'] = array ();

        try {

            if ( empty($emails) ) {
                return $results;
            }

            ### TODO ###
            // time out, what to do
            // rate limit what to do
            // logger
            
            /** ------------ MULTI CURL REQUEST -------------- **/

            $mh = curl_multi_init();

            // Build curl requests
            $i = 0;
            foreach ( $emails as $email ) {
                $ch_{$i} = curl_init('https://api.thechecker.co/v1/verify?email=' . $email['email'] . '&api_key=' . $this->api_key);
                curl_setopt($ch_{$i}, CURLOPT_RETURNTRANSFER, true);
                curl_multi_add_handle($mh, $ch_{$i});
                $i++;
            }

            // Execute all queries simultaneously, and continue when all are complete
            $running = null;
            do {
                curl_multi_exec($mh, $running);
                curl_multi_select($mh);
            } while ( $running > 0 );

            // Close the handles
            $i = 0;
            foreach ( $emails as $email ) {
                curl_multi_remove_handle($mh, $ch_{$i});
                $i++;
            }

            curl_multi_close($mh);
            
            /** ------------/MULTI CURL REQUEST -------------- **/

            // Process results
            $i = 0;
            foreach ( $emails as $key => $email ) {
                $_result = json_decode(curl_multi_getcontent($ch_{$i}), true);
                
                // Prep the results to return
                if ( $this->validate($_result) ) {
                    $results['valid'][$email['id']] = array (
                        'email'      => $email['email'],
                        'result'     => $_result['result'],
                        'reason'     => $_result['reason'],
                        'role'       => $_result['role'],
                        'disposable' => $_result['disposable'],
                        'accept_all' => $_result['accept_all']
                    );
                } else {
                    $results['invalid'][$email['id']] = array (
                        'email'      => $email['email'],
                        'result'     => $_result['result'],
                        'reason'     => $_result['reason'],
                        'role'       => $_result['role'],
                        'disposable' => $_result['disposable'],
                        'accept_all' => $_result['accept_all']
                    );
                }
                $i++;
            }
        } catch ( Exception $e ) {
            if ( extension_loaded('newrelic') ) { // Ensure PHP agent is available
                newrelic_notice_error($e->getMessage(), $e);
            }

            // Return the emails back if there is an error
            $results['valid'] = $emails;
        }

        return $results;
    }
    
    /**
     * Determine if we can send to the email or not based on TheChecker.co response.
     * @param  array $data The response from TheChecker.co
     *
     * @return bool
     */
    private function validate($data) {
        $pass = false;
        
        // First we want to check if the email is deliverable
        if ( $this->is_deliverable($data) ) {
            $pass = true;
        }
        
        // Then check if we are blocking one of the booleans: `disposable`, `role`, etc.
        if ( $this->is_blocked($data) ) {
            $pass = false;
        }
        
        return $pass;
    }
    
    /**
     * Is the result within our array of success statuses?
     * @return bool
     */
    private function is_deliverable($data) {
        if ( in_array($data['result'], $this->success_statuses) ) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Are one of the blocked booleans set to true in the response?
     * @return bool
     */
    private function is_blocked($data) {
        // Check if any booleans are present defined in $this->block array
        foreach( $this->block as $block ) {
            if ( $data[$block] ) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Make a request to TheChecker.co API using the GET method.
     * @param  string $email   The email to check
     * @return json   $results The API response
     */
    private function request( $email ) {
        $results = file_get_contents('https://api.thechecker.co/v1/verify?email=' . $email . '&api_key=' . $this->api_key);
        return $results;
    }

}
