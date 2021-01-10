<?php

namespace classes\app\fundraiser;

use classes\models\tables\Fundraiser_Details as Fundraiser_Details_Table;

/**
 * Interface for working with the `fundraiser_details` database table.
 */
class Fundraiser_Details
{
    public static function add_fundraiser( $fid ) {

        $table = new Fundraiser_Details_Table();

		$fid = (int) trim( $fid );

        $start_date = get_post_meta( $fid, 'start_date', true );
		$end_date   = get_post_meta( $fid, 'end_date', true );
		$sec_date   = get_post_meta( $fid, 'secondary_end_date', true );
        $goal       = get_post_meta( $fid, 'fundraising_goal', true );

        if ( $table->table_exist() ) {

            $fundraiser = array(
                'id'          => $fid,
                'start_date'  => $start_date,
				'end_date'    => $end_date,
				'sec_date'    => $sec_date,
                'goal'        => $goal,
                'transferred' => 0
            );

            $table->insert_fundraiser( $fundraiser );
        }
    }

    public static function update_fundraiser( $fid ) {

        $table = new Fundraiser_Details_Table();

		$fid = (int) trim( $fid );

        $start_date = get_post_meta( $fid, 'start_date', true );
		$end_date   = get_post_meta( $fid, 'end_date', true );
		$sec_date   = get_post_meta( $fid, 'secondary_end_date', true );
        $goal       = get_post_meta( $fid, 'fundraising_goal', true );

        if ( $table->table_exist() ) {

            $fundraiser = array(
                'id' => $fid,
                'start_date' => $start_date,
				'end_date' => $end_date,
				'sec_date' => $sec_date,
                'goal' => $goal
            );

            $table->update_fundraiser( $fundraiser );
        }

	}

	/**
	 * Get the fundraiser end date.
	 *
	 * @param int $fid The fundraiser id.
	 * @param bool $timestamp Whether to return timestamp or string.
	 *
	 * @return string|int|null
	 */
	public static function get_end_date( $fid, $timestamp = false ) {

		$table = new Fundraiser_Details_Table();

		$record = $table->get_single_count_row( $fid );

		$end_date = null;

		if ( ! empty( $record->end_date ) ) {
			$end_date = $record->end_date;
		}

		if ( ! empty( $record->secondary_end_date ) ) {
			$end_date = $record->secondary_end_date;
		}

		// Return a timestamp.
		if ( ! empty( $end_date ) && $timestamp ) {
			$end_date = strtotime( $end_date );
		}

		return $end_date;
	}

	/**
	 * Check for existing fundraiser name.
	 */
	public static function check_name() {

		$name = $_POST['fundraiser_name'];
		$fid  = $_POST['fundraiser_id'];

		$post = get_page_by_title( $name, OBJECT, 'fundraiser' );

		if ( $post === null ) {
			wp_send_json( array( 'duplicated' => false ), 200 );
		}

		if ( $fid == $post->ID ) {
			wp_send_json( array( 'duplicated' => false ), 200 );
		}

		wp_send_json( array( 'duplicated' => true ), 200 );

	}

}
