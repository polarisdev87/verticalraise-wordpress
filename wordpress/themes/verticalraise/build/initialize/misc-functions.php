<?php

function add_query_vars_wefund4u($aVars) {
    $aVars[] = "name";
    $aVars[] = "media";
    $aVars[] = "uid";
    $aVars[] = "semail";

    return $aVars;
}
add_filter('query_vars', 'add_query_vars_wefund4u');

add_role('Campaign Admin', 'campaign_admin');
add_role('Secondary Admin', 'secondary_admin');
add_role('Participant', 'participant');

add_theme_support( 'post-thumbnails' );
add_image_size( 'homepage-thumb', 288, 151, true );

function content($limit, $postid) {
    $post = get_page($postid);
    $fullContent = $post->post_content;
    $content = explode(' ', $fullContent, $limit);
    if ( count($content) >= $limit ) {
        array_pop($content);
        $content = implode(" ",$content).'...';
    } else {
        $content = implode(" ",$content);
    }
    $content = preg_replace('/\[.+\]/','', $content);
    $content = str_replace(']]>', ']]&gt;', $content);
    return $content;
}

function encripted($data) {
    $key1 = '644CBEF595BC9';
    $final_data = $key1.'|'.$data;
    $val = base64_encode(base64_encode(base64_encode($final_data)));
    return $val;
}

function decripted($data) {
    $val = base64_decode(base64_decode(base64_decode($data)));
    $final_data = explode('|', $val);
    return $final_data[1];
}

if ( !current_user_can('administrator') ):
    show_admin_bar(false);
endif;

function the_excerpt_max_charlength($charlength) {
    $excerpt = get_the_excerpt();
    $charlength++;
    
    if ( mb_strlen( $excerpt ) > $charlength ) {
        $subex = mb_substr( $excerpt, 0, $charlength - 5 );
        $exwords = explode( ' ', $subex );
        $excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
        if ( $excut < 0 ) {
            echo mb_substr( $subex, 0, $excut );
        } else {
            echo $subex;
        }
        echo '[...]';
    } else {
        echo $excerpt;
    }
}

function the_excerpt_max_charlength_by_content($charlength, $content) {
    $excerpt = $content;
    $charlength++;
    
    if ( mb_strlen( $excerpt ) > $charlength ) {
        $subex = mb_substr( $excerpt, 0, $charlength - 5 );
        $exwords = explode( ' ', $subex );
        $excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
        
        if ( $excut < 0 ) {
            echo mb_substr( $subex, 0, $excut );
        } else {
            echo $subex;
        }
        echo '[...]';
    } else {
        echo $excerpt;
    }
}

function get_user_role($userid){
    $user_info = get_userdata($userid);
    $role = implode(', ', $user_info->roles);
    
    return $role;
}

function generateRandomString($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    
    for ( $i = 0; $i < $length; $i++ ) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    
    return $randomString;
}

function in_array_r($needle, $haystack, $strict = false) {
    if ( !empty($haystack) ) {
        foreach ( $haystack as $item ) {
            if ( ($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict)) ) {
                return true;
            }
        }
    }

    return false;
}

function in_array_my($needle, $haystack) {
    if ( !empty($haystack) ) {
        return in_array($needle, $haystack);
    }
}

function aasort (&$array, $key) {
    $sorter = array();
    $ret = array();
    reset($array);
    
    foreach ( $array as $ii => $va ) {
        $sorter[$ii]=$va[$key];
    }
    asort($sorter);
    
    foreach ( $sorter as $ii => $va ) {
        $ret[$ii]=$array[$ii];
    }
    
    $array = $ret;
}

/**
 * Returns selected currency for campaign
 * @param int f_id Fundraiser id
 * @return string Currency code
 */
function getCurrency($f_id) {
    if ( empty($f_id) ) return false; // require f_id
    
    $currency_selection = get_post_meta($f_id, 'currency_selection', true);
    if ( $currency_selection == 'CAD' ) {
        return 'cad';
    } else {
        return 'usd';
    }
}

function currencyConverter($currency_from, $currency_to, $currency_input){
    $yql_base_url = "http://query.yahooapis.com/v1/public/yql";
    $yql_query = 'select * from yahoo.finance.xchange where pair in ("'.$currency_from.$currency_to.'")';
    $yql_query_url = $yql_base_url . "?q=" . urlencode($yql_query);
    $yql_query_url .= "&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys";
    
    $yql_session = curl_init($yql_query_url);
    curl_setopt($yql_session, CURLOPT_RETURNTRANSFER,true);
    $yqlexec = curl_exec($yql_session);
    $yql_json =  json_decode($yqlexec,true);
    $currency_output = (float) $currency_input*$yql_json['query']['results']['rate']['Rate'];
    
    return $currency_output;
}

function getCurrentURL() {
    $currentURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
//    $currentURL .= $_SERVER["SERVER_NAME"];
    $currentURL .= $_SERVER["HTTP_HOST"];


    if ( $_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443" ) {
        $currentURL .= ":".$_SERVER["SERVER_PORT"];
    }

    $currentURL .= $_SERVER["REQUEST_URI"];
    
    return $currentURL;
}

function processURL($url) {
    //echo 'curl exists: ' . function_exists('curl_version');
    
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 2
    ));

    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}

/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own twentytwelve_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Twelve 1.0
 */
function custom_comment( $comment, $args, $depth ) {
    $GLOBALS['comment'] = $comment;
    switch ( $comment->comment_type ) :
        case 'pingback' :
        case 'trackback' :
            // Display trackbacks differently than normal comments.
            ?>
            <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
            <p><?php _e( 'Pingback:' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)' ), '<span class="edit-link">', '</span>' ); ?></p>
            <?php
            break;
        default :
            // Proceed with normal comments.
            global $post;
            ?>
        <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
            <article id="comment-<?php comment_ID(); ?>" class="comment">
                <div class="comment-meta comment-author vcard">
                    <?php
                    //echo get_avatar( $comment, 44 );
                    ?>
                    <div class="comment-text">
                        <p class="meta">
                            <?php
                            printf( '<cite><b class="fn">%1$s</b> %2$s</cite>',
                                get_comment_author_link(),
                                // If current post author is also comment author, make it known visually.
                                ( $comment->user_id === $post->post_author ) ? '<span>' . __( ' - ' ) . '</span>' : ''
                            );
                            ?>
                            <?php if ( '0' == $comment->comment_approved ) : ?>
                                <?php _e( 'Your comment is awaiting moderation.' ); ?>
                            <?php endif; ?>
                            <section class="comment-content comment">
                                <?php comment_text(); ?>
                                <?php edit_comment_link( __( 'Edit' ), '<strong class="edit-link">', '</strong>' ); ?>
                            </section><!-- .comment-content -->
                            <?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply' ), 'after' => ' <span></span>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
                            <?php
                            printf( '<time datetime="%2$s">%3$s</time>',
                                esc_url( get_comment_link( $comment->comment_ID ) ),
                                get_comment_time( 'c' ),
                                /* translators: 1: date, 2: time */
                                sprintf( __( '%1$s at %2$s' ), get_comment_date(), get_comment_time() )
                            );
                            ?>
                        <div style="clear: both;"></div>
                        </p>
                    </div>
                </div><!-- .comment-meta -->
            </article><!-- #comment-## -->
            <?php
            break;
    endswitch; // end comment_type check
}

function ribbon_func( $atts ) {
    $text = '<div class="ribbon">'.$atts['text'].'</div>';
    return $text;
}
add_shortcode( 'ribbon', 'ribbon_func' );

class PseudoCrypt {

    /* Key: Next prime greater than 62 ^ n / 1.618033988749894848 */
    /* Value: modular multiplicative inverse */
    private static $golden_primes = array(
        '1'                  => '1',
        '41'                 => '59',
        '2377'               => '1677',
        '147299'             => '187507',
        '9132313'            => '5952585',
        '566201239'          => '643566407',
        '35104476161'        => '22071637057',
        '2176477521929'      => '294289236153',
        '134941606358731'    => '88879354792675',
        '8366379594239857'   => '7275288500431249',
        '518715534842869223' => '280042546585394647'
    );

    /* Ascii :                    0  9,         A  Z,         a  z     */
    /* $chars = array_merge(range(48,57), range(65,90), range(97,122)) */
    private static $chars62 = array(
        0=>48,1=>49,2=>50,3=>51,4=>52,5=>53,6=>54,7=>55,8=>56,9=>57,10=>65,
        11=>66,12=>67,13=>68,14=>69,15=>70,16=>71,17=>72,18=>73,19=>74,20=>75,
        21=>76,22=>77,23=>78,24=>79,25=>80,26=>81,27=>82,28=>83,29=>84,30=>85,
        31=>86,32=>87,33=>88,34=>89,35=>90,36=>97,37=>98,38=>99,39=>100,40=>101,
        41=>102,42=>103,43=>104,44=>105,45=>106,46=>107,47=>108,48=>109,49=>110,
        50=>111,51=>112,52=>113,53=>114,54=>115,55=>116,56=>117,57=>118,58=>119,
        59=>120,60=>121,61=>122
    );

    public static function base62($int) {
        $key = "";
        while(bccomp($int, 0) > 0) {
            $mod = bcmod($int, 62);
            $key .= chr(self::$chars62[$mod]);
            $int = bcdiv($int, 62);
        }
        return strrev($key);
    }

    public static function hash($num, $len = 5) {
        $ceil = bcpow(62, $len);
        $primes = array_keys(self::$golden_primes);
        $prime = $primes[$len];
        $dec = bcmod(bcmul($num, $prime), $ceil);
        $hash = self::base62($dec);
        return str_pad($hash, $len, "0", STR_PAD_LEFT);
    }

    public static function unbase62($key) {
        $int = 0;
        foreach(str_split(strrev($key)) as $i => $char) {
            $dec = array_search(ord($char), self::$chars62);
            $int = bcadd(bcmul($dec, bcpow(62, $i)), $int);
        }
        return $int;
    }

    public static function unhash($hash) {
        $len = strlen($hash);
        $ceil = bcpow(62, $len);
        $mmiprimes = array_values(self::$golden_primes);
        $mmi = $mmiprimes[$len];
        $num = self::unbase62($hash);
        $dec = bcmod(bcmul($num, $mmi), $ceil);
        return $dec;
    }

}

add_action('admin_menu', 'add_report_page');

function add_report_page() {
    add_submenu_page('edit.php?post_type=fundraiser', 'Report', 'Report', 'manage_options', 'report', 'report_page');
    add_submenu_page('', 'Report', 'Report', 'manage_options', 'single-report', 'single_report_page');
    add_submenu_page('edit.php?post_type=fundraiser', 'Participant Instructions', 'Participant Instructions', 'manage_options', 'participant_instructions', 'participant_instructions_page');
    add_submenu_page('edit.php?post_type=fundraiser', 'Flyers', 'Flyers', 'manage_options', 'flyers', 'flyers_page');
}

function report_page() {
    require_once('admin-menu-reports-page.php');
}

function single_report_page() {
    require_once('admin-menu-single-reports-page.php');
}

function participant_instructions_page() {
    require_once('admin-menu-participant_instructions-page.php');
}

function flyers_page() {
    require_once('admin-menu-flyers-page.php');
}


/* This is for Supporter */
add_action('init', 'supporter_register_function');
function supporter_register_function(){
    $labels = array(
        'name' => _x('Supporters', 'post type general name'),
        'singular_name' => _x('Supporter', 'post type singular name'),
        'add_new' => _x('Add New', 'Supporter item'),
        'add_new_item' => __('Add New Supporter'),
        'edit_item' => __('Edit Supporter Item'),
        'new_item' => __('New Supporter Item'),
        'view_item' => __('View Supporter Item'),
        'search_items' => __('Search Supporter'),
        'not_found' =>  __('Nothing found'),
        'not_found_in_trash' => __('Nothing found in Trash'),
        'parent_item_colon' => ''
    );
    $args = array(
        'labels' => $labels,
        'supporterlic' => true,
        'supporterlicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'public' => true,
        'menu_icon' => 'dashicons-lightbulb',
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'supports' => array('title', 'editor')
    );
    register_post_type( 'supporter' , $args );
}

function supporter_remove_menu_items() {
    remove_menu_page( 'edit.php?post_type=supporter' );
}

add_action( 'admin_menu', 'supporter_remove_menu_items' );

/* This is for Invite */
add_action('init', 'invite_register_function');
function invite_register_function(){
    $labels = array(
        'name' => _x('Invite', 'post type general name'),
        'singular_name' => _x('invite', 'post type singular name'),
        'add_new' => _x('Add New', 'Invite item'),
        'add_new_item' => __('Add New invite'),
        'edit_item' => __('Edit invite Item'),
        'new_item' => __('New invite Item'),
        'view_item' => __('View invite Item'),
        'search_items' => __('Search Invite'),
        'not_found' =>  __('Nothing found'),
        'not_found_in_trash' => __('Nothing found in Trash'),
        'parent_item_colon' => ''
    );
    $args = array(
        'labels' => $labels,
        'invitelic' => true,
        'invitelicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'public' => true,
        'menu_icon' => 'dashicons-groups',
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'supports' => array('title')
    );
    register_post_type( 'invite' , $args );
}

function invite_remove_menu_items() {
    remove_menu_page( 'edit.php?post_type=invite' );
}

add_action( 'admin_menu', 'invite_remove_menu_items' );



//require TEMPLATEPATH . '/html2text-master/vendor/autoload.php';

function convertHTML( $content ) {
    return Html2Text\Html2Text::convert( $content );
}

function delete_all_between($beginning, $end, $string) {
    $beginningPos = strpos($string, $beginning);
    $endPos = strpos($string, $end);
    if ($beginningPos === false || $endPos === false) {
        return $string;
    }

    $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

    return str_replace($textToDelete, '', $string);
}



function is_mobile_new() {
    $useragent=$_SERVER['HTTP_USER_AGENT'];
    if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
        return true;
    } else {
        return false;
    }
}

function isIphone($user_agent=NULL) {
    if ( !isset($user_agent) ) {
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }
    
    return ( strpos($user_agent, 'iPhone') !== FALSE );
}

/* This is for Testimonial */
function testimonial_register_function() {
    $labels = array(
        'name' => _x('Testimonials', 'post type general name'),
        'singular_name' => _x('Testimonial', 'post type singular name'),
        'add_new' => _x('Add New', 'Testimonial item'),
        'add_new_item' => __('Add New Testimonial'),
        'edit_item' => __('Edit Testimonial Item'),
        'new_item' => __('New Testimonial Item'),
        'view_item' => __('View Testimonial Item'),
        'search_items' => __('Search Testimonial'),
        'not_found' =>  __('Nothing found'),
        'not_found_in_trash' => __('Nothing found in Trash'),
        'parent_item_colon' => ''
    );
    
    $args = array(
        'labels' => $labels,
        'testimoniallic' => true,
        'testimoniallicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'public' => false,
        'menu_icon' => 'dashicons-testimonial',
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'supports' => array('title', 'editor', 'thumbnail')
    );
    
    register_post_type( 'testimonial' , $args );
}
add_action('init', 'testimonial_register_function');

function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
    $output = NULL;
    if ( filter_var($ip, FILTER_VALIDATE_IP) === FALSE ) {
        $ip = $_SERVER["REMOTE_ADDR"];
        if ( $deep_detect ) {
            if ( filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP) )
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if ( filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP) )
                $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
    }
    $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
    $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array(
        "AF" => "Africa",
        "AN" => "Antarctica",
        "AS" => "Asia",
        "EU" => "Europe",
        "OC" => "Australia (Oceania)",
        "NA" => "North America",
        "SA" => "South America"
    );
    if ( filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support) ) {
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
        
        if ( @strlen(trim($ipdat->geoplugin_countryCode)) == 2 ) {
            switch ($purpose) {
                case "location":
                    $output = array(
                        "city"           => @$ipdat->geoplugin_city,
                        "state"          => @$ipdat->geoplugin_regionName,
                        "country"        => @$ipdat->geoplugin_countryName,
                        "country_code"   => @$ipdat->geoplugin_countryCode,
                        "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                        "continent_code" => @$ipdat->geoplugin_continentCode
                    );
                    break;
                case "address":
                    $address = array($ipdat->geoplugin_countryName);
                    if (@strlen($ipdat->geoplugin_regionName) >= 1)
                        $address[] = $ipdat->geoplugin_regionName;
                    if (@strlen($ipdat->geoplugin_city) >= 1)
                        $address[] = $ipdat->geoplugin_city;
                    $output = implode(", ", array_reverse($address));
                    break;
                case "city":
                    $output = @$ipdat->geoplugin_city;
                    break;
                case "state":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "region":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "country":
                    $output = @$ipdat->geoplugin_countryName;
                    break;
                case "countrycode":
                    $output = @$ipdat->geoplugin_countryCode;
                    break;
            }
        }
    }
    
    return $output;
}

add_action( 'wp_mail_failed', 'onMailError', 10, 1 );
function onMailError( $wp_error ) {
    echo "<pre>";
    print_r($wp_error);
    echo "</pre>";
}