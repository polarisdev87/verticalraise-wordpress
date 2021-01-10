<?php

namespace classes\app\donation;

use classes\models\tables\Donations_Count as Donations_Total;

class Donations_Count
{

    private $table;

    public function __construct() {
        $this->table = new Donations_Total();
    }

    public function increment_total( $fundraiser_id, $value ) {
        $fundraiser_id = (int) trim($fundraiser_id);
        $value         = (int) trim($value);

        if ( $this->table->table_exist() ) {

            $myrow = $this->table->get_single_count_row($fundraiser_id);

            if ( !empty($myrow) ) {

                $this->table->update_donations_count($myrow, $value); // Update the row
            } else {

                $this->table->insert_donations_count($fundraiser_id, $value); // Insert new row
            }

        }

    }

}
