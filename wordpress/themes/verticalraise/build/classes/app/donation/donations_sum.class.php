<?php

namespace classes\app\donation;

use classes\models\tables\Donations_Total;

class Donations_Sum
{

    private $table;

    public function __construct() {
        $this->table = new Donations_Total();
    }

    public function increment_total( $fundraiser_id, $value ) {
        $fundraiser_id = (int) trim($fundraiser_id);
        $value         = (int) trim($value);

        $myrow = $this->table->get_single_sums_row($fundraiser_id);

        if ( !empty($myrow) ) {

            $this->table->update_donations_sum($myrow, $value); // Update the row
        } else {

            $this->table->insert_donations_sum($fundraiser_id, $value); // Insert new row
        }
    }    

}
