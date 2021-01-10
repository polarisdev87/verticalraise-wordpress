<?php

namespace classes\app\emails;

class Check_Duplicates
{

    /**
     * Check for duplicate email addresses so that we do not resend to recipients who have already been emailed for this user and fundraiser.
     *
     * @param  int    $user_ID
     * @param  array  $potential_donors An array of email addresses for the potential donors.
     * @param  string $email The we want to make sure is not a duplicate.
     *
     * @return bool
     */
    public function check( $records, $email ) {

        foreach ( $records as $record_set ) {
            if ( $this->is_empty( $record_set ) ) {
                continue;
            }
            if ( $this->compare( $record_set, $email ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Compare the email against the array of passed in emails.
     *
     * @param array  $records
     * @param string $email
     *
     * @return bool
     */
    private function compare( $records, $email ) {
        foreach ( $records as $record ) {
            if ( !empty( $record ) ) {
                if ( $record == $email ) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check if the records are empty.
     */
    private function is_empty( $records = null ) {
        if ( empty( $records ) ) {
            return true;
        }

        return false;
    }

}
