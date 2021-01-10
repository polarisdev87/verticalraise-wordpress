<?php

namespace classes\app\sms;

class SMS_Utils
{

    public function format_number( $number ) {
        $number = str_replace( '-', '', $number );
        $number = str_replace( '/', '', $number );
        $number = str_replace( '.', '', $number );
        $number = str_replace( '(', '', $number );
        $number = str_replace( ')', '', $number );
        $number = str_replace( '+', '', $number );
        $number = str_replace( ' ', '', $number );
        $number = str_replace( '_', '', $number );
        $number = trim( $number );

        return $number;
    }

}
