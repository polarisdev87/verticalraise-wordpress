<?php

/**
 * Set user role
 */
use \classes\app\site_admin\Site_Admin;

// Change user role in admin
add_action('set_user_role', function( $user_id, $role, $old_roles ) {
    
    $site_admin = new Site_Admin();
    // add administrator role to user
    $admin_role = 'administrator';

    if ( $role == $admin_role ) {
        $site_admin->add_admin_role($user_id);
    }

    if ( in_array($admin_role, $old_roles) && $role != $admin_role ) {
        $site_admin->remove_admin_role($user_id);
    }
}, 10, 3);

// Add user in admin
//** It will hook in set_user_role.

// Remove user in admin
function admin_delete_user( $user_id ) {
    $site_admin = new Site_Admin();
    $site_admin->remove_admin_role($user_id);
}

add_action('delete_user', 'admin_delete_user');
