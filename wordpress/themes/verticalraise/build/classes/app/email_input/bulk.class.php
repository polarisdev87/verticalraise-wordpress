<?php

namespace classes\app\email_input;

use \classes\models\tables\Email_Input;
use \classes\app\initial_send\Build_Email as Build_Email;

class Bulk
{
    private $email_input;
    private $info;
    
    public function __construct() {
        $this->email_input = new Email_Input();
        $this->info        = new Build_Email();
    }
    
    public function add($emails, $user_id, $fundraiser_id, $type) {
        $messages = '';
        foreach ( $emails as $email ) {
            try {
                $results['message'] = $this->email_input->insert($email, $user_id, $fundraiser_id, $type, $this->info->set_from_name($user_id));
                // messages
            }
            catch(Exception $e) {
                $messages['errors'][$email] = $e->getMessage();
            }
        }
        
        return $results;
    }

}