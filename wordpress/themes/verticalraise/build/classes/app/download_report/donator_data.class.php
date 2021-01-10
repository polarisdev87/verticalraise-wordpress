<?php

namespace classes\app\download_report;

use \classes\models\tables\Donations;

class Donator_Data
{
    
    private $payments;

    /**
     * Class Constructor.
     */
    public function __construct() {
        $this->payments = new Donations();
    }
    
    public function init($fundraiser_id) {
        $payments = $this->payments->get_all_payments_by_fundraiser_id($fundraiser_id);
        
        if ( empty($payments) ) {
            return $this->return_empty();
        }
        
        $data = array();
        
        foreach ( $payments as $payment ) {
            $data[] = $this->build_row($payment);
        }
        
        return $data;
    }

    private function build_row($payment) {
        $data = new \stdClass();
        
        $data->title  = $payment['name'];
        $data->email  = $payment['email'];
        $data->amount = intval($payment['amount']);
        $data->date   = date('m/d/Y', strtotime($payment['time']));

        if ( !empty($payment['uid']) ) { 
            $data->recipient_data = get_userdata($payment['uid']); 
            $data->recipient      = $data->recipient_data->first_name . ' ' . $data->recipient_data->last_name;
        } else {
            $data->recipient      = '';
        }
        
        return $this->construct_row($data);
    }
    
    private function construct_row($data) {
        return [
            $data->title, 
            $data->email, 
            $data->amount, 
            $data->recipient, 
            $data->date
        ];
    }
    
    private function return_empty() {
        return array(array('No donations yet', '', '', '', ''));
    }


}