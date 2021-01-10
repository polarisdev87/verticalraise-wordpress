<?php

/**
 * Get fundraiser statistics.
 */

namespace classes\app\fundraiser;

use \classes\models\tables\Participant_Fundraiser_Details as Participant_Fundraiser_Details;
use \classes\models\tables\Fundraiser_Participants as Fundraiser_Participants;
use \classes\models\tables\Donations as Donations;
use \classes\models\tables\Donations_Count;

class Fundraiser_Statistics
{

    private $fundraiser_Id;                          // Fundraier ID
    private $table1;                                 // Participant Fundraiser Details class object
    private $table2;                                 // Fundraiser Paticipants class object
    private $table3;                                 // Donations class object

    public function __construct( $f_id ) {
        $this->fundraiser_Id = (int) $f_id;
        $this->table1        = new Participant_Fundraiser_Details;
        $this->table2        = new Fundraiser_Participants;
        $this->table3        = new Donations;
        $this->donations_count        = new Donations_Count;
    }

    public function participation_score() {
        $filter_participants = $this->table1->get_participant_by_fid_emailcount( $this->fundraiser_Id );
        $total_participants  = $this->table2->get_total_participants_by_fid( $this->fundraiser_Id );

        if ( $total_participants == 0 ) {
            return 0;
        } else {
            return number_format( $filter_participants / $total_participants, 4 );
        }
    }

    public function email_quality_score() {
        $total_donors = $this->table3->get_total_donors_by_fid( $this->fundraiser_Id );
        $total_emails = $this->table1->get_total_emails_by_fid( $this->fundraiser_Id );

        if ( $total_emails == 0 ) {
            return 0;
        } else {
            return number_format( $total_donors / $total_emails, 4 );
        }
    }

    public function participant_score() {
        $donate_participants = $this->table1->get_donor_participants_by_fid( $this->fundraiser_Id );
        $total_participants  = $this->table2->get_total_participants_by_fid( $this->fundraiser_Id );

        if ( $total_participants == 0 ) {
            return 0;
        } else {
            return number_format( $donate_participants / $total_participants, 2 );
        }
    }

    public function participant_2_score() {
        $donate_participants = $this->table1->get_donor_2_participants_by_fid( $this->fundraiser_Id );
        $total_participants  = $this->table2->get_total_participants_by_fid( $this->fundraiser_Id );

        if ( $total_participants == 0 ) {
            return 0;
        } else {
            return number_format( $donate_participants / $total_participants, 2 );
        }
    }

    public function participation_score_formatted() {
        $score = round( $this->participation_score() * 100 );
        $score = ($score > 100) ? 100 : $score;

        return $score;
    }

    public function email_quality_score_formatted() {
        $score = round( $this->email_quality_score() * 100 );
        $score = ($score > 100) ? 100 : $score;

        return $score;
    }

    public function participant_score_formatted() {
        $score = ceil( $this->participant_score() * 100 );
        $score = ($score > 100) ? 100 : $score;

        return $score;
    }

}
