<?php

namespace classes\app\initial_send;

use \classes\Get_User_Info;
use \classes\app\initial_send\Opening_Line;
use \classes\models\mixed\Admins;
use \classes\models\tables\Secondary_Admins;
use \classes\app\fundraiser\Fundraiser_Media;


class Build_Email
{

    private $get_user_info;
    private $from_email;
    private $default_from_name;
    private $thumb_url;
    private $thumbnail_type;

    /**
     * Class constructor.
     */
    public function __construct() {

        load_class( 'get_user_info.class.php' );

        $this->get_user_info = new Get_User_Info();

        $this->from_email        = _TRANSACTIONAL_FROM_EMAIL;
        $this->default_from_name = 'VerticalRaise';
        $this->thumb_url         = '';
        $this->thumbnail_type    = 'fundraiser-logo-small';

    }

    /**
     * Set the participant/amdin/share user email to use.
     * @param int $user_ID
     * @return string The user_email.
     */
    public function set_user_email( $user_ID ) {

        if ( $user_ID != 0 ) {
            $user_info  = get_user_by( 'id', $user_ID );
            $user_email = trim( $user_info->user_email );
        } else {
            $user_email = '';
        }

        return $user_email;
    }

    /**
     * Set the user name to use.
     * @param int $user_ID
     * @return string The user's username.
     */
    public function set_user_name( $user_ID ) {

        if ( $user_ID != 0 ) {
            $user_info = get_user_by( 'id', $user_ID );
            $user_name = trim( $user_info->user_firstname ) . ' ' . trim( $user_info->user_lastname );
        } else {
            $user_name = '';
        }

        return $user_name;
    }

    /**
     * Set the from email -- left as a function in case we ever need to add logic.
     * @return string The from email.
     */
    public function set_from_email() {
        return $this->from_email;
    }

    /**
     * Set the from name based on a user ID or $_POST value.
     * @param  int $user_id The user's id
     * @return string The from name
     */
    public function set_from_name( $user_ID ) {
        $default = $this->default_from_name;
        // If there is a From $_POST
        if ( isset( $_POST['your_name'] ) ) {
            // Validate the name
            $your_name = trim( $_POST['your_name'] );
            $your_name = sanitize_text_field( $your_name );

            return $your_name;
        } else if ( $user_ID != 0 ) {
            // If there is a user id
            $full_name = $this->get_user_info->get_full_name_with_backup( $user_ID );

            if ( !empty( $full_name ) ) {
                return $full_name;
            }
        } else if ( $user_ID == 0 ) {
            return "Vertical Raise";
        }

        return $default;
    }

    /**
     * Get the opening line.
     */
    public function get_opening_line( $fundraiser, $user, $template_type ) {
        $opening_line = new Opening_line();
        return $opening_line->get( $fundraiser, $user, $template_type );
    }

    /**
     * Get author id.
     */
    public function get_author_id( $fundraiser_id ) {
        $admins = new Admins();
        return $admins->get_post_author( $fundraiser_id );
    }

    /**
     * Get secondary admins.
     */
    public function get_s_admins( $fundraiser_id ) {
        $s_admins = new Secondary_Admins();
        return $s_admins->get_sadmin_ids_by_fid( $fundraiser_id );
    }

    /**
     * Get all admins.
     */
    public function get_all_admins( $fundraiser_id ) {
        $admins = new Admins();
        return $admins->get_all_admins( $fundraiser_id );
    }

    /**
     * Get the thumbnail url.
     */
    public function get_thumb_url( $fundraiser_id ) {
        return get_the_post_thumbnail_url( $fundraiser_id, $this->thumbnail_type );
    }
    
    
    /**
     * Set the formatted thumb image.
     */
    public function set_formatted_thumb( $thumb_url ) {
        $image_tag = "<img src='{$thumb_url}' width='160' height='160' style='border-radius: 50%' alt='Fundraiser'>";
        
        return $image_tag;
    }

    /**
     * Get the mail details.
     */
    public function get_mail_details( $to, $from, $from_email, $subject ) {
        $mail_details = [
            'to'       => $to,
            'from'     => "<{$from}> {$from_email}",
            'subject'  => $subject,
            'datetime' => current_time( 'Y-m-d:H:i:s' )
        ];

        return $mail_details;
    }

    /**
     * Set the click url.
     */
    public function set_click_url( $f_id, $user_id ) {
        
        $url = get_permalink( $f_id ) . 'email/' . $user_id;
        
        return $url;
    }

    /**
     * Set the avatar.
     */
    public function set_avatar( $user_id ) {
        return get_avatar( $user_id, 96 );
    }
    
    /**
     * Set the formatted avatar.
     */
    public function set_formatted_avatar( $avatar_file  ) {
        $path = '/p/';
        $site_url = site_url() . $path . $avatar_file;
        $image_tag = "<img src='{$site_url}' width='160' height='160' style='border-radius: 50%' alt='Avatar'>";
        
        return $image_tag;
    }
    
    /**
     * Strip down the avatar to just a string.
     */
    public function set_avatar_file( $avatar ) {
        $matches = array();
        preg_match_all( '/(alt|title|src)=("[^"]*")/i', $avatar, $matches );

        $file_name = trim( $matches[2][0], '"' );
        $file_name = basename( $file_name );
        
        return $file_name;
    }

    /**
     * Set the title.
     */
    public function set_title( $f_id ) {
        return get_the_title( $f_id );
    }

    /**
     * Set uid.
     * @param  int $user_ID
     * @return int $uid
     */
    public function set_uid( $user_ID ) {
        ### TODO: Use our new method of seeing participations ###
        return $user_ID;
    }

    /**
     * fundraiser campaign message: About Fundraiser
     *
     */
    public function set_fundraiser_message( $f_id ) {
        return nl2br( get_post_meta( $f_id, 'campaign_msg', true ) );
    }

    /**
     * get fundraiser year
     */

    public function get_copyright_year(){
        return date('Y');
    }
}
