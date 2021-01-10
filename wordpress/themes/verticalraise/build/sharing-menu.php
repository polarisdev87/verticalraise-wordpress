<?php

/**
 * Determine which menu to use.
 */
if ( isset($_GET['parent']) && $_GET['parent'] == 1 ) {
    wp_nav_menu(array('theme_location' => 'invitemenuparent'));
} elseif ( isset($_GET['display_type']) && $_GET['display_type'] == 'single' ) {
    // Invite Wizard
    wp_nav_menu(array('theme_location' => 'invitemenusingle'));
} else {
    // Spread the word?
    wp_nav_menu(array('theme_location' => 'invitemenu'));
}

