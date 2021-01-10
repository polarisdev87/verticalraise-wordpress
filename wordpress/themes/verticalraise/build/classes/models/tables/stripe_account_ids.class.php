<?php

namespace classes\models\tables;

class Stripe_Account_Ids
{
    
    private $table_name = 'stripe_connect_ids';

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb; 
    }
    
    public function insert($f_id, $account_id) {
        $this->wpdb->insert( 
            $this->table_name, 
            array( 
                'f_id' => $f_id, 
                'stripe_account_id' => $account_id, 
            ) 
        );
        
        $insert_id = $this->wpdb->insert_id;
            
        return $insert_id;        
    }
    
    public function update($id, $f_id, $account_id) {
        
    }
    
    public function delete(){
        
    }
    
    public function get_account_id($f_id) {      
        //$result = $this->wpdb->get_results("SELECT * FROM {$this->table_name} WHERE f_id = '{$f_id}'", OBJECT);
                
        $result = $this->wpdb->get_row("SELECT * FROM `{$this->table_name}` WHERE `f_id` = '{$f_id}' LIMIT 1", OBJECT);
        if ( !empty($result) ) {
            return $result;
        }
        return 0;
    }
    
   
}
