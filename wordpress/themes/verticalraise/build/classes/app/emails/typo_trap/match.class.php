<?php

namespace classes\app\emails\typo_trap;

class Match
{

    public function phrase( $domain, $phrases ) {
        foreach ( $phrases as $phrase ) {
            if ( strpos( $domain, $phrase ) !== false ) {
                //echo "{$phrase} matched";
                return true;
            }
        }

        //echo "phrase didnt match";

        return false;
    }

    public function exact( $domain, $exacts ) {
        if ( in_array( $domain, $exacts ) )
            return true;

        //echo "exact didnt match";

        return false;
    }

    public function address_role( $address, $roles ) {
        if ( in_array( $address, $roles ) )
            return true;

        return false;
    }

    public function block_domain( $domain, $blocks ) {
        $ch = explode( ".", $domain );
        foreach ( $blocks as $block ) {
            if ( strpos( $ch[1], $block ) !== false ) {
                //echo "{$phrase} matched";
                return true;
            }
        }

        return false;
    }

}
