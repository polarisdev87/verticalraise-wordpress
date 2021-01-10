<?php

namespace classes\models\tables;

class Site_Admin
{

    private $table_name = 'site_admin';

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function admin_check( $user_id ) {
        $check = $this->wpdb->get_row("SELECT * FROM `{$this->table_name}` WHERE `uid` = '{$user_id}' LIMIT 1", ARRAY_N);
        if ( !empty($check) ) {
            return 1;
        }
        return 0;
    }

    public function set_admin( $user_id ) {
        $this->wpdb->insert(
                $this->table_name, array (
            'uid'    => $user_id,
                )
        );

        $insert_id = $this->wpdb->insert_id;
        return $insert_id;
    }

    public function remove_admin( $user_id ) {
        $delete = $this->wpdb->delete($this->table_name, array (
            'uid' => $user_id
                ), array ('%d')
        );
    }
    
     public function get_all_admins() {
        $site_admins = array ();
        $results     = $this->wpdb->get_results("SELECT * FROM `{$this->table_name}` ", ARRAY_A);

        if ( !empty($results) ) {
            foreach ( $results as $result ) {
                $site_admins[] = $result['uid'];
            }
        }

        return $site_admins;
    }


}
