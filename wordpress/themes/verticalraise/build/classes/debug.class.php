<?php

//add_action('shutdown', 'sql_logger');
/*function sql_logger() {
    global $wpdb;
    $log_file = fopen(ABSPATH.'/sql_log.txt', 'a');
    fwrite($log_file, "//////////////////////////////////////////\n\n" . date("F j, Y, g:i:s a")."\n");
    foreach($wpdb->queries as $q) {
        fwrite($log_file, $q[0] . " - ($q[1] s)" . "\n\n");
    }
    fclose($log_file);
}*/

/*if(is_admin() == true ){

echo "current_time( 'mysql' ) returns local site time: " . current_time( 'mysql' ) . '<br />';
echo "current_time( 'mysql', 1 ) returns GMT: " . current_time( 'mysql', 1 ) . '<br />';
echo "current_time( 'timestamp' ) returns local site time: " . date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) . "<br>";
echo "current_time( 'timestamp', 1 ) returns GMT: " . date( 'Y-m-d H:i:s', current_time( 'timestamp', 1 ) ) . "<br>";
    exit();
}*/