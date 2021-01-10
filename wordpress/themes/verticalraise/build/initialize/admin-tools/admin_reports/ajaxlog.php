<?php

use \classes\app\admin_reports\Cron_Reports;
use \classes\app\admin_reports\Cron_Fundraisers_DB;

add_action('wp_ajax_get_jsondata', 'my_ajax_action_function');

function my_ajax_action_function() {

    $response = array ();
    $response['status'] = true;
    header("Content-Type: application/json");
    //Get Json output and DB content both here.

    if ( empty($_POST['filename']) ) {
        $response['status'] = false;
        echo json_encode($data);
        exit();
    }

    //Get json file content.
    $jsonFilename = $_POST['filename'];
    $json         = new Cron_Reports();
    $jsondata     = $json->getjson($_POST);
    $response['json_data'] = $jsondata;
    
    //Get cron data from db.

    $cronData       = new Cron_Fundraisers_DB();
    $cronDataFromDB = $cronData->get_cron_data($_POST);
    $response['cron_data_db'] = $cronDataFromDB;

//    var_dump($response);exit;
    echo json_encode($response);
    exit();
}
