<?php

//namespace classes;

use \classes\Get_User_Info;
use \classes\VerticalRaise_Shorturl;

use classes\app\utm\UTM;
/**
 * Methods to construct the SMS text message content
 */
class Build_SMS
{

    /**
     * Class Constructor.
     */
    public function __construct() {        
        load_class('get_user_info.class.php');
        load_class('verticalraise_shortcode.class.php');
        
        $this->get_user_info      = new Get_User_Info();
        $this->short_url          = new VerticalRaise_Shorturl();
        $this->default_from_name  = _DEFAULT_FROM_NAME;
    }

    /**
     * Set the from name based on a user ID or $_POST value.
     * @param  int    $user_ID The user's id
     * @return string The from name
     */
    public function set_from_name($user_ID) {
        $default = $this->default_from_name;
        
        // If there is a From $_POST
        if ( isset($_POST['your_name']) ) {
            // Validate the name
            $your_name  = trim($_POST['your_name']);
            $your_name  = sanitize_text_field($your_name);
            $your_name  = ucwords($your_name);
            
            return $your_name;
            
        } else if ( $user_ID != 0 ) {
            // If there is a user id
            $full_name = $this->get_user_info->get_full_name_with_backup($user_ID);
            
            if ( !empty($full_name) ) {
                return $full_name;
            }
        }
        
        return $default;
    }
    
    /**
     * Set the person's full name.
     * @param int $user_ID The user ID
     * @return string The person's full name
     */
    public function set_full_name($user_ID) {
        return $this->get_user_info->get_full_name_with_backup($user_ID);
    }
        
    /**
     * Get the fundraiser title.
     * @param int $f_id The fundraiser ID
     * @return string The fundraiser's title
     */
    public static function set_title($f_id) {
        $f_id = (int) $f_id;
        return get_the_title($f_id);
    }
    
    /**
     * Set the click url.
     *
     * @param int $f_id    The fundraiser ID
     * @param int $user_id The user ID
     * @param int $parent if parent invite

     * @return string $url The click URL
     */
    public function set_click_url($f_id, $user_id, $parent = 0, $utm_code) {
        // Grab the Wefund4u Short URL
        $url = $this->short_url->get($f_id, $user_id, 'sms', $parent, $utm_code);
        return $url;
    }
    
    /**
     * Generate click url.
     *
     * @param int $f_id    The fundraiser ID
     * @param int $user_id The user ID
     *
     * @return string The click url
     */
    private function generate_click_url($f_id, $user_id) {
        // Get the Wordpress Permalink for the fundraiser
        return get_permalink($f_id) . 'sms/' . $user_id;
    }
    

    
}