<?php

/**
 * Model for `email_input` table
 */

namespace classes\models\tables;

class Email_Input
{

    private $table = 'email_input';

    /**
     * Class Constructor.
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    /**
     * Insert record
     *
     * @param string $email
     * @param int $uid
     * @param int $f_id
     *
     * @return bool
     */
    public function insert( $email, $uid, $f_id, $type, $from_name ) {
        $insert = $this->wpdb->insert( $this->table, array(
            'email'     => $email,
            'f_id'      => $f_id,
            'u_id'      => $uid,
            'type'      => $type,
            'channel'   => ($type == 1 ) ? 2 : 1,
            'from_name' => $from_name
                ), array( '%s', '%d', '%d', '%d', '%d', '%s' )
        );

        // Return the results
        if ( empty( $insert ) ) {
            return 'error';
        } else {
            return 'inserted';
        }
    }

    /**
     * Get the records by fundraiser id.
     * @param  int $fid The fundraiser id
     * @return array The results
     */
    public function get_by_fid( $fid ) {
        $results = $this->wpdb->get_results( "SELECT * FROM `{$this->table}` where `f_id` = '{$fid}'", ARRAY_A );

        if ( !empty( $results ) ) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get the records by fundraiser id.
     * @param  int $fid The fundraiser id
     * @return array The results
     */
    public function get_by_id( $id ) {
        $results = $this->wpdb->get_results( "SELECT * FROM `{$this->table}` where `id` = '{$id}'", ARRAY_A );

        if ( !empty( $results ) ) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get the potential donor records (channel == 1) by fundraiser id.
     * @param  int $fid The fundraiser id
     * @return array The results
     */
    public function get_potential_donors_by_fid( $fid ) {
        $results = $this->wpdb->get_results( "SELECT * FROM `{$this->table}` where `f_id` = '{$fid}' AND `channel` = '1'", ARRAY_A );

        if ( !empty( $results ) ) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get the email share records (channel == 2) by fundraiser id.
     * @param  int $fid The fundraiser id
     * @return array The results
     */
    public function get_email_shares_by_fid( $fid ) {
        $results = $this->wpdb->get_results( "SELECT * FROM `{$this->table}` where `f_id` = '{$fid}' AND `channel` = '2'", ARRAY_A );

        if ( !empty( $results ) ) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get the records by fundraiser id, user id and email.
     *
     * @param  int    $fid   The fundraiser id
     * @param  int    $uid   The user id
     * @param  string $email The email
     *
     * @return array  The results
     */
    public function get_by_email_fid_uid( $fid, $uid, $email ) {
        $results = $this->wpdb->get_results( "SELECT * FROM `{$this->table}` where `f_id` = '{$fid}' AND `u_id` = '{$uid}' AND `email` = '{$email}'", ARRAY_A );

        if ( !empty( $results ) ) {
            return $results;
        } else {
            return false;
        }
    }

    public function get_emails_by_id_order( $limit ) {
        $results = $this->wpdb->get_results( "SELECT * FROM `{$this->table}` ORDER BY id desc LIMIT {$limit}", ARRAY_A );
        return $this->make_array( $results );
    }

    private function make_array( $results ) {
        $return_arr = array();
        if ( !empty( $results ) ) {
            foreach ( $results as $result ) {
                $key              = $result["f_id"] . "_" . $result["u_id"] . "_" . $result["email"];
                $return_arr[$key] = $result;
            }
            return $return_arr;
        } else {
            return $return_arr;
        }
    }

}
