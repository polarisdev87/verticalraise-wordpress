<?php

namespace classes\app\stripe;

use classes\app\stripe\Stripe;
use classes\models\tables\Stripe_Account_Ids;

Class Stripe_Form
{

    public $table;
    public $stripe_connect;

    public function __construct() {
        $this->table = new Stripe_Account_Ids();
        $this->stripe_connect = new Stripe();
    }

    //proccess form    
    public function process_form() {
        
    }

    // Create account  
    public function create_connect_account($post_data) {        
        $result = $this->stripe_connect->create($post_data);
        
        if (isset($result->id) && !empty($result->id)) {
            return $result->id;
        } else {
            return false;
        }
    }

    // Create own account for check by mail fundraisers
    public function create_own_account($post_data) {
        $result = $this->stripe_connect->createOwnAccount($post_data);

        if (isset($result->id) && !empty($result->id)) {
            return $result->id;
        } else {
            return false;
        }
    }

    // Save token  //Store account id  
    public function insert_account_id($f_id, $account_id) {
        //check existing of account id in stripe_connect_ids table
        $check = $this->get_account_id($f_id);
        if (!$check) {
            $result = $this->table->insert($f_id, $account_id);
        } else {
            $result = $this->table->update($check->id, $f_id, $account_id);
        }
        
    }

    public function update_account_id($id, $params, $post_data) {
        
        $stripe_update = $this->stripe_connect->update($params, $post_data);
        if (isset($stripe_update->id) && !empty($stripe_update->id)) {
            return $stripe_update->id;
        } else {
            return false;
        }
    }

    public function delete_account_id($id) {
        
    }

    public function get_account_id($f_id) {
        $result = $this->table->get_account_id($f_id);
        if ($result) {
            return $result;
        }
        return false;
    }
    
    public function retrieve_account($f_id) {        
        $result = $this->get_account_id($f_id);       
        if (!empty($result)) {
            $account_id = $result->stripe_account_id;
            $retrieve   = $this->stripe_connect->get($account_id);
           
            return $retrieve->external_accounts;
//            var_dump($retrieve);
        } else {
            return 0;
        }
    }
}
