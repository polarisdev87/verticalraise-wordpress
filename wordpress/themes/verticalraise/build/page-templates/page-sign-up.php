<?php

/* Template Name: Sign Up Template */

use classes\app\user\Signup;

if ( is_user_logged_in() ) {

    $redirectUrl      = get_bloginfo('url') . "/my-account";
    $result['status'] = true;
    $result['data']   = $redirectUrl;
    die(json_encode($result));
    exit();
}

if ( isset($_POST['register']) ) {
    $signup_user = new Signup();
    $result      = $signup_user->proccess_signup($_POST);

    die(json_encode($result));
    exit();
}