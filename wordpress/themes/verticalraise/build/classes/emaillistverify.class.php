<?php


class EmailListVerify
{
    
    public function __construct() {
    
        $this->api_key = _EMAIL_LIST_VERIFY_API_KEY;
    }
    
    public function verify($email) {
        
        try{
            $response = $this->request($email);
            if ( $response == 'ok' ) {
                return true;
            } else if ( $response == 'unknown' ) {
                return true;
            } else if ( $response == 'key_not_valid' ) {
                return true;
            } else {
                return false;
            }
        }
        
        catch(Exception $e) {
            return true;
        }
        
    }
    
    public function request($email) {
        $url = "https://apps.emaillistverify.com/api/verifyEmail?secret=" . $this->api_key . "&email=" . $email;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }
    
    public function bulk_request($data) {
        $settings['file_contents'] ="@/home/Downloads/emails.txt"; //path to your file
        $url = 'https://apps.emaillistverify.com/api/verifyApiFile?secret=' . $this->api_key . '&filename=my_emails.txt';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $settings);
        $file_id = curl_exec($ch); //you need to save this FILE ID for get file status and download reports in future
        curl_close($ch);
        
        return $file_id;
    
    }
    
    public function bulk_return($file_id) {
        $url = 'https://apps.emaillistverify.com/api/getApiFileInfo?secret=' . $this->api_key . '&id=10700';
        $string = file_get_contents($url);
        list($file_id, $filename, $unique, $lines, $lines_processed, $status, $timestamp, $link1, $link2) = explode('|', $string); //parse data
    }
    
}