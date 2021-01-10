<?php

use \classes\app\site_admin\Site_Admin;

class Admins
{

    public function __construct() {
        load_class('secondary_admins.class.php');
        $this->secondary_admins = new Secondary_Admins;
        $this->site_admins      = new Site_Admin;
    }

    public function get_all_admins( $f_id ) {

        $admins = array ();

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
        $site_admins = $this->site_admins->get_site_admins();
        return $site_admins;
    }

    public function get_post_author( $f_id ) {
        $post_author = get_post_field('post_author', $f_id);

        if ( !empty($post_author) )
            return $post_author;
    }

}
