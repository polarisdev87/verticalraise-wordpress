<?php

namespace classes\app\fundraiser;

class Fundraiser_emails
{

    public static function get_emails_for( $fid ) {

        $file = Fundraiser_emails::get_folder() . $fid . ".txt";
        if ( file_exists( $file ) ) {
            $data = file_get_contents( $file );
            $email_list = explode( PHP_EOL, $data );
            return $email_list;
        } else {
            return [];
        }

    }

    public static function get_folder() {
        $cache_dir = get_cache_dir();
        $final_dir = $cache_dir . "/invite_wizard_emails/";
            if ( !is_dir( $final_dir ) ) {
                if ( !mkdir( $final_dir, 755, true ) ) {
                    throw new \Exception( "Failed to create invite_wizard_emails folder in upload" );
                }
            }
        return $final_dir;
    }

    public static function store_emails_for( $fid, $new_emails ) {

        $file = Fundraiser_emails::get_folder() . $fid . ".txt";
        $email_list = [];
        if ( file_exists( $file ) ) {
            $data = file_get_contents( $file );
            $email_list = explode( "\n", $data );
        }
        $email_list = array_merge( $email_list, $new_emails );
        $email_list = array_unique( $email_list );
        $emails = implode( "\n", $email_list );
        if ( !file_put_contents( $file, $emails ) ) {
            throw new \Exception( "Error storing emails in file for $fid" );
        };

    }
}