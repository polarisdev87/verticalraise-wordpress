<?php

namespace classes\models\mixed;

class Signup_User
{

    private $post_data;

    /**
     * Update User Meta
     * @param type $data
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function register_user_meta( $data ) {
        $this->post_data = $data;

        $new_user_id = wp_insert_user(
                array (
                    'user_login'      => $this->post_data['email'],
                    'user_pass'       => $this->post_data['password'],
                    'user_email'      => $this->post_data['email'],
                    'first_name'      => $this->post_data['first_name'],
                    'last_name'       => $this->post_data['last_name'],
                    'display_name'    => $this->post_data['first_name'] . " " . $this->post_data['last_name'],
                    'role'            => 'member',
                    'user_nicename'   => $this->post_data['email'],
                    'nickname'        => $this->post_data['email'],
                    'user_registered' => date('Y-m-d H:i:s')
                )
        );
        return $new_user_id;
    }

    public function get_active_key( $email ) {

        $key = $this->wpdb->get_var(
                $this->wpdb->prepare("SELECT user_activation_key FROM " . $this->wpdb->users . " WHERE user_email = %s", $email));
        return $key;
    }

    public function add_active_key( $email ) {
        $key = wp_generate_password(20, false);

        $this->wpdb->update($this->wpdb->users, array (
            'user_activation_key' => $key), array ('user_login' => $email)
        );
        return $key;
    }

    public function activation_key( $data ) {
        $email = $data['email'];
        $key   = $this->get_active_key($email);
        if ( empty($key) ) {
            $key = $this->add_active_key($email);
        }

        $user_status = 2;

        $this->update_active_key($user_status, $key);
    }

    public function update_active_key( $user_status, $key ) {
        $this->wpdb->update($this->wpdb->users, array ('user_status' => $user_status), array ('user_activation_key' => $key));
    }

}
