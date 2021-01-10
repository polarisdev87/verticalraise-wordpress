<?php

/**
 *
 */
class Participant_Records2
{

    /**
     * Get the participants that have shared less than 10 emails or less than $500 raised.
     */
    public function get_low_sharing_by_fid( $f_id ) {
        global $wpdb;

        $f_id = (int) $f_id;

        $results = $wpdb->get_results($wpdb->prepare(
                        "
                SELECT * FROM `participant_fundraiser_details` WHERE fundraiser = '%d' AND ( ( email < '10' AND total < " . _PARTICIPATION_GOAL . " ) OR total = '0')  ORDER BY `email` ASC
            ", $f_id
                ), ARRAY_A);

        return $results;
    }

    /**
     * Get all participants.
     */
    public function get_all_participants_by_fid( $f_id ) {
        global $wpdb;

        $f_id = (int) $f_id;

        $results = $wpdb->get_results($wpdb->prepare(
            "
                SELECT * FROM `participant_fundraiser_details` WHERE fundraiser = '%d' ORDER BY `email` ASC
            ", $f_id
        ), ARRAY_A);

        return $results;
    }
}
