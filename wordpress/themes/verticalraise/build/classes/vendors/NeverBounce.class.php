<?php

namespace classes\vendors;

/**
 * This class connects to the Neverbounce API to validate emails on the fly
 * Documentation: https://api.neverbounce.com
 *
 * Response:
 * 1. status : success, failed, etc.
 * 2. result
 *      valid       - Verified as real address
 *      Invalid     - Verified as not valid
 *      Disposable  - A temporary, disposable address
 *      Catchall    - A domain-wide setting
 *      Unknown	    - The server cannot be reached
 * 3. flags
 *
 *      has_dns            - The input has one or more DNS records associated with the hostname.
 *		has_dns_mx         - The input has mail exchanger DNS records configured.
 *		bad_syntax         - The input given doesn't appear to be an email.
        free_email_host    - This email is registered on a free-mail host. (e.g: yahoo.com, hotmail.com)
        profanity          -
        role_account       - This email is a role-based email address (e.g: admin@, help@, sales@)
        disposable_email   - The input given is a disposable email.
        government_host    - The input given is a government email.
        acedemic_host      - The input given is a acedemic email.
        military_host      - The input given is a military email.
        international_host - INT designated domain names.
        squatter_host      - Host likely intended to look like a big-time provider (type of spam trap).
        spelling_mistake   - The input was misspelled
        bad_dns            -
        temporary_dns_error-
        connect_fails      - Unable to connect to remote host.
        accepts_all        - Remote host accepts mail at any address.
        contains_alias     - The email address supplied contains an address part and an alias part.
        contains_subdomain - The host in the address contained a subdomain.
        smtp_connectable   - We were able to connect to the remote mail server.
        spamtrap_network   - Host is affiliated with a known spam trap network.
 */
class NeverBounce
{

    /**
     * Class variables.
     */
    private $api_key;                                                  // API Key
    private $success_statuses = ['valid', 'unknown', 'catchall'];      // Success statuses
    private $failure_statuses = ['invalid', 'disaposable'];            // Failure statuses
    private $block            = [
        'role_account',
        'disposable_email',
        'bad_syntax',
        'squatter_host',
        'spelling_mistake',
        'bad_dns',
        'connect_fails',
        'spamtrap_network'
    ];                                                                // Responses to block from sending

    /**
     * Public constructor.
     */

    public function __construct() {
        $this->api_key = ( _SERVER_TYPE === 'dev' ) ? _NEVERBOUNCE_API_DEV_KEY : _NEVERBOUNCE_API_LIVE_KEY;
    }

    /**
     * Verify multiple emails at once through Neverbounce.com verification service.
     * @param  array $emails  The array of emails
     * @return array $results The invalid and valid emails
     */
    public function verify_multiple( $emails ) {
        // Empty arrays to hold our results
        $results['valid']   = array();
        $results['invalid'] = array();

        try {
            if ( empty($emails) ) {
                return $results;
            }
            ### TODO ###
            // time out, what to do
            // rate limit what to do
            // logger

            /** ------------ MULTI CURL REQUEST -------------- * */
            $mh = curl_multi_init();

            // Build curl requests
            $i = 0;
            foreach ( $emails as $email ) {
				//echo $email['email'];
				$request_url = 'https://api.neverbounce.com/v4/single/check?email=' . $email['email'] . '&key=' . $this->api_key;
				//echo $request_url;
				//echo "<br>";
				$ch_{$i} = curl_init( $request_url );
                curl_setopt( $ch_{$i}, CURLOPT_RETURNTRANSFER, true );
                curl_multi_add_handle( $mh, $ch_{$i} );
                $i++;
            }

            // Execute all queries simultaneously, and continue when all are complete.
            $running = null;
            do {
				$status = curl_multi_exec( $mh, $running );

				if ( $running ) {
					curl_multi_select( $mh );
				}

			} while ( $running > 0 && $status === CURLM_OK );

			// Check for errors.
			if ( $status != CURLM_OK ) {
				// Display error message
				//echo "ERROR!\n " . curl_multi_strerror($status);
				if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
					newrelic_notice_error( curl_multi_strerror( $status ) );
				}
			}

			// Get the response codes.
			$i = 0;
			foreach ( $emails as $email ) {
				$http_code = curl_getinfo( $ch_{$i}, CURLINFO_HTTP_CODE);
				if ( $http_code != 200 ) {
					//echo 'NeverBounce single verifier API response code is' . $http_code;
					if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
						newrelic_notice_error( 'NeverBounce single verifier API response code is' . $http_code );
					}
				}
				$i++;
			}

            // Close the handles
            $i = 0;
            foreach ( $emails as $email ) {
                curl_multi_remove_handle($mh, $ch_{$i});
                $i++;
            }

            curl_multi_close($mh);

            /** ------------/MULTI CURL REQUEST -------------- * */
            // Process results
			$i = 0;
			//echo "results";
            foreach ( $emails as $key => $email ) {
				$_result = json_decode( curl_multi_getcontent( $ch_{$i} ), true );

				//print_r($_result);

//                echo "<pre>";
//                echo $email['email'];
//                print_r($_result);
//                echo "</pre>";

                // Check for a real response
                if ( $this->has_result($_result) == false ) {
                    continue;
                }


                // Prep the results to return
                if ( $this->validate($_result) ) {
                    $results['valid'][$email['id']] = array (
                        'email'      => $email['email'],
                        'result'     => $_result['result'],
                        'reason'     => $_result['result'],
                        'role'       => (in_array("role_account", $_result['flags'])) ? 1 : 0,
                        'disposable' => (in_array("disposable_email", $_result['flags'])) ? 1 : 0,
                        'accept_all' => (in_array("accepts_all", $_result['flags'])) ? 1 : 0
                    );
                } else {
                    $results['invalid'][$email['id']] = array (
                        'email'      => $email['email'],
                        'result'     => $_result['result'],
                        'reason'     => $_result['result'],
                        'role'       => (in_array("role_account", $_result['flags'])) ? 1 : 0,
                        'disposable' => (in_array("disposable_email", $_result['flags'])) ? 1 : 0,
                        'accept_all' => (in_array("accepts_all", $_result['flags'])) ? 1 : 0
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
	 * Verify a single email record against NeverBounce.
	 * @param $email string The email adddress.
	 * @return results
	 */
    public function single_verify( $email ) {
        try {
            if ( empty($email) ) {
                return false;
            }

            $timeout = 10;
            $ch = curl_init();
            curl_setopt_array($ch, array (
                CURLOPT_URL            => 'https://api.neverbounce.com/v4/single/check?key=' . $this->api_key . '&email=' . $email . '&timeout='.$timeout,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => "",
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => "GET"
            ));

            $response = curl_exec($ch);
            curl_close($ch);

			$results = json_decode( $response, true );

			if ( empty( $results ) ) {
				if ( extension_loaded( 'newrelic' ) ) {
					newrelic_notice_error( 'NeverBounce Single Verify results missing') ;
				}
			}

			if ( empty( $results['status'] ) ) {
				if ( extension_loaded( 'newrelic' ) ) {
					newrelic_notice_error( 'NeverBounce Single Verify status missing: ' . $response);
				}
			}

            if ( $results['status'] != 'success' ) {
                return false;
            }

            if ( $this->validate( $results ) ) {
                return 'valid';
            } else {
                return 'invalid';
            }

        } catch ( Exception $e ) {
            if ( extension_loaded( 'newrelic' ) ) {
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

    /**
     * Do we have a result from NeverBounce?
     * @return bool
     */
    private function has_result( $data ) {
        if ( empty($data['result']) ) {
            return false;
        }

        return true;
    }

}
