<?php /* Template Name: Facebook Access Token */ 
$post_id = $_GET['fundraiser_id'];
global $user_ID;
$return_url = get_the_permalink(548).'?fundraiser_id='.$post_id.'&token=1';
session_start();
require_once TEMPLATEPATH.'/facebooklogin/autoload.php';
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookSDKException;

$helper = new FacebookRedirectLoginHelper($return_url, '202438063458458', '999b684c411ba8212f5f7b0747ba37e1');

try {
    $session = $helper->getSessionFromRedirect();
} catch(FacebookSDKException $e) {
    $session = null;
}

if ($session) {
  // User logged in, get the AccessToken entity.
  $accessToken = $session->getAccessToken();
  // Exchange the short-lived token for a long-lived token.
  $longLivedAccessToken = $accessToken->extend();
  $facebook_token = json_decode(get_post_meta($post_id, 'facebook_token', true), true);
    if(empty($facebook_token)){
        $facebook_token = array();
        $facebook_token['user_array'] = array();
        $user_array = array();
        $user_array['uid'] = $user_ID;
        $user_array['access_token'] = $longLivedAccessToken;
        array_push($facebook_token['user_array'], $user_array);
        update_post_meta($post_id, 'facebook_token', json_encode($facebook_token));
    } else {
        $flag = 0;
        foreach($facebook_token['user_array'] as $user_array) {
            if($user_array['uid'] == $user_ID) {
                $user_array['access_token'] = $longLivedAccessToken;
                $flag = 1;
            }
        }
        if($flag == 0) {                        
            $user_array = array();
            $user_array['uid'] = $user_ID;
            $user_array['access_token'] = $longLivedAccessToken;
            array_push($facebook_token['user_array'], $user_array);
        }
        update_post_meta($post_id, 'facebook_token', json_encode($facebook_token));
    }
  // Now store the long-lived token in the database
  // . . . $db->store($longLivedAccessToken);
  // Make calls to Graph with the long-lived token.
  // . . . 
} else {
  echo '<a href="' . $helper->getLoginUrl() . '">Login with Facebook</a>';
  //header("Location: ".$helper->getLoginUrl());
}
?>