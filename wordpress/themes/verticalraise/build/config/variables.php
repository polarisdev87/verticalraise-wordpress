<?php

/* --------------------
  |
  | Extra variable define
  |
  /*-------------------- */

// United States Array

$states_array = array (
    'AL' => 'Alabama',
    'AK' => 'Alaska',
    'AZ' => 'Arizona',
    'AR' => 'Arkansas',
    'CA' => 'California',
    'CO' => 'Colorado',
    'CT' => 'Connecticut',
    'DE' => 'Delaware',
    'DC' => 'District Of Columbia',
    'FL' => 'Florida',
    'GA' => 'Georgia',
    'HI' => 'Hawaii',
    'ID' => 'Idaho',
    'IL' => 'Illinois',
    'IN' => 'Indiana',
    'IA' => 'Iowa',
    'KS' => 'Kansas',
    'KY' => 'Kentucky',
    'LA' => 'Louisiana',
    'ME' => 'Maine',
    'MD' => 'Maryland',
    'MA' => 'Massachusetts',
    'MI' => 'Michigan',
    'MN' => 'Minnesota',
    'MS' => 'Mississippi',
    'MO' => 'Missouri',
    'MT' => 'Montana',
    'NE' => 'Nebraska',
    'NV' => 'Nevada',
    'NH' => 'New Hampshire',
    'NJ' => 'New Jersey',
    'NM' => 'New Mexico',
    'NY' => 'New York',
    'NC' => 'North Carolina',
    'ND' => 'North Dakota',
    'OH' => 'Ohio',
    'OK' => 'Oklahoma',
    'OR' => 'Oregon',
    'PA' => 'Pennsylvania',
    'RI' => 'Rhode Island',
    'SC' => 'South Carolina',
    'SD' => 'South Dakota',
    'TN' => 'Tennessee',
    'TX' => 'Texas',
    'UT' => 'Utah',
    'VT' => 'Vermont',
    'VA' => 'Virginia',
    'WA' => 'Washington',
    'WV' => 'West Virginia',
    'WI' => 'Wisconsin',
    'WY' => 'Wyoming',
);

define('_US_STATES', $states_array);


//Response Reason string of api.thechecker.co 

$invalid_string = array (
    'invalid_email'      => 'Specified email is not a valid email address syntax.',
    'invalid_domain'     => 'Domain for email does not exist or does not have a SMTP server.',
    'rejected_email'     => 'Email address was rejected by the SMTP server, email address does not exist.',
    'accepted_email'     => 'Email address was accepted by the SMTP server',
    'low_quality'        => 'Email address has quality issues that may make it a risky or low-value address.',
    'low_deliverability' => 'Email address appears to be deliverable, but deliverability cannot be guaranteed.',
    'no_connect'         => 'Could not connect to SMTP server.',
    'timeout'            => 'SMTP session timed out.',
    'unavailable_smtp'   => 'SMTP server was unavailable to process our request.'
);
define('_CHECKER_INVALID', $invalid_string);