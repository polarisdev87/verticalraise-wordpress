<?php

use \classes\VerticalRaise_Shorturl;

/**
 * Check to see if the page request is coming from our shorturl domain. If it is, check to see if there is a 6 character code attached to the request.
 * If there is, then forward the user to either the fundraiser landing page or the parent invite wizard.
 */
class Shorturl_Check
{

    /**
     * @var string Short urls
     */
    private $shorturl;
    private $shorturl_local;
    private $shorturl_dev;

    /**
     * Class Constructor.
     */
    public function __construct () {
        // Short URLs
        $this->shorturl       = _SHORTURL_BASE;
        $this->shorturl_local = _SHORTURL_BASE_LOCAL;
        $this->shorturl_dev   = _SHORTURL_BASE_DEV;
    }

    /**
     * Check to see if the request is a short url.
     */
    public function check() {

        // Check if the expected short url domain is within the current http_host
        if ( $this->is_domain_match() !== false ) {

            // Get the incoming code
            $code = $this->get_code();
            
            // Validate
            $validated = $this->validate($code);
            
            if ( $validated == false ) {
                // Kick the user to the front page if the code does not validate
                header("Location: " . get_site_url() . "/?signup");
                exit();
            }

            // Sanitize
            $code = sanitize_text_field($code);
            
            // Instantiate Wefund4u Shortcode class
            load_class('verticalraise_shortcode.class.php');
            $shorturl = new VerticalRaise_Shorturl();

            // Check if the code exists
            $results = $shorturl->lookup_code($code);
            
            // Code exists
            if ( !empty($results) ) {
                $uid     = $results[0]['uid'];
                $fid     = $results[0]['fid'];
                $channel = $results[0]['channel'];
                $parent  = $results[0]['parent'];
                $utm_code['source']  = $results[0]['utm_source'];
                $utm_code['medium']  = $results[0]['utm_medium'];
                $utm_code['campaign']  = $results[0]['utm_campaign'];
                $utm_code['content']  = $results[0]['utm_content'];
                $utm_code['term']  = $results[0]['utm_term'];
                
                

                // Redirect the user to the fundraiser page
                $redirect_url = $this->get_redirect_url($fid, $uid, $channel, $parent, $utm_code);
                
                header("Location: {$redirect_url}");
                exit();
            } else {
                // Code does not exist, kick user to the front page
                header("Location: " . get_site_url());
                exit();
            }
        }
    }

    /**
     * Get the code off the url
     * @return string $code The code
     */
    private function get_code() {
        $code = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $code = str_replace('/', '', $code);

        return $code;
    }

    /**
     * Validate the code
     * @param string $code 6 character code
     * @return bool
     */
    private function validate( $code = '' ) {
        // Make sure the code is valid
        if ( !empty($code) ) {
            // 6 Characters
            if ( strlen($code) != 6 ) {
                return false;
            }
            // Alphanumeric
            if ( ctype_alnum($code) == false ) {
                return false;
            }

            // The code passes our tests
            return true;
        }

        // No code was supplied
        return false;
    }

    /**
     * Get the redirect url.
     *
     * @param int    $fid     The fundraiser ID
     * @param int    $uid     The user ID
     * @param string $channel The invite channel
     * @param int    $parent
     *
     * @return string url
     */
    private function get_redirect_url( $fid, $uid, $channel = '', $parent = 0, $utm_code ) {
        
        // Parent Invite Wizard
        if ( $parent == 1 ) {
            return get_site_url() . '/invite-parent-start/?fundraiser_id=' . $fid . '&parent=1&uid=' . $uid . '&utm_source=' . $utm_code['source'] . '&utm_medium=' . $utm_code['medium'] . '&utm_campaign=' . $utm_code['campaign'] . '&utm_content=' . $utm_code['content'] . '&utm_term='.$utm_code['term'];
        }
        // SMS Invite Wizard
        return get_permalink($fid) . $channel . '/' . $uid . '/' . '?utm_source=' . $utm_code['source'] . '&utm_medium=' . $utm_code['medium'] . '&utm_campaign=' . $utm_code['campaign'] . '&utm_content=' . $utm_code['content'] . '&utm_term='.$utm_code['term'];
    }

    /**
     * Define the shorturl domain based on the server environment.
     * @return string The domain
     */
    private function get_domain() {
        if ( _SERVER_TYPE == 'dev' ) {
            if ( _IS_LOCAL_DEV ) {
                return $this->shorturl_local;
            }
            return $this->shorturl_dev;
        }

        return $this->shorturl;
    }
    
    /**
     * Get the current server HTTP_HOST.
     */
    private function get_current_http_host() {
        return strtolower($_SERVER['HTTP_HOST']);
    }
    
    /**
     * Does the current http_host match the expected short_url domain?
     * @return bool
     */
    private function is_domain_match() {
        // Establish the domain to use
        $shorturl_url = $this->get_domain();
        $http_host    = $this->get_current_http_host();
    
        //echo "Domain: " . $http_host;
        //echo "Shorturl: " . $shorturl_url;
        return strpos($http_host, $shorturl_url);
    }

}

$shorturl_check = new Shorturl_Check();
$shorturl_check->check();