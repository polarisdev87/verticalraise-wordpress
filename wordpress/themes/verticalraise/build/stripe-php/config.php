<?php
require_once __DIR__ . '/vendor/autoload.php';

if ( _SERVER_TYPE == 'dev' ) {
    $stripe_secret_key = _STRIPE_DEV_SECRET_KEY;
    $stripe_publishable_key = _STRIPE_DEV_PUBLISHABLE_KEY;
} else {
    $stripe_secret_key = _STRIPE_SECRET_KEY;
    $stripe_publishable_key = _STRIPE_PUBLISHABLE_KEY;
}

$stripe = array(
	"secret_key"      => $stripe_secret_key,
	"publishable_key" => $stripe_publishable_key
);

\Stripe\Stripe::setApiKey($stripe['secret_key']);
?>
