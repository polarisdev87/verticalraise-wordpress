<?php

namespace classes\models\tables;

class Shortruls {

    /**
     * Class variables.
     */
    private $table_name = "shorturls"; // Table name
    private $wpdb;                     // Wordpress Database Object

    /**
     * Class constructor.
     */

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    /**
     * Delete shortcodes older than 6 months
     * @return type
     */
    public function delete($f_id) {
        $sql = "DELETE FROM `{$this->table_name}` WHERE `fid` = '{$f_id}'";
        $result = $this->wpdb->query($sql);
        return $result;
    }

    public function get_all_f_ids() {
        $results = $this->wpdb->get_results("SELECT DISTINCT `fid` as 'fid' FROM `{$this->table_name}`", OBJECT);
        return $results;
    }

}
