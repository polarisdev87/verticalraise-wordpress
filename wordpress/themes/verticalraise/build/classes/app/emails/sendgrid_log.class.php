<?php

namespace classes\app\emails;

use classes\models\tables\SendGrid_Log as Log;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class SendGrid_Log

{
    
    private $sendgrid_log;
    
    public function __construct() {
        $this->sendgrid_log = new Log;
    }
    
    public function log($email, $code) {
        return $this->sendgrid_log->insertCode($email, $code);
    }

    public function delete_logs() {
        return $this->sendgrid_log->deleteLogs();
    }
}
