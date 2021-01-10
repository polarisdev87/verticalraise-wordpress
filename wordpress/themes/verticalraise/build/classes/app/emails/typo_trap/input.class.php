<?php

namespace classes\app\emails\typo_trap;

class Input
{

    public function get_domain( $email ) {
        $domain = $this->extract_domain( $email );

        return $this->format( $domain );
    }

    private function extract_domain( $email ) {
        $pieces = explode( "@", $email );
        $domain = $pieces[1];

        return $domain;
    }

    public function get_role_address( $email ) {
        $address = $this->extract_role_address( $email );
        return $this->format( $address );
    }

    private function extract_role_address( $email ) {
        $pieces = explode( "@", $email );
        $addr   = $pieces[0];

        return $addr;
    }

    private function format( $string ) {
        return strtolower( $string );
    }

}
