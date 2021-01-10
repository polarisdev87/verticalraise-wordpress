<?php

/**
 * Retreive Payment records
 */
use classes\models\tables\Donations_Total;

class Payment_Records
{

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    /**
     * Get TOTAL payment amount for a specifc fundraiser id.
     * @param  int   $fundraiser_id 
     * @return float $total
     */
    public function get_total_by_fundraiser_id( $fundraiser_id ) {
        if ( $this->validate_fundraiser_id($fundraiser_id) ) {
            $donations_sum = new Donations_Total();
            $total         = $donations_sum->get_total_by_f_id($fundraiser_id);
        } else {
            return 'fundraiser id does not validate';
        }

        return $total;
    }

    public function get_total_to_end_date($fundraiser_id){

	    $query = $this->wpdb->prepare( " SELECT sum(`amount`) FROM `donations` d WHERE f_id = %d AND d.refunded = '0' AND d.deleted  = '0' and DATE(`time`) < ( SELECT `meta_value` FROM `wp_postmeta` WHERE `post_id` = %d AND `meta_key` = 'end_date' ) ", array(
		    $fundraiser_id,
		    $fundraiser_id
	    ) );


	    return $this->wpdb->get_var($query);
    }

    public function get_total_since_end_date($fundraiser_id){
	    $query = $this->wpdb->prepare( " SELECT sum(`amount`) FROM `donations` d WHERE f_id = %d AND d.refunded = '0' AND d.deleted  = '0' and DATE(`time`) >= ( SELECT `meta_value` FROM `wp_postmeta` WHERE `post_id` = %d AND `meta_key` = 'end_date' ) ", array(
		    $fundraiser_id,
		    $fundraiser_id
	    ) );


	    return $this->wpdb->get_var($query);
    }

    /**
     * Get all payments for a specific fundraiser id.
     * @param  int   $fundraiser_id 
     * @return float $total
     */
    public function get_all_payments_by_fundraiser_id( $fundraiser_id ) {
        if ( $this->validate_fundraiser_id($fundraiser_id) ) {

            $results = $this->wpdb->get_results($this->wpdb->prepare(
                            "
                    SELECT * FROM `donations` WHERE f_id = '%d' AND refunded = 0  AND deleted = 0 ORDER BY `amount` DESC, `time` DESC
                ", $fundraiser_id
                    ), ARRAY_A);

            return $results;
        } else {
            return 'fundraiser id does not validate';
        }
    }

    /*
     * Get all payments for a specific fundraiser id and participant Id.
     * @param int $fundraiser_id
     * @param int $user_id
     * @return float $total by user id
     */

    public function get_all_payments_by_fundraiser_uid( $user_id, $fundraiser_id ) {
        if ( $this->validate_fundraiser_id($fundraiser_id) ) {

            $results = $this->wpdb->get_results($this->wpdb->prepare(
                            "SELECT d.*, dc.comment "
                            . "FROM `donations` d LEFT JOIN `donation_comments` dc on d.id = dc.d_id "
                            . "WHERE d.f_id = '%d' AND d.uid = '%d' AND d.refunded = 0  AND deleted = 0 ORDER BY d.`amount` DESC
                ", $fundraiser_id, $user_id
                    ), ARRAY_A);

            return $results;
        } else {
            return 'fundraiser id does not validate';
        }
    }

    /**
     * Get the total number of supporters by fundraiser id.
     * @param  int   $fundraiser_id 
     * @return float $total
     */
    public function get_number_supporters_by_fundraiser_id( $fundraiser_id ) {
        if ( $this->validate_fundraiser_id($fundraiser_id) ) {

            $results = $this->wpdb->get_var($this->wpdb->prepare(
                            "
                    SELECT COUNT(*) FROM `donations` WHERE f_id = '%d'  AND refunded = 0  AND deleted = 0 
                ", $fundraiser_id
            ));

            if ( $results == false || $results == null ) {
                $results = 0;
            }

            return $results;
        } else {
            return 'fundraiser id does not validate';
        }
    }

    /**
     * Get the total number of supporters by user id.
     * @param  int   $fundraiser_id 
     * @return float $total
     */
    public function get_number_supporters_by_user_id( $user_id, $f_id ) {
        $user_id = (int) $user_id;

        $results = $this->wpdb->get_var($this->wpdb->prepare(
                        "
                SELECT COUNT(*) FROM `donations` WHERE uid = '%d' AND f_id = '%d' AND refunded = 0  AND deleted = 0 
            ", $user_id, $f_id
        ));

        if ( $results == false || $results == null ) {
            $results = 0;
        }

        return $results;
    }

    /**
     * Get TOTAL payment amount for a user id.
     * @param  int $user_id
     * @return float $total
     */
    public function get_total_by_user_id( $user_id, $f_id ) {
        $user_id = (int) $user_id;

        $total = $this->wpdb->get_var($this->wpdb->prepare(
                        "
                SELECT SUM(amount) as amount FROM `donations` WHERE uid = '%d' AND f_id = '%d' AND refunded = 0  AND deleted = 0 
            ", $user_id, $f_id
        ));

        if ( $total == null || $total == false ) {
            $total = 0;
        }

        return $total;
    }

    /**
     * Validate a specific fundraiser id.
     * @param  int $id Fundraiser id
     * @return bool
     */
    private function validate_fundraiser_id( $id = null ) {
        if ( empty($id) ) {
            return false;
        } elseif ( is_int((int)$id) == false ) {
            return false;
        } elseif ( get_post_status($id) == false ) {
            return false;
        } else {
            return true;
        }
    }

}
