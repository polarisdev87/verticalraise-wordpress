<?php
/**
 * Contact Form 7 Filters
 */

 namespace classes\app\filters;

 class Contact_form_7 {

    public static function validate_tel( $result, $tag ){

        if ( isset( $_POST['PhoneNumber'] ) ) {
            $phone = trim( $_POST['PhoneNumber'] );
        } else if ( isset( $_POST['phone-bf'] ) ) {
            $phone = trim( $_POST['phone-bf'] );
        } else {
            return $result;
        }
    
        if ( strlen($phone) === 7 ) {
            $result->invalidate( $tag, "Please include area code" );
        } else if ( strlen($phone) < 10 ) {
            $result->invalidate( $tag, "The phone length should be 10 digits" );
        }
        return $result;

    }

 }

 