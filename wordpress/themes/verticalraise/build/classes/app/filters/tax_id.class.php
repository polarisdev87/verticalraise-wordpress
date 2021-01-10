<?php 
/**
 * Wordpress filter to format tax id
 */
namespace classes\app\filters;

class Tax_ID{

    public static function format( $tax_id ) {
        
        $formatted_tax_id = '';

        if ( $tax_id ) {
            $formatted_tax_id = 'Tax ID: ' . substr( $tax_id , 0 , 2 ) . "-" . substr( $tax_id , 2 );
        }

        return $formatted_tax_id;
    }
    
}