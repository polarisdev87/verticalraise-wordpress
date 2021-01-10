<?php

/* Template Name: NeverBounce Email Validation Ajax */

use \classes\vendors\NeverBounce;
//use \classes\vendors\NeverBounce_Bulk;

$vendor = new NeverBounce();
//$vendor = new NeverBounce_Bulk();

if ( $_POST['post_type'] == 'create_job' ) {
    $_emails = explode(",", $_POST['emails']);

    $emails = array ();


    $i = 0;
    foreach ( $_emails as $_email ) {
        $emails[$i]['email'] = $_email;
        $emails[$i]['id']    = $i;
        $i++;
    }
    $result = $vendor->create_job($emails);
    die(json_encode($result));
    exit;
}

if ( $_POST['post_type'] == 'get_status' ) {
//    $start = $vendor->job_start($_POST['job_id']);
    $result = array();
    $result = $vendor->get_status($_POST['job_id']);
    if (!isset($result['job_status']) ) {
         $result['job_status'] = 'failed';
    } 
    die (json_encode($result)); exit;
}

if ( $_POST['post_type'] == 'get_result' ) {
    $result = $vendor->get_result($_POST['job_id']);
    die(json_encode($result));
    exit;
}

if ( $_POST['post_type'] == 'bulk' ) {
    $_emails = explode(",", $_POST['emails']);

    $emails = array ();
    $i      = 0;
    foreach ( $_emails as $_email ) {
        $emails[$i]['email'] = $_email;
        $emails[$i]['id']    = $i;
        $i++;
    }
    $result = $vendor->bulk($emails);
    die(json_encode($result));
    exit;
}

//single verify
if ( $_POST['post_type'] == 'single_verify' ) {
    $result = $vendor->single_verify($_POST['email']);
    if ($result == 'failure') {
        $data['status'] = 'failed';
    } else {
        $data['status'] = 'success';
        $data['validation'] = $result;
    }
    die(json_encode($data));
    exit;
}


