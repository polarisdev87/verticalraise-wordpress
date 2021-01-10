<?php

/**
 * Get and set email_queue records for a specific user.
 */

namespace classes\models\tables;

class Email_Queue
{

    private $table                = 'email_queue';
    private $default_verify_limit = 100;
    private $default_send_limit   = 10000;
    private $default_delete_limit = 100;

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
    public function insert( $email, $uid, $f_id, $type, $from_name, $logged_in = '', $logged_in_u_id = '', $parent = '', $share_type ) {
        $insert = $this->wpdb->insert(
            $this->table,
                array(
                    'email'          => $email,
                    'f_id'           => $f_id,
                    'u_id'           => $uid,
                    'type'           => $type,
                    'channel'        => ( $type == 1 ) ? 2 : 1,
                    'share_type'     => $share_type,
                    'from_name'      => $from_name,
                    'logged_in'      => $logged_in,
                    'logged_in_u_id' => $logged_in_u_id,
                    'parent'         => $parent
                ),
                array( '%s', '%d', '%d', '%d', '%d', '%d', '%s', '%d', '%d' )
        );

        // Return the results
        if ( empty( $insert ) ) {
            return 'error';
        } else {
            return 'inserted';
        }
    }

    /**
     * Get the records ordered by created_at date.
     * @param  int $limit The number of results to return
     * @return array The results
     */
    public function get_all( $limit ) {
        // Default limit clause
        $limit_clause = "";

        if ( isset( $limit ) ) {
            $limit_clause = "LIMIT {$limit}";
        }

        $results = $this->wpdb->get_results( "SELECT * FROM `{$this->table}` ORDER BY `created_at` DESC {$limit_clause}" );

        foreach ( $results as $result ) {
            $queued[] = $result[0];
        }

        if ( !empty( $queued ) ) {
            return $queued;
        } else {
            return false;
        }
    }

    /**
     * Get the records by fundraiser id.
     * @param  int $fid The fundraiser id
     * @return array The results
     */
    public function get_by_fid( $fid, $uid, $type ) {
        $results = $this->wpdb->get_results( "SELECT `email` FROM `{$this->table}` where `f_id` = '{$fid}'  AND `u_id` = '{$uid}' AND `type` = '{$type}'", ARRAY_N );

        foreach ( $results as $result ) {
            $queued[] = $result[0];
        }

        if ( !empty( $queued ) ) {
            return $queued;
        } else {
            return false;
        }
    }

    /**
     * Get the oldest 500 unverified records.
     * What happens if 0 records
     * @param  int $limit The number of results to return
     * @return array The results
     */
    public function get_ready_to_verify( $limit = 100 ) {
        // Default limit clause
        $limit_clause = "LIMIT {$this->default_verify_limit}";

        if ( isset( $limit ) ) {
            $limit_clause = "LIMIT {$limit}";
        }

        $results = $this->wpdb->get_results( "SELECT `id`, `email` FROM `{$this->table}` WHERE `verified` = '0' ORDER BY `created_at` DESC {$limit_clause}", ARRAY_A );

        foreach ( $results as $result ) {
            $queued[] = $result;
        }

        if ( !empty( $queued ) ) {
            return $queued;
        } else {
            return false;
        }
    }

    /**
     * Get the oldest 10000 ready to send records.
     * What happens if 0 records
     * @param  int $limit The number of results to return
     * @return array The results
     */
    public function get_ready_to_send( $limit = 100 ) {
        // Default limit clause
        $limit_clause = "LIMIT {$this->default_verify_limit}";

        if ( isset( $limit ) ) {
            $limit_clause = "LIMIT {$limit}";
        }

        $results = $this->wpdb->get_results( "SELECT `id`, `f_id`, `u_id`, `email`, `type`, `share_type`, `from_name`, `channel` , `attempts`, `parent` FROM `{$this->table}` WHERE "
                . "`verified` = '1' AND `sent` = '0' AND `attempts` < 3 AND `role` = '0' AND `disposable` = '0' ORDER BY `f_id` DESC {$limit_clause}", ARRAY_A );

        foreach ( $results as $result ) {
            $queued[] = $result;
        }

        if ( !empty( $queued ) ) {
            return $queued;
        } else {
            return false;
        }
    }

    /**
     * Get the oldest 10000 ready to move records.
     * What happens if 0 records
     * @param  int $limit The number of results to return
     * @return array The results
     */
    public function get_ready_to_move( $limit = 100 ) {
        // Default limit clause
        $limit_clause = "LIMIT {$this->default_verify_limit}";

        if ( isset( $limit ) ) {
            $limit_clause = "LIMIT {$limit}";
        }

        $results = $this->wpdb->get_results( "SELECT * FROM `{$this->table}` WHERE `verified` = '1' AND `sent` = '1' AND `moved` = '0' AND `channel` = '1' ORDER BY `f_id` DESC {$limit_clause}", ARRAY_A );

        foreach ( $results as $result ) {
            $queued[] = $result;
        }

        if ( !empty( $queued ) ) {
            return $queued;
        } else {
            return false;
        }
    }

    /**
     * Update a record's 'verified' status.
     *
     * @param string $email
     * @param int $uid
     * @param int $f_id
     *
     * @return mixed False if there is an error, otherwise number of rows updated
     */
    public function update_verified( $id, $verified, $result = null ) {
        $update = $this->wpdb->update( $this->table, array(
            'verified'   => $verified,
            'reason'     => $result['reason'],
            'result'     => $result['result'],
            'role'       => $result['role'],
            'disposable' => $result['disposable'],
            'accept_all' => $result['accept_all'],
                ), array(
            'id' => $id
                )
        );

        return $update;
    }

    /**
     * Update a record's 'sent' status.
     *
     * @param int $id Record id
     * @param int $sent Sent status (null | timestamp)
     *
     * @return mixed False if there is an error, otherwise number of rows updated
     */
    public function update_sent( $id, $sent ) {
        $update = $this->wpdb->update( $this->table, array(
            'sent' => $sent,
                ), array(
            'id' => $id
                )
        );

        return $update;
    }

    /**
     * Increment record 'attemtps' field
     *
     * @param int $id Record Id
     * @param int $attempts number of attempts
     *
     * @return mixed False if there is an error, otherwise number of rows updated
     */
    public function increment_attempts( $id, $attempts ) {
        $attempts = intval( $attempts ) + 1;
        $update   = $this->wpdb->update( $this->table, array(
            'attempts' => $attempts
            ), array(
        'id' => $id
            )
        );
        return $update;
    }

    /**
     * Update a record's 'moved' status.
     *
     * @param int $id Record id
     * @param int $sent Sent status (null | timestamp)
     *
     * @return mixed False if there is an error, otherwise number of rows updated
     */
    public function update_moved( $id, $moved ) {
        $update = $this->wpdb->update( $this->table, array(
            'moved' => $moved,
                ), array(
            'id' => $id
                )
        );

        return $update;
    }

    /**
     * Delete record
     *
     * @param string $email
     * @param int $uid
     * @param int $f_id
     *
     * @return bool
     */
    public function delete( $id ) {
        $delete = $this->wpdb->delete( $this->table, array(
            'id' => 1
                ), array( '%d' )
        );
    }

    public function delete_unverified() {
        $delete = $this->wpdb->delete( $this->table, array(
            'verified' => 2
                ), array( '%d' )
        );
    }

    public function delete_moved() {
        $delete = $this->wpdb->delete( $this->table, array(
            'verified' => 1,
            'sent'     => 1,
            'moved'    => 1
                ), array( '%d' )
        );
    }

    public function delete_channel2_sent() {
        $delete = $this->wpdb->delete( $this->table, array(
            'channel'  => 2,
            'verified' => 1,
            'sent'     => 1,
                ), array( '%d', '%d', '%d' )
        );
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

    public function insert_missing_emails( $array ) {
        $insert = $this->wpdb->insert( $this->table, array(
            'email'     => $array['email'],
            'f_id'      => $array['f_id'],
            'u_id'      => $array['u_id'],
            'type'      => $array['type'],
            'channel'   => $array['channel'],
            'from_name' => $array['from_name']
                ), array( '%s', '%d', '%d', '%d', '%s' )
        );

        // Return the results
        if ( empty( $insert ) ) {
            return false;
        } else {
            return true;
        }
    }

}
