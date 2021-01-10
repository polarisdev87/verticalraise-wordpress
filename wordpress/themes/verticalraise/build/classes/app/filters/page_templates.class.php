<?php
/**
 * Page Templates util class
 */

namespace classes\app\filters;

class Page_Templates {

    public static function add_subdir( $templates = array() ){
        // Generally this doesn't happen, unless another plugin / theme does modifications
        // of their own. In that case, it's better not to mess with it again with our code.
        if( empty( $templates ) || ! is_array( $templates ) || count( $templates ) < 3 )
            return $templates;

        $page_tpl_idx = 0;
        if( $templates[0] === get_page_template_slug() ) {
            // if there is custom template, then our page-{slug}.php template is at the next index
            $page_tpl_idx = 1;
        }

        $page_tpls = array( WPSE_PAGE_TEMPLATE_SUB_DIR . '/' . $templates[$page_tpl_idx] );

        // As of WordPress 4.7, the URL decoded page-{$slug}.php template file is included in the
        // page template hierarchy just before the URL encoded page-{$slug}.php template file.
        // Also, WordPress always keeps the page id different from page slug. So page-{slug}.php will
        // always be different from page-{id}.php, even if you try to input the {id} as {slug}.
        // So this check will work for WordPress versions prior to 4.7 as well.
        if( $templates[$page_tpl_idx] === urldecode( $templates[$page_tpl_idx + 1] ) ) {
            $page_tpls[] = WPSE_PAGE_TEMPLATE_SUB_DIR . '/' . $templates[$page_tpl_idx + 1];
        }

        array_splice( $templates, $page_tpl_idx, 0, $page_tpls );

        return $templates;
    }
}

