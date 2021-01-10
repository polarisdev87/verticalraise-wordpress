<?php

/**
 * Utilize Google's API to shorten urls
 */
class Google_ShortURL
{

    private $target; 
    private $endpoint;
    
    public function __construct() {
        
        // Set Google Shortener API target
		$this->target = 'https://www.googleapis.com/urlshortener/v1/url?';
        
        // Build the actual endpoint
        $this->endpoint = $this->build_endpoint($this->target);
        
    }
    
    /**
     * Build the final endpoint url.
     * @param  string $endpoint The base endpoint url
     * @return string The final endpoint url
     */
    private function build_endpoint($endpoint) {
		if ( _GOOGLE_API_KEY != null ) {
			$api_key = _GOOGLE_API_KEY;
			$endpoint .= 'key=' . $api_key . '&';
		}
        
        return $endpoint;
    }
        
    /**
     * Shorten a long url into a short Google url.
     * @param  string $url The long url to shorten
     * @return string $url The short url
     */
    public function shorten_url($url) {
        try {
            $resp = json_decode($this->request($url), true);  
            $short_url = $resp['id'];
            
            return $short_url;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }  
    
    /**
     * Send the request to Google to transform the url into a Google short url.
     * @param  string $url The url to transform
     * @return string The Google short url
     */
    private function request($url) {    
        $headers = [
            'Accept: application/json',
            'Cache-Control: no-cache',
            'Content-Type: application/json',
        ];
        
        $data_string = '{ "longUrl": "' . $url . '" }';
        
        // Curl request
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->endpoint,
        ));

        $resp = curl_exec($curl);
        curl_close($curl);
        
        return $resp;
    }
    
}