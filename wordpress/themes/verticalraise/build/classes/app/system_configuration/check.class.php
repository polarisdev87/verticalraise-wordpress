<?php

namespace classes\app\systems_configuration;

class Check
{

    public function check( $input, $expected, $operator ) {
        switch ( $operator ) {
            case ">":
                if ( $input > $expected ) {
                    return true;
                }
                break;
            case "<":
                if ( $input < $expected ) {
                    return true;
                }
                break;
            case "=":
                if ( $input == $expected ) {
                    return true;
                }
                break;
            case ">=":
                if ( $input >= $expected ) {
                    return true;
                }
                break;
            case "<=":
                if ( $input <= $expcted ) {
                    return true;
                }
                break;
        }

        return false;
    }

}
