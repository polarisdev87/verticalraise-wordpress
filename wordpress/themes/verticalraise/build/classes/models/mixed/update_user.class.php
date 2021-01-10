<?php

namespace classes\models\mixed;

class User
{
        
    private $update_data;  
    
    /**
     * Update User Meta
     * @param type $data
     */
    public function update_user_meta($data) {       
        $this->update_data = $data;
//        $users = get_user_by('email', $this->update_data['email']);
//        var_dump($users);
        
        if ( !empty( $this->update_data['pw1'] ) ) {
            $result = wp_update_user(
                array(
                    'ID'             => $this->update_data['participant'],
                    'user_login'     => $this->update_data['email'],
                    'user_pass'      => $this->update_data['pw1'],
                    'user_email'     => $this->update_data['email'],
                    'first_name'     => $this->update_data['first_name'],
                    'last_name'      => $this->update_data['last_name'],
                    'user_nicename'  => $this->update_data['email'],
                    'nickname'       => $this->update_data['email'],
                    'display_name'   => $this->update_data['first_name'] . " " . $data['last_name']
                )
            );
        } else {
            $result = wp_update_user(
                array(
                    'ID'             => $this->update_data['participant'],
                    'user_login'     => $this->update_data['email'],
                    'user_email'     => $this->update_data['email'],
                    'first_name'     => $this->update_data['first_name'],
                    'last_name'      => $this->update_data['last_name'],
                    'user_nicename'  => $this->update_data['email'],
                    'nickname'       => $this->update_data['email'],
                    'display_name'   => $this->update_data['first_name'] . " " . $data['last_name']
                )
            );
        }
        return $result;
        
    } 
    
}