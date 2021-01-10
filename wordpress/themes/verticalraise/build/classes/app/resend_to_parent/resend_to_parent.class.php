<?php

class Resend_To_parent
{
    private $fid = null;
    private $uid = null;
    private $key = null;
    
    public function init() {
        $this->run();
    }
    
    private function run() {
        // Get request
        $request = $this->request();
        if ( empty($request) ) {
            return;
        }
        
        // Parse the request
        $parse = $this->parse_request($this->request);
        if ( empty($parse) ) {
            return;
        }
        
        // Set the url
        $url = $this->url($parse);
        if ( empty($url) ) {
            return;
        }
        
        // Redirect user
        $this->redirect($url);
    }
    
    /**
     * Return the request
     * @return string|false
     */
    private function request() {
        if (isset($_GET['key'])) {
            return $_GET['key'];
        }
        
        return false;
    }
    
    private function parse_request($request) {
        // Parse it
        return false;
        
        
        
        // Set private members
        // Return true
        return true;
    }
    
    /**
     * Set the url
     * @return string
     */
    private function url() {
        return $this->is_mobile() ? $this->mobile_url() : $this->desktop_url();
    }
    
    /**
     * Check if a user is mobile or desktop
     * @return bool
     */
    private function is_mobile() {
        return wp_is_mobile();
    }
    
    /**
     * Redirect the user to a given url
     * @param string $url
     */
    private function redirect($url = null) {
        header("Location: {$url}");
        die();
    }
    
    private function mobile_url() {
        
    }
    
    private function desktop_url() {
        
    }
    

}