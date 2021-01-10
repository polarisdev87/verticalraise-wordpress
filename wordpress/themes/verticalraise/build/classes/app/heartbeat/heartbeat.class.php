<?php

namespace classes\app\heartbeat;

class Heartbeat
{
    private $endpoint;
    
    public function __construct() {
        $this->endpoint = $this->get_endpoint();
    }

    public function run() {
        $this->process();
    }
    
    private function process() {
        wp_remote_get($this->endpoint);
    }
    
    private function get_endpoint() {
        if ( _SERVER_TYPE == 'prod' ) {
            return _HEARTBEAT_ENDPOINT;
        }
        
        return _HEARTBEAT_ENDPOINT_DEV;
    }

}