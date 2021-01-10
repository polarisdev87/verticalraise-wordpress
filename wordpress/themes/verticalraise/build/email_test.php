<?php

/*

// Error output
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define the verifier
//$v = 'thechecker';
$v = 'neverbounce';

// Server Type
define('_SERVER_TYPE', 'prod');

// Script execution start time
$start = microtime(true);
echo "<br>Start: {$start}<br>";

include("classes/vendors/NeverBounce.class.php");
include("classes/vendors/thechecker.class.php");

use \classes\vendors\NeverBounce;
use \classes\vendors\TheChecker;

if ( $v == 'thechecker' ) {
    define( '_THE_CHECKER_API_KEY', 'd3cb3ea13b6817ef4f65da12f726eee69b8702c498f4c3b2a70ddedaf9630bad' );

    $vendor = new TheChecker();
}



if ( $v == 'neverbounce' ) {
    define( '_NEVERBOUNCE_API_DEV_KEY', 'secret_bfe0c26b9eb815e0f76f1b7491d68c8b');
    define( '_NEVERBOUNCE_API_LIVE_KEY', 'secret_bfe0c26b9eb815e0f76f1b7491d68c8b');
    
    $vendor = new NeverBounce();
}



try{

    $_emails = [ "ipm2k4@glockapps.com","allanb@glockapps.awsapps.com","ingridmejiasri@aol.com","caseywrighde@aol.de","baileehinesfr@aol.fr","brendarodgersuk@aol.co.uk","franprohaska@aol.com","garrettjacqueline@aol.com","julianachristensen@aol.com","julia_g_76@icloud.com","gappsglock@icloud.com","zacheryfoleyrx@azet.sk","bcc@spamcombat.com","chazb@userflowhq.com","glock.julia@bol.com.br","drteskissl@eclipso.de","s.exploitation@free.fr","s.client@free.fr","s.webmastering@free.fr","carloscohenm@freenet.de","amanishepherdsx@gmail.com","llionelcohenbr@gmail.com","bbarretthenryhe@gmail.com","lindseylancasterrd@gmail.com","cierawilliamsonwq@gmail.com","silviacopelandqy@gmail.com","romeowhitfieldbx@gmail.com","kolepadillafx@gmail.com","baileybryantk@gmail.com","athenawebsterwd@gmail.com","demarcopearsonu@gmail.com","magdalenaatkinsrq@gmail.com","verifycom79@gmx.com","verifyde79@gmx.de","gd@desktopemail.com","verify79@buyemailsoftware.com","frankiebeckerp@hotmail.com","sgorska12@interia.pl","layneguerreropm@laposte.net","britnigrahamap@laposte.net","amandoteo79@libero.it","glocktest@vendasrd.com.br","b2bdeliver79@mail.com","verifymailru79@mail.ru","glockapps@mc.glockapps.com","verify79ssl@netcourrier.com","nsallan@expertarticles.com","evalotta.wojtasik@o2.pl","exosf@glockeasymail.com","krysiawal1@onet.pl","brendonosbornx@outlook.com","tristonreevestge@outlook.com.br","brittanyrocha@outlook.de","glencabrera@outlook.fr","christopherfranklinhk@outlook.com","kaceybentleyerp@outlook.com","meaghanwittevx@outlook.com","aileenjamesua@outlook.com","shannongreerf@outlook.com","katlynnbridgesv@outlook.com","verify79@seznam.cz","glock1@sfr.fr","glock3@sfr.fr","sa79@justlan.com","amandoteo79@virgilio.it","verify79@web.de","sebastianalvarezv@yahoo.com.br","verifyca79@yahoo.ca","justynbenton@yahoo.com","testiotestiko@yahoo.co.uk","emailtester493@yahoo.com","loganbridgesrk@yahoo.com","rogertoddw@yahoo.com","darianhuffg@yahoo.com","verifyndx79@yandex.ru","verifynewssl@zoho.com","lamb@glockdb.com"
    ];
    
    echo "<pre>";
    $emails = array();
    echo "</pre>";
    
    $i = 0;
    foreach($_emails as $_email) {
        $emails[$i]['email'] = $_email;
        $emails[$i]['id'] = $i;
        $i++;
    }
    
    print_r($emails);

    $response = $vendor->verify_multiple($emails);

    $finish = microtime(true);
    echo "<br>Finish: {$finish}<br>";
    $duration = $finish-$start;

    echo "Duration: " . $duration;
    echo "<br><br>";
    echo "<pre>";
    //print_r($response);
    echo "</pre>";
} catch ( Exception $e ) {
    
    echo "error " . $e->getMessage();

}

*/