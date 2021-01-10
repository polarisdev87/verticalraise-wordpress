<?php

namespace classes\app\System_configuration;

class Constants
{

    public function __construct() {
        
    }

    private function value_wrapper( $check, $value ) {

        if ( $check ) {
            $class = "expected";
        } else {
            $class = "unexpected";
        }
        return '<span class="checkmark ' . $class . '" style="font-weight:900">' . ((constant( $value ) != '') ? '&#x2713;' : '') . ' </span> <span class="' . $class . '" >' . $value . '</span>';
    }

    public function system_config( $const ) {
        return $this->value_wrapper( defined( $const ) ? true : false, $const );
    }

}
