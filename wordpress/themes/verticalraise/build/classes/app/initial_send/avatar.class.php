<?php

namespace classes\app\initial_send;

use \classes\app\initial_send\Build_Email as Build_Email;

class Avatar
{

    /**
     * Class constructor
     */
    public function __construct() {
        $this->default_avatar = "user-avatar-96x96.png";
    }
    
    /**
     * Get the avatar to use.
     *
     * @param object $user The User object.
     * @param int $type The type
     *
     * @return string The image tag
     */
    public function get_avatar($user, $fundraiser, $type) {
        //type 3 admin invite wizard
        //type 2 participant invite wizard
        //type 1 email share

        if ( $type == 1 ) {
            if ( $user->id == 0 ) {
                if ( $user->thumb_url == false ) {
                    $url = get_template_directory_uri() . '/assets/images/default-logo.png';
                    return "<img src=\"$url\" width=\"160\" height=\"160\" alt=\"Generic Fundraiser Image\"/>";
                } else {
                    return $user->formatted_thumb;
                }
            } else {
                if ( $user->avatar_file == $this->default_avatar ) {
                    $url = get_template_directory_uri() . '/assets/images/user-avatar.png';
                    return "<img src=\"$url\" width=\"160\" height=\"160\" style=\"border-radius: 50%;background: black;\" alt=\"Generic User Avatar\"/>";
                } else {
                    return $user->formatted_avatar;
                }
            }
        } else {
            if ( $user->avatar_file == $this->default_avatar ) {
                $url = get_template_directory_uri() . '/assets/images/user-avatar.png';
                return "<img src=\"$url\" width=\"160\" height=\"160\" style=\"border-radius: 50%;background: black;\" alt=\"Generic User Avatar\"/>";
            } else {
                return $user->formatted_avatar;
            }
        }


    }
    
}

