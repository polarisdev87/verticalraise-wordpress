<?php

namespace classes\models\mixed;

use \classes\models\tables\Secondary_Admins;
use \classes\app\site_admin\Site_Admin;

class Admins
{

    public function __construct() {
        $this->secondary_admins = new Secondary_Admins;
        $this->site_admins      = new Site_Admin;
    }

    public function get_all_admins($f_id) {
        
        $admins = array();
        
        // Get the secondary admin IDs
        $s_admins = $this->secondary_admins->get_sadmin_ids_by_fid($f_id);
        if ( !empty($s_admins) ) {
            $admins = array_merge($admins, $s_admins);
        }
        
        // Get Wefund4u Admin IDs
        $site_admins = $this->get_site_admins();
        if ( !empty($site_admins) ) {
            $admins = array_merge($admins, $site_admins);
        }
        
        // Get the Post Author
        $post_author = $this->get_post_author($f_id); 
        if ( !empty($post_author) ) {
            $admins[] = $post_author;
        }
        
        // Return all the IDs
        return array_unique($admins);
    }
    
    public function get_site_admins() {
        // Get list of admins on Wefund4u
//        $args = array(
//            'role' => 'administrator',
//        );
//        
//        $admin_users = get_users($args);
//        $admins = array();
//        foreach ( $admin_users as $admin_user ) {
//            $admins[] = $admin_user->ID;
//        }
//        
//        return $admins;
        $site_admins = $this->site_admins->get_site_admins();        
        return $site_admins;
    }
    
    public function is_site_admin($user_id) {
        $admins = $this->get_site_admins();
        if ( in_array($user_id, $admins) ) {
            return true;
        }
        
        return false;
    }
    
    public function get_post_author($f_id) {
        $post_author = get_post_field( 'post_author', $f_id );
        
        if ( !empty($post_author) ) 
            
        return $post_author;
    }
    
    public function is_fundraiser_admin($user_id, $f_id) {
        $post_author = $this->get_post_author($f_id);
            
        // Is an author
        if ( !empty($post_author) ) {
            if ( $post_author == $user_id ) {
                return true;
            }
        }
        
        $s_admins = $this->secondary_admins->get_sadmin_ids_by_fid($f_id);

        // Is an sadmin
        if ( !empty($s_admins ) ) {
            if ( in_array_my($user_id, $s_admins) ) {
                return true;
            }
        }
        
        return false;
    }
    
    public function is_fundraiser_admin_or_site_admin($user_id, $f_id) {
        if ( $this->is_fundraiser_admin($user_id, $f_id) ) {
            return true;
        }
        
        if ( $this->is_site_admin($user_id, $f_id) ) {
            return true;
        }
        
        return false;
    }


}