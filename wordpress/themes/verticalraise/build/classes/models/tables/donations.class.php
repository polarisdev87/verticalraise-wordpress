<?php

namespace classes\models\tables;

/**
 * Retreive Payment records
 */
class Donations{
    
    /**
     * Class variables.
     */
    private $table_name = "donations"; // Table name
    private $wpdb;                     // Wordpress Database Object
    
    /**
     * Class constructor.
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }  
    
    public function get_total_donors_by_fid($fundraiser_id) {
        // Query the database
        $total_donors = $this->wpdb->get_var($this->wpdb->prepare(
                        "SELECT COUNT(*) FROM {$this->table_name} WHERE f_id = '%d'  AND refunded = 0 AND deleted = 0", $fundraiser_id
        ));
        if ( $total_donors == null )
            return 0;
        return $total_donors;
    }
    
    /**
     * Get all payments for a specific fundraiser id.
     * @param  int   $fundraiser_id 
     * @return float $total
     */
    public function get_all_payments_by_fundraiser_id($fundraiser_id) {
        if ( $this->validate_fundraiser_id($fundraiser_id) ) {
            
            $results = $this->wpdb->get_results( $this->wpdb->prepare(  
                "
                    SELECT * FROM `{$this->table_name}` WHERE f_id = '%d' AND refunded = 0 AND deleted = 0 ORDER BY `amount` DESC
                ",
                $fundraiser_id 
            ), ARRAY_A );
            
            return $results;
        } else {
            return 'fundraiser id does not validate';
        }
        
    }
    
    /**
     * Get the donator emails for a particular fundraiser id.
     * @param  int   $fundraiser_id 
     * @return array emails
     */
    public function get_donator_emails_by_fundraiser_id($fundraiser_id) {
        if ( $this->validate_fundraiser_id($fundraiser_id) ) {
            $data = array();
            $results = $this->wpdb->get_results( $this->wpdb->prepare(  
                "
                    SELECT DISTINCT `email` FROM `{$this->table_name}` WHERE f_id = '%d'  AND refunded = 0 AND deleted = 0 
                ",
                $fundraiser_id 
            ), ARRAY_A );
            
            if ( is_array($results) ) {
                foreach ( $results as $result ) {
                    $data[] = $result['email'];
                }
                
                return $data;
            }
            
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
    public function get_number_supporters_by_fundraiser_id($fundraiser_id) {
        if ( $this->validate_fundraiser_id($fundraiser_id) ) {
            
            $results = $this->wpdb->get_var( $this->wpdb->prepare(  
                "
                    SELECT COUNT(*) FROM `{$this->table_name}` WHERE f_id = '%d'  AND refunded = 0 AND deleted = 0 
                ", 
                $fundraiser_id 
            ) );
            
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
    public function get_number_supporters_by_user_id($user_id, $f_id) {
        $user_id = (int) $user_id;
            
        $results = $this->wpdb->get_var( $this->wpdb->prepare(  
            "
                SELECT COUNT(*) FROM `{$this->table_name}` WHERE uid = '%d' AND f_id = '%d'  AND refunded = 0 AND deleted = 0 
            ", 
            $user_id, $f_id
        ) );

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
    public function get_total_by_user_id($user_id, $f_id) {
        $user_id = (int) $user_id;
        
        $total = $this->wpdb->get_var( $this->wpdb->prepare(  
            "
                SELECT SUM(amount) as amount FROM `{$this->table_name}` WHERE uid = '%d' AND f_id = '%d' AND refunded = 0 AND deleted = 0 
            ", 
            $user_id, $f_id
        ) );
        
        if ( $total == null || $total == false ) {
            $total = 0;
        }
        
        return $total;
    }
    
    /**
     * Delete donations older than 1 year
     * @return type
     */
    public function delete_donations() {
        $sql = "DELETE FROM `{$this->table_name}` WHERE time < DATE_SUB(NOW(),INTERVAL 1 YEAR)";
        $result = $this->wpdb->query( $sql );
        return $result;
    }
    
    /**
     * Validate a specific fundraiser id.
     * @param  int $id Fundraiser id
     * @return bool
     */
    private function validate_fundraiser_id($id = null) {
        if ( empty($id) ) {
            return false;
        } elseif ( is_int($id) == false ) {
            return false;
        } elseif ( get_post_status($id) == false ) {
            return false;
        } else{
            return true;
        }
    }

	public function insert_donation_check( $args ) {

		$result = $this->wpdb->insert( $this->table_name, array(
				"f_id"           => $args["f_id"],
				"uid"            => $args["uid"],
				"anonymous"      => $args["anonymous"],
				"name"           => $args["donor_name"],
				"email"          => $args["donor_email"],
				"amount"         => $args["donation_amount"],
				"time"           => date( 'Y-m-d H:i:s' ),
				"transaction_id" => "",
				"donation_type"  => $args["donation_type"],
			)
		);

		if ( $result ) {
			return $this->wpdb->insert_id;
		}

		return false;

	}


	/**
	 * @param $d_id
	 *
	 * @return null|string
	 */
	public function get_fundraiser_id( $d_id ) {
		return $this->wpdb->get_var( $this->wpdb->prepare(
			"
                SELECT f_id FROM `{$this->table_name}` WHERE id = '%d'
            ",
			$d_id
		) );
	}

	/**
	 * @param $id
	 * @param $donor_name
	 *
	 * @return false|int
	 */
	public function change_donor_name( $id, $donor_name ) {

		$donation = $this->wpdb->get_row( $this->wpdb->prepare(
			"
                SELECT f_id, amount, uid FROM `{$this->table_name}` WHERE id = '%d'
            ",
			$id
		) );

		if($donation){
			delete_transient( 'get_donators_' . $donation->f_id );
		}

		return $this->wpdb->update( $this->table_name, array(
			"name" => $donor_name
		), array(
			"id" => $id
		) );
	}

	/**
	 * @param $d_id
	 * @param $uid
	 *
	 * @return bool|int
	 */
	public function change_recipient( $d_id, $uid ) {

		$old_donation = $this->wpdb->get_row( $this->wpdb->prepare(
			"
                SELECT f_id, amount, uid FROM `{$this->table_name}` WHERE id = '%d'
            ",
			$d_id
		) );

		if ( $old_donation ) {

			$this->wpdb->query( 'START TRANSACTION' );

			$status = $this->wpdb->update( $this->table_name, array( "uid" => $uid ), array( "id" => $d_id ) );

			if ( $status ) {

				if ( $old_donation->uid ) {

					$query = $this->wpdb->prepare( "UPDATE `participant_fundraiser_details` SET supporters = supporters - 1, total = total - %d WHERE participant_id = %d AND fundraiser = %d", array(
						$old_donation->amount,
						$old_donation->uid,
						$old_donation->f_id
					) );

					$status = $this->wpdb->query( $query );
				}

				if ( $status ) {

					if ( $uid ) {
						$query = $this->wpdb->prepare( "UPDATE `participant_fundraiser_details` SET supporters = supporters + 1 , total = total + %d WHERE participant_id = %d AND fundraiser = %d", array(
							$old_donation->amount,
							$uid,
							$old_donation->f_id
						) );
						$status = $this->wpdb->query( $query );
					}

					$this->wpdb->query( 'COMMIT' );

					return $status;
				}

			}

			$this->wpdb->query( 'ROLLBACK' );

		}

		return false;

	}

	public function get_donation($d_id){
		$query = $this->wpdb->prepare("SELECT * from `{$this->table_name}` WHERE id =%d ", array($d_id));
		return $this->wpdb->get_row($query);
	}

	public function set_donation_to_deleted( $d_id ) {

		$this->wpdb->query( 'START TRANSACTION' );

		$donation = $this->wpdb->get_row( $this->wpdb->prepare(
			"
                SELECT f_id, amount, uid FROM `{$this->table_name}` WHERE id = '%d'
            ",
			$d_id
		) );

		if ( $donation ) {

			$status = $this->wpdb->update( $this->table_name, array( 'deleted' => 1 ), array( 'id' => $d_id ) );

			if ( $status ) {

				if ( $donation->uid ) {
					$query = $this->wpdb->prepare( "UPDATE `participant_fundraiser_details` SET supporters = supporters - 1, total = total - %d WHERE participant_id = %d AND fundraiser = %d", array(
						$donation->amount,
						$donation->uid,
						$donation->f_id
					) );
					$status = $this->wpdb->query( $query );

				}

				if ( $status ) {

					$query = $this->wpdb->prepare( "UPDATE `donations_sum` SET amount = amount - %d WHERE f_id = %d", array(
						$donation->amount,
						$donation->f_id
					) );

					$result = $this->wpdb->query( $query );

					if ( $result ) {

						$query = $this->wpdb->prepare( "DELETE FROM `donations_count` WHERE f_id = %d LIMIT 1", array(
							$donation->f_id
						) );

						$result = $this->wpdb->query( $query );
						$this->wpdb->delete( "donation_comments" , array( 'd_id' => $d_id ) );

						if ( $result ) {
							$this->wpdb->query( 'COMMIT' );
							delete_transient( 'get_donators_' . $donation->f_id );
							wp_cache_delete( 'get_amount_' . $donation->f_id );
							wp_cache_delete( 'get_num_supporters_' . $donation->f_id );
							return true;
						}

					}

				}

			}

		}

		$this->wpdb->query( 'ROLLBACK' );

		return false;
	}
}