<?php

namespace classes\app\crons\potential_donors;

use \classes\app\crons\potential_donors\Config;
use \classes\app\crons\potential_donors\Auth;
use \classes\app\crons\potential_donors\Process;

class Potential_Donors
{

    public function run($period) {
        
        $this->auth->run($period);
        $this->log->run($period);
        $this->process->run($period);
    }
    


}