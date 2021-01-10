<?php

/**
 * Theme rewrites
 */
function wefund4u_rewrite() {

    /**
     * Rewrite rules need to be prioritized by the longest possible case first, to exact match it, and then gradually work our way down the possible shorter cases, by length.
     */
    /**
     * --- Fundraiser rewrite rules. ---
     */
    // Full correctly formatted url /name/media/uid/email
    add_rewrite_rule('^fundraiser/([^/]*)/([^/]+)/([^/]+)/([^/]+)/?$', 'index.php?post_type=fundraiser&name=$matches[1]&media=$matches[2]&uid=$matches[3]&semail=$matches[4]', 'top');

    // Url without email: /name/media/uid
    add_rewrite_rule('^fundraiser/([^/]*)/([^/]+)/([^/]+)/?$', 'index.php?post_type=fundraiser&name=$matches[1]&media=$matches[2]&uid=$matches[3]', 'top');

    // Url without user id: /name/media
    add_rewrite_rule('^fundraiser/([^/]*)/([^/]+)/?$', 'index.php?post_type=fundraiser&name=$matches[1]&media=$matches[2]', 'top');

    // Url without media: /name/
    add_rewrite_rule('^fundraiser/([^/]*)/?$', 'index.php?post_type=fundraiser&name=$matches[1]', 'top');

    /**
     * --- Catchall rewrite rules. ---
     */
    // Full correctly formatted url /name/media/uid/email
    add_rewrite_rule('^([^/]*)/([^/]+)/([^/]+)/([^/]+)/?$', 'index.php?post_type=fundraiser&name=$matches[1]&media=$matches[2]&uid=$matches[3]&semail=$matches[4]', 'top');

    // Url without email: /name/media/uid
    add_rewrite_rule('^([^/]*)/([^/]+)/([^/]+)/?$', 'index.php?post_type=fundraiser&name=$matches[1]&media=$matches[2]&uid=$matches[3]', 'top');
}

add_filter('init', 'wefund4u_rewrite', 10, 1);


// add rewrite rule for profile image url
add_action('generate_rewrite_rules', 'nowp_add_rewrites');

function nowp_add_rewrites($content) {

    global $wp_rewrite;
    $nowp_new_non_wp_rules = array(
        'p/(.*)' => '/wp-content/uploads/profile_img_thumb/$1',
        'logo/(.*)' => '/wp-content/uploads/teamlogo_img/$1'
    );
    $wp_rewrite->non_wp_rules = array_merge($wp_rewrite->non_wp_rules, $nowp_new_non_wp_rules);
    return $content;
}

function nowp_clean_urls($content) {
    if (strpos($content, '/wp-content/uploads/profile_img_thumb') > 0) {
        return str_replace('/wp-content/uploads/profile_img_thumb', '/p', $content);
    } else if (strpos($content, '/wp-content/uploads/teamlogo_img') > 0) {
        return str_replace('/wp-content/uploads/teamlogo_img', '/logo', $content);
    } else {
        return $content;
    }
}

add_action('generate_rewrite_rules', 'nowp_add_rewrites');
if (!is_admin()) {
    $tags = array(
        'wp_get_attachment_url',
    );
    foreach ($tags as $filter) {
        add_filter($filter, 'nowp_clean_urls');
    }
}

