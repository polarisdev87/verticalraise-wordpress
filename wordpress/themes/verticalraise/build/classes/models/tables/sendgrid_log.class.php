<?php

namespace classes\models\tables;

class SendGrid_Log
{
    
    private $table_name = 'sendgrid_log';

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb; 
    }
    
    public function insertCode($email, $code) {
        $this->wpdb->insert(
            $this->table_name, 
            array( 
                'email' => $email, 
                'code' => $code, 
            ) 
        );
        
        $insert_id = $this->wpdb->insert_id;
            
        return $insert_id;
    }

    // Delete sendgrid logs older than 2 months.
    public function deleteLogs() {
        $sql = "DELETE FROM `{$this->table_name}` WHERE created < DATE_SUB(NOW(),INTERVAL 2 MONTH)";
        $result = $this->wpdb->query( $sql );
        return $result;
    }

}
