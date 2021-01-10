<?php

namespace classes\app\donation;

use classes\models\tables\Donations;

class Donations_Cron
{

    private $table;

    public function __construct() {
        $this->table = new Donations();
    }

    public function run() {
        $delete = $this->table->delete_donations();        
    }

}
