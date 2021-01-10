<?php

namespace classes\app\emails\typo_trap;

use \classes\app\emails\typo_trap\Load;
use \classes\app\emails\typo_trap\Match;
use \classes\app\emails\typo_trap\Input;

class TypoTrap
{

    // Objects
    private $load;
    private $match;
    private $input;
    // Private Variables
    private $phrases;
    private $exact;
    private $roles;
    private $block_domain;

    public function __construct() {
        $this->load  = new Load();
        $this->match = new Match();
        $this->input = new Input();

        // Load the data on construction
        $this->phrases      = $this->load->load( "phrases" );
        $this->exact        = $this->load->load( "exact" );
        $this->roles        = $this->load->load( "address_roles" );
        $this->block_domain = $this->load->load( "blocking_domains" );
    }

    /**
     * Check to see if the given email contains any of our phrases or exact match strings.
     * @param string $email
     * @return bool
     */
    public function check( $email ) {
        $domain = $this->input->get_domain( $email );
        if ( $this->match->phrase( $domain, $this->phrases ) )
            return true;
        if ( $this->match->exact( $domain, $this->exact ) )
            return true;
        if ( $this->match->block_domain( $domain, $this->block_domain ) )
            return true;


        $full_role = $this->input->get_role_address( $email );
        if ( $this->match->address_role( $full_role, $this->roles ) )
            return true;

        return false;
    }

}
