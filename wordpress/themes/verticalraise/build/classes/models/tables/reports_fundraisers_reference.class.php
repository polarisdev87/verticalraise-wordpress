<?php

/**
 * Get fundraiser data by indexed fields
 */

namespace classes\models\tables;

class Reports_Fundraisers_Reference
{

    private $table = 'reports_fundraisers_reference';
    private $limit = 1000;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    
    public function insert($f_id, $name, $start_date, $end_date) {
        $insert = $this->wpdb->insert( 
            $this->table, 
                array(
                    'f_id' => $f_id,
                    'name' => $name,
                    'created_date' => null,
                    'start_date' => $start_date,
                    'end_date' => $end_date
                ), 
                array( '%d', '%s', '%s', '%s', '%s' )
        );

        // Return the results
        if ( empty( $insert ) ) {
            return 'error';
        } else {
            return 'inserted';
        }
    }
    
    public function migrate_insert($f_id, $name, $created, $start_date, $end_date) {
        $exists = $this->wpdb->get_results( "SELECT * FROM `{$this->table}` WHERE `f_id` = '{$f_id}' LIMIT 1 ");
        if (count($exists)<0) {
            $insert = $this->wpdb->insert( 
                $this->table, 
                    array(
                        'f_id' => $f_id,
                        'name' => $name,
                        'created_date' => $created,
                        'start_date' => $start_date,
                        'end_date' => $end_date
                    ), 
                    array( '%d', '%s', '%s', '%s', '%s' )
            );

            // Return the results
            if ( empty( $insert ) ) {
                return 'error';
            } else {
                return 'inserted';
            }
        }
    }
    
    public function update($id, $column = null, $value = null) {
        $update = false;
        if (!empty($column) && !empty($value) && !empty($id)) {
            $update = $this->wpdb->update( 
                $this->table, 
                array(
                    $column  => $value
                    ), 
                array(
                    'f_id' => $id
                )
            );
        }

        return $update;

    }
  
    public function get_by_start_date( $date = array(), $limit = null ) {
        // Default limit clause
        $limit_clause = "LIMIT {$this->limit}";

        if ( isset( $limit ) ) {
            $limit_clause = "LIMIT {$limit}";
        }
        
        // Check if a range and set where clause
        $where_clause = "";
        if (!empty($date)) {
            if ( !empty($date['from'])) {
                $where_clause .= "`start_date` >= '{$date['from']}'";
            }
            if ( !empty($date['to'])) {
                $where_clause .= " AND `start_date` <= '{$date['to']}'";
            }
        }

        $results = $this->wpdb->get_results( "SELECT * FROM `{$this->table}` WHERE {$where_clause} ORDER BY `start_date` DESC {$limit_clause}", ARRAY_A );

        return $results;
    }
    
    public function get_by_end_date( $date = array(), $limit = null ) {
        // Default limit clause
        $limit_clause = "LIMIT {$this->limit}";

        if ( isset( $limit ) ) {
            $limit_clause = "LIMIT {$limit}";
        }
        
        // Check if a range and set where clause
        $where_clause = "";
        if (!empty($date)) {
            if ( !empty($date['from'])) {
                $where_clause .= "`end_date` >= '{$date['from']}'";
            }
            if ( !empty($date['to'])) {
                $where_clause .= " AND `end_date` <= '{$date['to']}'";
            }
        }

        $results = $this->wpdb->get_results( "SELECT * FROM `{$this->table}` WHERE {$where_clause} ORDER BY `end_date` DESC {$limit_clause}", ARRAY_A );

        return $results;
    }
}