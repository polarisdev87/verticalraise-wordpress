<?php

/**
 * Add or Remove Site admin from site_admin table.
 */

namespace classes\app\site_admin;

use \classes\models\tables\Site_Admin as SiteAdmin;

class Site_Admin
{

    private $table;

    public function __construct() {
        $this->table   = new SiteAdmin;
    }

    public function add_admin_role($user_id) {
        $admin_check = $this->table->admin_check($user_id);
        if ( !$admin_check ) {
            $this->table->set_admin($user_id);
        }
    }

    public function remove_admin_role($user_id) {
        $admin_check = $this->table->admin_check($user_id);
        if ( $admin_check ) {
            $this->table->remove_admin($user_id);
        }
    }
    
    public function get_site_admins() {
        return $this->table->get_all_admins();
    }

}
