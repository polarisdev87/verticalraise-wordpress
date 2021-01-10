<?php

/* Template Name: Donation Timber */

use classes\app\fundraiser\Fundraiser;
use classes\app\participant\Participant;

require_once ( get_template_directory() . '/stripe-php/config.php' );

$fundraiser_id   = (int) $_GET['fundraiser_id'];
$uid             = (isset( $_GET['uid'] )) ? $_GET['uid'] : 0;
$media           = isset( $_GET['media'] )? $_GET['media'] : null;
$donation_amount = isset( $_GET['donation_amount'] )? $_GET['donation_amount'] : null;
$anonymous       = isset( $_GET['anonymous'] )? $_GET['anonymous'] : null ;
$smail           = isset( $_GET['smail'] )? $_GET['smail'] : null ;

$context = Timber::context();
$context['post'] = new Timber\Post();

$context['constants'] = array (
    'template_directory' => get_bloginfo( 'template_directory' ),
    'site_url'           => get_bloginfo( 'url' ),
    'facebook_app_id'    => _FACEBOOK_APP_ID,
    'stripe_publishable_key'=> _STRIPE_DEV_PUBLISHABLE_KEY,
    'the_checker_api_key'    => _THE_CHECKER_API_KEY,
    'site_name'          =>  get_bloginfo( 'name' ),
    'multiple'           => 100, // MULTIPLE
    'currency'           => 'USD',
    'is_mobile_new'      => is_mobile_new(),
);

$context['request'] = array(
    'donation_amount' => $donation_amount,
    'fundraiser_id'   => $fundraiser_id,
    'anonymous'       => $anonymous,
    'media'           => $media,
    'smail'           => $smail,
    'uid'             => $uid
);

$fundraiser =  Fundraiser::getFundraiser( $fundraiser_id );
$context['fundraiser'] = $fundraiser;
$context['participant'] = Participant::getParticipant($uid, $fundraiser);

Timber::render( 'templates\donation\main.twig', $context );