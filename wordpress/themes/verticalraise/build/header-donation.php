<?php
ob_start();

use classes\app\fundraiser\Fundraiser_Media;

// 3 Critical Variables used around the site
$fundraiser_id = ( ! empty( $_GET['fundraiser_id'] ) ) ? $_GET['fundraiser_id'] : 0;
$display_type  = ( ! empty( $_GET['display_type'] ) ) ? $_GET['display_type'] : '';

if ( is_user_logged_in() ) {
    global $user_ID;
    $user_info = get_userdata( $user_ID );
}

global $template;

$page_name          = basename( $template );
$return             = '';
$return_type        = '';
$base_url           = get_template_directory_uri();
$logo_img           = $base_url . "/assets/images/logo2.png";
$header_class       = "";
$logo_class         = "small_logo";
$post_id            = $fundraiser_id;
?>
<!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
    <!--<![endif]-->
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
        <!-- Responsive and mobile friendly stuff -->
        <!--    <meta name="HandheldFriendly" content="True">-->
        <!--    <meta name="MobileOptimized" content="320">-->
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">

        <title>
            <?php
            /*
             * Print the <title> tag based on what is being viewed.
             */
            global $page, $paged;
            wp_title( '|', true, 'right' );
// Add the blog name.
            bloginfo( 'name' );
// Add the blog description for the home/front page.
            $site_description   = get_bloginfo( 'description', 'display' );
            if ( $site_description && ( is_home() || is_front_page() ) )
                echo " | $site_description";
// Add a page number if necessary:
            if ( $paged >= 2 || $page >= 2 )
                echo ' | ' . sprintf( __( 'Page %s', 'twentyeleven' ), max( $paged, $page ) );
            ?>
        </title>
        <!-- SET: FAVICON -->
        <link rel="shortcut icon" type="image/png" sizes="4x4"
              href="<?php bloginfo( 'template_directory' ); ?>/assets/images/favicon.png">
        <!-- END: FAVICON -->

        <!-- SET: FONTS -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900" rel="stylesheet">
        <!-- END: FONTS -->

        <!-- SET: STYLESHEET -->

        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo( 'template_directory' ); ?>/assets/css/font-awesome.min.css"/>
        <link rel="stylesheet" href="<?php bloginfo( 'template_directory' ); ?>/assets/css/icon-font.min.css"/>
        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo( 'template_directory' ); ?>/assets/css/bootstrap.min.css"/>

        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo( 'template_directory' ); ?>/assets/css/jquery.mCustomScrollbar.min.css"/>

        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo( 'template_directory' ); ?>/assets/css/style.css?ts=<?php echo time() ?>"/>
        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo( 'template_directory' ); ?>/assets/css/responsive.css?ts=<?php echo time() ?>"/>
        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo( 'template_directory' ); ?>/assets/css/buttonLoader.css"/>


        <!-- END: STYLESHEET -->

        <?php
        $fundraise_mediaObj = new Fundraiser_Media();
        $image_url          = $fundraise_mediaObj->get_fundraiser_logo( $post_id );
        ?>
        <meta property="og:url" content="<?php echo get_the_permalink( $post_id ); ?>"/>
        <meta property="og:type" content="article"/>
        <meta property="og:title" content="Click here to support the <?php echo get_the_title( $post_id ); ?>"/>
        <meta property="og:description" content='<?php echo get_post_meta( $post_id, 'campaign_msg', true ); ?>'/>
        <meta property="og:site_name" content="<?php echo get_bloginfo( 'name' ); ?>"/>
        <meta property="og:image" content="<?php echo $image_url; ?>"/>
        <meta property="og:image:width" content="400"/>
        <meta property="og:image:height" content="400"/>

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Click here to support the <?php echo get_the_title( $post_id ); ?>">
        <meta name="twitter:description" content="<?php echo get_post_meta( $post_id, 'campaign_msg', true ); ?>">
        <meta name="twitter:image" content="<?php echo $image_url; ?>">
        <meta property='fb:app_id' content='<?php echo _FACEBOOK_APP_ID ?>'/>

        <script>
            // Global Ajax Variable
            var LoginAjaxUrl = '<?php bloginfo( 'url' ) ?>';
            var ismobile = '<?php echo ( is_mobile_new() ) ? '1' : '0' ?>';
            var TEMP_DIRECTORY = '<?php bloginfo( 'template_directory' ); ?>';
        </script>
        <!-- SET: SCRIPTS -->
        <script type="text/javascript"
        src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery-1.12.4.min.js"></script>
        <script type="text/javascript"
        src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.matchHeight.js"></script>
        <script type="text/javascript"
        src="<?php bloginfo( 'template_directory' ); ?>/assets/js/bootstrap.min.js"></script>

        <script type="text/javascript"
        src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.mCustomScrollbar.js"></script>


        <!--    Datetimepicker-->
        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo( 'template_directory' ); ?>/assets/css/jquery.datetimepicker.css"/>
        <script type="text/javascript"
        src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.datetimepicker.js"></script>
        <!--    Datetimepicker-->
        <!-- JQuery Validate -->
        <script type="text/javascript"
        src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.validate.min.js"></script>
        <script type="text/javascript"
        src="<?php bloginfo( 'template_directory' ); ?>/assets/js/additional-methods.js"></script>
        <script type="text/javascript"
        src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.inputmask.bundle.js"></script>


        <!--    icheckbox -->
        <link type="text/css" rel="stylesheet" href="<?php bloginfo( 'template_directory' ); ?>/assets/css/skins/all.css"/>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/icheck.js"></script>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/form-icheck.js"></script>

        <script type="text/javascript"
        src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.form.min.js"></script>
        <script type="text/javascript"
        src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.imgareaselect.js"></script>
        <link type="text/css" href="<?php bloginfo( 'template_directory' ); ?>/assets/css/imgareaselect-default.css" rel="stylesheet"/>

        <script>
            function removeParam(key, sourceURL) {
                var rtn = sourceURL.split("?")[0],
                        param,
                        params_arr = [],
                        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
                if ( queryString !== "" ) {
                    params_arr = queryString.split("&");
                    for ( var i = params_arr.length - 1; i >= 0; i -= 1 ) {
                        param = params_arr[i].split("=")[0];
                        if ( param === key ) {
                            params_arr.splice(i, 1);
                        }
                    }
                    rtn = rtn + "?" + params_arr.join("&");
                }
                return rtn;
            }
            function closeFancyboxAndRedirectToUrl() {
                jQuery.fancybox.close();
                window.location = removeParam('popup', window.location.href);
            }
            // donors comment read more function
            function comment_moreless(visibleCharacters, obj) {
                var comments = obj;
                comments.each(function () {
                    var text = $(this).text();
                    var wholetext = text.slice(0, visibleCharacters) + "<span class='ellipsis'>...</span><a href='#' class='more'>Read More →</a>" +
                            "<span style='display:none'>" + text.slice(visibleCharacters, text.length) + "<a href='#' class='less'>Less ←</a></span>";
                    if ( text.length < visibleCharacters ) {
                        return;
                    } else {
                        $(this).html(wholetext);
                    }
                });

                $(".more").click(function (e) {
                    e.preventDefault();
                    $(this).hide().prev().hide();
                    $(this).next().show();
                });

                $(".less").click(function (e) {
                    e.preventDefault();
                    $(this).parent().hide().prev().show().prev().show();
                })
            }
        </script>

        <!--    google oauth for video upload-->
        <?php if ( is_user_logged_in() ) { ?>
            <script>
                $(window).load(function () {
                    handleClientLoad()
                });

                var CLIENT_ID = '<?php echo _GOOGLE_CLIENT_ID ?>';
                var SECRET = '<?php echo _GOOGLE_CLIENT_SECRET ?>'
                var REFRESH_TOKEN = '<?php echo _GOOGLE_REFRESH_TOKEN ?>'


                function handleClientLoad() {

                    $.ajax({
                        url: 'https://www.googleapis.com/oauth2/v4/token',

                        method: 'POST',
                        dataType: 'json',
                        data: {
                            client_id: CLIENT_ID,
                            client_secret: SECRET,
                            refresh_token: REFRESH_TOKEN,
                            grant_type: 'refresh_token'
                        },
                        success: function (data) {
                            //                    console.log(data);
                            _token = data.access_token;

                        }
                    })

                }
            </script>
        <?php } ?>
        <meta property='fb:app_id' content='<?php echo _FACEBOOK_APP_ID ?>'/>
        <script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/progressbar.js?ts=<?php echo time() ?>"></script>
        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo( 'template_directory' ); ?>/assets/js/skins/default/progressbar.css?ts=<?php echo time() ?>">

        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo( 'template_directory' ); ?>/assets/js/skins/big-green/progressbar.css?ts=<?php echo time() ?>">
        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo( 'template_directory' ); ?>/assets/js/skins/big-white/progressbar.css?ts=<?php echo time() ?>">

        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/mailcheck.js?ts=<?php echo time() ?>"></script>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.buttonLoader.js?ts=<?php echo time() ?>"></script>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/custom.js?ts=<?php echo time() ?>"></script>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/auth.js?ts=<?php echo time() ?>"></script>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/myaccount.js?ts=<?php echo time() ?>"></script>

        <?php
        /* We add some JavaScript to pages with the comment form
         * to support sites with threaded comments (when in use).
         */
        if ( is_singular() && get_option( 'thread_comments' ) )
            wp_enqueue_script( 'comment-reply' );
        /* Always have wp_head() just before the closing </head>
         * tag of your theme, or you will break many plugins, which
         * generally use this hook to add elements to <head> such
         * as styles, scripts, and meta tags.
         */
        wp_enqueue_script( 'jquery' );
        wp_head();
        ?>

        <?php

        // Display Google Analytics
        use \classes\app\google_analytics\GoogleAnalytics;

		$google_analytics = new GoogleAnalytics();
		echo $google_analytics->display();

		global $ab_category;
		global $ab_action;
		// Fire custom A/B Test GA Event Code.
		if ( ! empty( $ab_category ) && ! empty( $ab_action ) ) {
			echo $google_analytics->custom_event( $ab_category, $ab_action );
		}
        ?>

    </head>

    <body <?php body_class(); ?>>

        <!--WRAPPER start-->
        <div class="wrapper">
            <!--HEADER start-->
            <header class="<?php echo $header_class; ?>" style="<?php
            if ( is_mobile_new() && !is_user_logged_in() ) {
                echo "display:none";
            }
            ?>">
                <!--CONTAINER start-->
                <div class="container clearfix" style="display:<?php echo (is_mobile_new() && !is_user_logged_in()) ? 'none' : 'block' ?>">
                    <!--LOGO START-->
                    <div class="logo <?php echo $logo_class ?>">
                        <a href="<?php echo get_bloginfo( 'url' ) ?>" title="VERTICALRAISE">
                            <img src="<?php echo $logo_img ?>" alt="">
                        </a>
                    </div>
                    <!--LOGO END-->
                    <!--TOGGLE MENU	start-->
                    <div class="toggle_menu">
                        <div id="nav-icon2">
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                    <!--TOGGLE MENU	end-->

                    <!--RIGHT HEADER start-->
                    <div class="right_header">

                        <?php if ( !is_user_logged_in() ) { ?>
                            <ul>
                                <li>
                                    <a href="<?php bloginfo( 'url' ); ?>/">Home</a>
                                </li>
                                <li>
                                    <a class="loginLink" href="#">Login</a>
                                </li>
                                <li>
                                    <a class="signupLink" href="#">Sign-up</a>
                                </li>
                                <li>
                                    <a href="<?php bloginfo( 'url' ); ?>/contact-us/">Contact Us</a>
                                </li>
                                <li>
                                    <a href="<?php bloginfo('url'); ?>/careers/">Careers</a>
                                </li>
                            </ul>
                        <?php } else { ?>
                            <ul>
                                <li>
                                    <a href="<?php bloginfo( 'url' ); ?>/">Home</a>
                                </li>
                                <li>
                                    <a href="<?php bloginfo( 'url' ); ?>/contact-us/">Contact Us</a>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="<?php bloginfo( 'url' ); ?>/my-account/">My Account</a>
                                    <div class="mega_menu">
                                        <div class=" clearfix">
                                            <div class="menu_content">
                                                <div class="row">
                                                    <div class="col col_left">
                                                        <?php echo get_avatar( $user_ID, 170 ); ?>
                                                    </div>
                                                    <div class="col col_right">
                                                        <h4><?php echo $user_info->first_name . " " . $user_info->last_name; ?></h4>
                                                        <ul>
                                                            <li>
                                                                <a href="<?php bloginfo( 'url' ); ?>/my-account/">my
                                                                    account</a>
                                                            </li>
                                                            <li>
                                                                <a href="<?php bloginfo( 'url' ); ?>/edit-profile/">edit
                                                                    profile</a>
                                                            </li>
                                                            <li>
                                                                <a href="<?php bloginfo( 'url' ); ?>/my-account/"
                                                                   class="join_fundraiser">join a fundraiser(s)</a>
                                                            </li>
                                                            <li>
                                                                <a href="#" class="create_fundraiser">create a fundraiser</a>
                                                            </li>
                                                            <li>
                                                                <a href="<?php bloginfo( 'url' ); ?>/my-account/"
                                                                   class="current_fundraiser">current fundraisers</a>
                                                            </li>
                                                            <li>
                                                                <a href="<?php bloginfo( 'url' ); ?>/my-account/"
                                                                   class="upcoming_fundraiser">upcoming fundraisers</a>
                                                            </li>
                                                            <li>
                                                                <a href="<?php echo wp_logout_url( get_bloginfo( 'url' ) ); ?>">logout</a>
                                                            </li>
                                                            <li>
                                                                <a href="#" class="past_fundraiser">past fundraisers</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <a href="<?php echo wp_logout_url( get_bloginfo( 'url' ) ); ?>">Log Out</a>
                                </li>
                            </ul>
                        <?php } ?>
                    </div>
                    <!--RIGHT HEADER end-->
                </div>
                <?php if ( is_mobile_new() && !is_user_logged_in() ) { ?>
                    <div class="sec_header landing mob" style="display: none">
                        <div class="container clearfix">
                            <?php
                            $base_url          = get_site_url();
                            $fundraiser_string = '/donation/?fundraiser_id=' . $post_id;
                            $media_string      = ( isset( $media ) ) ? '&media=' . $media : '';
                            $uid_string        = ( isset( $uid ) ) ? '&uid=' . $uid : '';
                            $email_string      = ( isset( $semail ) ) ? '&semail=' . $semail : '';
                            // Donation URL
                            $donation_url      = $base_url . $fundraiser_string . $media_string . $uid_string . $email_string;
                            $donation_btn      = "Donate Now";
                            ?>
                            <ul>
                                <li>
                                    <a href="<?php echo $donation_url ?>">
                                        <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon6.png"
                                             alt="">
                                        <span>Donate</span>
                                    </a>
                                </li>
                                <li>
                                    <?php if ( !is_mobile_new() ) { ?>
                                        <a href="javascript:void(0);" onclick="popup_facebookshare()">
                                            <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon7.png"
                                                 alt="">
                                            <span>share on facebook</span>
                                        </a>

                                    <?php } else { ?>

                                        <a href="https://www.facebook.com/dialog/feed?app_id=<?php echo _FACEBOOK_APP_ID ?>&display=popup&caption=<?php echo urlencode( $title ); ?>&link=<?php echo $permalink_facebook_embed; ?>&redirect_uri=<?php echo urlencode( $permalink ); ?>">
                                            <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon7.png"
                                                 alt="">
                                            <span>share on facebook</span>
                                        </a>
                                    <?php } ?>

                                </li>
                                <li>
                                    <a href="javascript:void(0);" onclick="popup_tweetshare()">
                                        <!--                        <a href="https://twitter.com/share?url=-->
                                        <?php //echo $permalink_twitter;
                                        ?><!--"-->
                                        <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon8.png"
                                             alt="">
                                        <span>Tweet</span>
                                    </a>
                                </li>
                            </ul>

                        </div>
                    </div>
                <?php } ?>
            </header>


            <!--Login and SignUp popup modal -->
            <?php get_template_part( 'template-parts/login_popup' ); ?>
            <?php get_template_part( 'template-parts/signup_popup' ); ?>
            <?php get_template_part( 'template-parts/forgotpassword_popup' ); ?>

            <?php if ( is_user_logged_in() ) { ?>
                <!--    CreateFundariser popup modal    -->
                <?php get_template_part( 'template-parts/createfund_popup' ); ?>

                <?php
                if ( $page_name == "page-single-fundraiser.php" ) {
                    //   EditFundariser popup modal    -->
                    get_template_part( 'template-parts/editfund_popup' );
                    //   inviteAdmin popup modal    -->
                    get_template_part( 'template-parts/inviteAdmin_popup' );
                }
                ?>
            <?php } ?>

