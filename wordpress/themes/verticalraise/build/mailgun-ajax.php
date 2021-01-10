<?php
/* Template Name: Email API Validation */
/*$validateAddress = trim($_POST['email_id']);
$key = "098a171bbb60684c26f3ff1df1ac7b94d34f5d5a371cf45893d290716b8c17e3";
$url = "https://api.kickbox.io/v2/verify?email=$validateAddress&apikey=$key";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
$curl_return = curl_exec($ch);
curl_close($ch);
$curl_return_array = json_decode($curl_return, true);
$isValid = $curl_return_array['result'];
$isValidReason = $curl_return_array['reason'];
if($isValid != 'undeliverable') {
    if($isValidReason != 'invalid_email' && $isValidReason != 'invalid_domain' && $isValidReason != 'rejected_email') {
        echo 1;
    } else {
        echo 0;
    }
} else {
    echo 0;
}*/

//require_once("../../../wp-load.php");
require TEMPLATEPATH .'/mailgun-php-master/vendor/autoload.php';
use Mailgun\Mailgun;
$mgClient = new Mailgun('pubkey-54cbc3e6cb4208d64ece65bfdadaa3ba');

$validateAddress = trim($_POST['email_id']);
$result = $mgClient->get("address/validate", array('address' => $validateAddress));
$isValid = $result->http_response_body->is_valid;

if($isValid == 1) {
    echo 1;
} else {
    echo 0;
}
?>