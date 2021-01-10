<?php

namespace classes;

/**
 * Get User Info
 * @description Get specific data about or entered by the user.
 */
class Get_User_Info {

    /**
     * Get the user's full name based on what they have entered in their profile.
     * @param  int   $user_ID
     * @return mixed Either the user's full name or false if it does not exist.
     */
    public function get_full_name($user_ID) {
        $user_info = get_userdata($user_ID);

        if ( !empty($user_info) ) {

            // Check for a First Name & Last Name
            $first_name = trim($user_info->user_firstname);
            $last_name  = trim($user_info->user_lastname);

            // Build the full name
            $full_name = '';
            if ( !empty($first_name) ) {
                $full_name .= $first_name;
            }
            if ( !empty($last_name) ) {
                $full_name .= ' ' . $last_name;
            }

            // A full name exists
            if ( !empty($full_name) ) {
                $full_name = sanitize_text_field($full_name);
                return $full_name;
            }
        }
        return false;
    }

    /**
     * Get the user's full name and if it does not exist return a static backup name.
     * @param  int    $user_ID The user's id.
     * @return string Either the user's full name or a static backup.
     */
    public function get_full_name_with_backup($user_ID) {
        $default   = 'VerticalRaise';
        $full_name = $this->get_full_name($user_ID);
        if ( !empty($full_name) ) {
            return $full_name;
        } else {
            return $default;
        }
    }

}
