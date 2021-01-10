<?php

namespace classes\pages;

use \classes\app\user\Update_User;

class Edit_Profile_Page
{

    public function __construct( $user_ID ) {
        $this->update_user = new Update_User( $user_ID );
    }

    /**
     * Handle edit profile form submit.
     * @param type $post
     */
    public function handle_form_submit( $post ) {
        if ( isset($post['register']) ) {
            $result = $this->update_user->process_update($post);

            if ( $result['status'] ) {
                echo '<div class="successMsg">' . $result['message'] . '</div>';
//                header('Location: ' . get_bloginfo('home') . '/my-account/');
            } else {
                echo '<div class="errorMsg">' . $result['message'] . '</div>';
            }
        }
    }

}
