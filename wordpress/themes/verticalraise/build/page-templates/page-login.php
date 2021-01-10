<?php 

/**
 *
 * Template Name: Login Template 
 *
 * @Descrition The page template used for the login page. Users will login here account as well as activate their account.
 *
 */


/**
 * Load Class.
 */
load_class('page.login.class.php');

/**
 * Instantiate Class.
 */
$login = new Login();
/**
 * Check if the user is trying to activate their account.
 */
$error_code = $login->activate();
if ($error_code) {
    echo  $login->output_errors($error_code);
    exit();
}
/**
 * Check if the user is trying to login and process the login.
 */
$result = $login->login();
if ( $result['error'] ) {
    $response['success'] = false;
    $response['data'] = $login->output_errors($result['param']);   
} else {
    $response['success'] = true;
    $response['data'] = $result['param'];   
}
die (json_encode( $response ));



