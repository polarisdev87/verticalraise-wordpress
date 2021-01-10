<?php

namespace classes\app\initial_send;

use \classes\app\initial_send\Build_Email as Build_Email;

class Content
{

    private static $content;

    public static function set_content_fundraiser( $fundraiser_id ) {
        $build_email = new Build_Email();

        self::$content = new \StdClass();

        self::$content->id        = $fundraiser_id;
        self::$content->from      = $build_email->set_from_email();
        self::$content->title     = $build_email->set_title( $fundraiser_id );
        self::$content->subject   = self::$content->title;
        self::$content->author_id = $build_email->get_author_id( $fundraiser_id );
        self::$content->s_admins  = $build_email->get_s_admins( $fundraiser_id );
        self::$content->admins    = $build_email->get_all_admins( $fundraiser_id );
        self::$content->message   = $build_email->set_fundraiser_message( $fundraiser_id );

        return self::$content;
    }

    public static function set_content_user( $fundraiser_id, $user_id ) {
        $build_email = new Build_Email();

        self::$content = new \StdClass();

        self::$content->uid              = $build_email->set_uid( $user_id );
        self::$content->id               = self::$content->uid;
        self::$content->user_name        = $build_email->set_user_name( $user_id ); ### Which User ID to use?
        self::$content->user_email       = $build_email->set_user_email( $user_id ); ### Which User ID to use?
        self::$content->from             = $build_email->set_from_email();
        self::$content->from_name        = $build_email->set_from_name( $user_id ); ### Which User ID to use?
        self::$content->your_name        = $build_email->set_from_name( $user_id ); ### Which User ID to use?
        self::$content->thumb_url        = $build_email->get_thumb_url( $fundraiser_id );
        self::$content->formatted_thumb  = $build_email->set_formatted_thumb( self::$content->thumb_url );
        self::$content->click_url        = $build_email->set_click_url( $fundraiser_id, $user_id ); ### Which User ID to use?
        self::$content->avatar_url       = $build_email->set_avatar( $user_id  ); ### Which User ID to use?
        self::$content->avatar_file      = $build_email->set_avatar_file( self::$content->avatar_url );
        self::$content->formatted_avatar = $build_email->set_formatted_avatar( self::$content->avatar_file );
        self::$content->title            = $build_email->set_title( $fundraiser_id );
        self::$content->subject          = self::$content->title;

        return self::$content;
    }

    public static function set_content_template( $fundraiser, $user, $template_type ) {
        $build_email = new Build_Email();

        self::$content = new \StdClass();

        self::$content->opening_line = $build_email->get_opening_line( $fundraiser, $user, $template_type );
        self::$content->copyright_year = $build_email->get_copyright_year();

        return self::$content;
    }

}
