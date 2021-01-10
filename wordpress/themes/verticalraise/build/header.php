<?php
ob_start();

use \classes\app\fundraiser\Fundraiser_Ended;      // Fundraiser Ended Class Object
use \classes\app\google_analytics\GoogleAnalytics; // Google Analytics Object
use classes\app\fundraiser\Fundraiser_Media;       //Fundraiser Media Class Object

// 3 Critical Variables used around the site
$fundraiser_id = (!empty( $_GET['fundraiser_id'] ) ) ? $_GET['fundraiser_id'] : get_the_ID();
$display_type  = (!empty( $_GET['display_type'] ) ) ? $_GET['display_type'] : '';

if ( is_user_logged_in() ) {
    global $user_ID;
    $uid         = $user_ID;
    $user_info   = get_userdata( $user_ID );
    $first_login = get_user_meta( $uid, 'first_login', true );
} else {
    $uid = (!empty( $_GET['uid'] ) ) ? $_GET['uid'] : 0;
}

global $template;

$page_name   = basename( $template );
$return      = '';
$return_type = '';

$base_url = get_template_directory_uri();

if ( $page_name == "page-single-fundraiser.php" ) {
    $header_class = "wizard_header";
    $logo_img     = $base_url . "/assets/images/logo.png";
    $logo_class   = "";
} else if ( $page_name == "page-participant-fundraiser.php" ) {
    $header_class = "dashboard_page_header";
    $logo_img     = $base_url . "/assets/images/logo.png";
    $logo_class   = "";
    $return       = "participant";
} else if ( $page_name == "index.php" ) {
    $header_class = "home_header";
    $logo_img     = $base_url . "/assets/images/logo.png";
    $logo_class   = "";
} else {
    $logo_img     = $base_url . "/assets/images/logo2.png";
    $header_class = "";
    $logo_class   = "small_logo";
}

if ( $page_name == "page-my-account.php" ) {
    $return = "my-account";
}
if ( $page_name == "page-edit-profile.php" ) {
    $return = "edit-profile";
}
$return_url = '';
if ( $return != '' ) {
    switch ( $return ) {
        case 'participant' :
            $return_url = get_site_url() . '/participant-fundraiser/?fundraiser_id=' . $fundraiser_id;
            break;
        case 'my-account':
            $return_url = get_site_url() . '/my-account';
            break;
        case 'edit-profile':
            $return_url = get_site_url() . '/edit-profile';
            break;
    }
}

$post_id                 = $fundraiser_id;
$campaign_participations = get_user_meta( $uid, 'campaign_participations', true );
$participations_array    = json_decode( $campaign_participations );

if ( $participations_array == NULL )
    $participations_array = array();

// Set the source - (is the user logged in and is he part of this campaign?)
if ( ( is_user_logged_in() && in_array( $post_id, $participations_array ) ) || (!empty( $_GET['parent'] ) && $_GET['parent'] == 1 ) ) {
    $source = 'invite';
} else {
    $source = '';
}

$permalink          = get_permalink( $post_id );
$permalink_facebook = $permalink . 'f/' . $uid;
$permalink_twitter  = urlencode( $permalink . 't/' . $uid );
$twitter_share_text = "Click Here to support the " . get_the_title( $post_id ) . "%0A" . $permalink_twitter;

$fundraiser_end   = new Fundraiser_Ended( $fundraiser_id );
$ended_fundraiser = $fundraiser_end->check_end();

if ( _SERVER_TYPE == 'dev' ) {
    $stripe_secret_key      = _STRIPE_DEV_SECRET_KEY;
    $stripe_publishable_key = _STRIPE_DEV_PUBLISHABLE_KEY;
} else {
    $stripe_secret_key      = _STRIPE_SECRET_KEY;
    $stripe_publishable_key = _STRIPE_PUBLISHABLE_KEY;
}

$stripe             = array(
    "secret_key"      => $stripe_secret_key,
    "publishable_key" => $stripe_publishable_key
);
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
        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">
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
              href="<?php bloginfo( 'template_directory' ); ?>/assets/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?php bloginfo( 'template_directory' ); ?>/assets/css/icon-font.min.css">
        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo( 'template_directory' ); ?>/assets/css/bootstrap.min.css">

        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo( 'template_directory' ); ?>/assets/css/jquery.mCustomScrollbar.min.css">

        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo( 'template_directory' ); ?>/assets/css/style.css?ts=<?php echo time() ?>">
        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo( 'template_directory' ); ?>/assets/css/responsive.css?ts=<?php echo time() ?>">
        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo( 'template_directory' ); ?>/assets/css/buttonLoader.css">


        <script>
            // Global Ajax Variable
            var LoginAjaxUrl = '<?php bloginfo( 'url' ) ?>';
            var ismobile = '<?php echo ( is_mobile_new() ) ? '1' : '0' ?>';
            var TEMP_DIRECTORY = '<?php bloginfo( 'template_directory' ); ?>';
            var THE_CHECKER_API_KEY = '<?php echo _THE_CHECKER_API_KEY; ?>';
            var Css_is_mobile;
        </script>

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


        <!-- SET: SCRIPTS -->
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery-1.12.4.min.js"></script>
        <script type="text/javascript" src="https://js.stripe.com/v3/"></script>
        <script>
            var stripe = Stripe('<?php echo $stripe['publishable_key'] ?>');
        </script>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.matchHeight.js"></script>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.mCustomScrollbar.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php bloginfo( 'template_directory' ); ?>/assets/css/jquery.datetimepicker.css"/>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.datetimepicker.js"></script>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.validate.min.js"></script>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/additional-methods.js"></script>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.inputmask.bundle.js"></script>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.maskedinput.min.js"></script>

        <script>
            $(document).ready(function () {
                addMask();

            })
            function addMask() {
                $.mask.definitions['~'] = "[+-]";
                $(".phone").mask("(999) 999-9999");
            }
        </script>

        <!--    icheckbox -->
        <link type="text/css" rel="stylesheet" href="<?php bloginfo( 'template_directory' ); ?>/assets/css/skins/all.css"/>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/icheck.js"></script>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/form-icheck.js"></script>

        <link rel="stylesheet" type="text/css" href="<?php bloginfo( 'template_directory' ); ?>/assets/css/jquery.fancybox.css"/>
        <script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/modernizr-2.8.2-min.js"></script>
        <script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.fancybox.pack.js"></script>
        <script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.countdown.js"></script>

        <script type="text/javascript">

            jQuery(".fancybox").fancybox({
                afterClose: function () {
                    location.href = "<?php echo $return_url; ?>";
                }
            });

            $(document).ready(function () {

                <?php if ( isset( $_GET['editpopup'] ) && $_GET['editpopup'] == 1 ) { ?>
                setTimeout(function () {
                    $('#edit_fundraiser').modal('show');
                    // #multimedia
                }, 300);
<?php } if ( !is_mobile_new() ) { ?>
                    var upload_success = 'false';
                    var upload_url = '';
                    //Profile Upload
                    $(".fancyboxUpload").fancybox({
                        width: 780,
                        maxWidth: 780,
                        minWidth: 300,
                        minHeight: 1500,
                        maxHeight: 1500,
                        height: 1500,
                        scrolling: 'no',
                        wrapCSS: 'Fancyupload',
                        beforeLoad: function () {
                            $('#upload_pro_pic').modal('hide');
                        },
                        beforeClose: function () {
                            var $upload_frame = $(".fancybox-iframe");
                            upload_success = $('input#uploadsuccess', $upload_frame.contents()).val();
                        },
                        afterClose: function () {
                            $('#upload_pro_pic').modal('hide');
                            if ( upload_success == 'success' ) {
                                location.href = "<?php echo $return_url; ?>";
                            }
                        },
                        helpers: {
                            overlay: { closeClick: false }
                        }
                    });

                    //Team logo upload
                    $(".fancyboxLogoUpload").fancybox({
                        width: 780,
                        maxWidth: 780,
                        minWidth: 300,
                        minHeight: 1500,
                        maxHeight: 1500,
                        height: 1500,
                        scrolling: 'no',
                        wrapCSS: 'Fancyupload',
                        beforeLoad: function () {
                            $('#edit_fundraiser').modal('hide');
                        },
                        beforeClose: function () {
                            var $upload_frame = $(".fancybox-iframe");
                            upload_success = $('input#uploadsuccess', $upload_frame.contents()).val();
                            upload_url = $("input#uploadurl", $upload_frame.contents()).val();
                        },
                        afterClose: function () {
                            if ( upload_success == 'success' ) {
                                $(".fundraiser_logo img").attr("src", upload_url)
                                $('#edit_fundraiser .logo_update.fancyboxLogoUpload').css('background-image', 'url(' + upload_url + ')');
                                $('#edit_fundraiser').modal('show');
                            }
                        },
                        helpers: {
                            overlay: { closeClick: false }
                        }
                    });

                    $(".fancyboxInvite").fancybox({
                        width: 780,
                        maxWidth: 780,
                        minWidth: 300,
                        minHeight: 1500,
                        maxHeight: 5000,
                        height: 4000,
                        scrolling: 'no',
                        wrapCSS: 'FancyInvite',
                        helpers: {
                            overlay: { closeClick: false }
                        }
                    });
<?php } ?>
            });
        </script>

        <script type="text/javascript"
        src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.form.min.js"></script>
        <script type="text/javascript"
        src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.imgareaselect.js"></script>
        <link type="text/css" href="<?php bloginfo( 'template_directory' ); ?>/assets/css/imgareaselect-default.css"
              rel="stylesheet"/>
        <script>

            //facebook share
            window.fbAsyncInit = function () {
                FB.init({
                    appId: '<?php echo _FACEBOOK_APP_ID ?>', // FB App ID
                    cookie: true, // enable cookies to allow the server to access the session
                    xfbml: true, // parse social plugins on this page
                    version: 'v2.8' // use graph api version 2.8
                });

                FB.AppEvents.logPageView();

            };

            (function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if ( d.getElementById(id) ) {
                    return;
                }
                js = d.createElement(s);
                js.id = id;
                js.src = "https://connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));


            function popup_facebookshare() {
                FB.ui({
                    method: 'share',
                    mobile_iframe: true,
                    //hashtag: '#',
                    display: 'popup',
                    href: '<?php echo $permalink_facebook; ?>',
                }, function (response) {
                    if ( response && !response.error_code ) {
                        jQuery('.message_hidden').show();
                        jQuery.ajax({
                            url: "<?php bloginfo( 'url' ); ?>/participants-invite-share-ajax/",
                            data: {
                                "success": 1,
                                "post_id": <?php echo $post_id; ?>,
                                "user_id": '<?php echo $uid; ?>',
                                "type": 'facebook',
                                "source": '<?php echo $source; ?>'
                            },
                            async: false,
                            type: 'POST',
                            success: function (response) {
                                console.log(response);
                            }
                        });

                    } else {
                        jQuery('.message_hidden').hide();
                    }
                });
            }
            ;

            //tweet share

            function popup_tweetshare() {

                var win = window.open('https://twitter.com/intent/tweet?text=<?php echo $twitter_share_text; ?>', 'Twitter', 'width=500,height=400,scrollbars=no');
                var timer = setInterval(function () {
                    //if(win.closed) {
                    clearInterval(timer);
                    jQuery.ajax({
                        url: "<?php bloginfo( 'url' ); ?>/participants-invite-share-ajax/",
                        data: {
                            "success": 1,
                            "post_id": <?php echo $post_id; ?>,
                            "user_id": '<?php echo $user_ID; ?>',
                            "type": 'twitter',
                            "source": '<?php echo $source; ?>'
                        },
                        async: false,
                        type: 'POST',
                        success: function (response) {
                            console.log(response);
                        }
                    });
                    //location.href = '<?php /* echo $return_url; */ ?>';
                    //}
                }, 1000);
            }
        </script>
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

            // donor comment read more function

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

            function custom_donation(customObj, customInputObj) {

                var donation_url = customObj.data("href");
                var donate_amout = customInputObj.val();
                if ( donate_amout == '' || donate_amout == 0 ) {
                    alert("please enter amount.");
                    return false;
                }
                window.location.href = donation_url + "&donation_amount=" + donate_amout;
                return;
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


        <script type="text/javascript"
        src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.buttonLoader.js?ts=<?php echo time() ?>"></script>

        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/mailcheck.js?ts=<?php echo time() ?>"></script>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/auth.js?ts=<?php echo time() ?>"></script>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/myaccount.js?ts=<?php echo time() ?>"></script>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/custom.js?ts=<?php echo time() ?>"></script>

        <script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/teamlogo_custom.js?ts=<?php echo time() ?>"></script>

        <?php if ( $page_name == "page-single-fundraiser.php" ) { ?>
            <script src="https://code.highcharts.com/highcharts.js"></script>
            <script src="https://code.highcharts.com/highcharts-more.js"></script>
            <script src="https://code.highcharts.com/modules/exporting.js"></script>
            <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/meter.js?ts=<?php echo time(); ?>"></script>
        <?php } ?>
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

        // Display google analytics
        $GoogleAnalytics = new GoogleAnalytics();
        echo $GoogleAnalytics->display();
        ?>

    </head>

    <body <?php body_class(); ?>>
        <!--PAST FUNDRAISER POPUP start-->
        <?php if ( is_user_logged_in() ) { ?>
            <div class="modal fade upload_pic_modal" id="upload_pro_pic">

                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header model_title">
                            <button type="button" class="close show_in_mob1" data-dismiss="modal"
                                    aria-label="Close"></button>
                            <div class="user">
                                <?php echo get_avatar( $user_ID, 150 ); ?>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div style="text-align: center;">
                                <h2 style="text-decoration: underline;">Please upload a photo of yourself</h2>
                                <p>Adding a photo of yourself personalizes your<br/>fundraiser page and helps you raise
                                    more.
                                </p>

                                <p style="text-align: center;margin:50px 0">
                                    <a class="custom_button fancyboxUpload"
                                       data-fancybox-type="iframe"
                                       href="<?php bloginfo( 'url' ); ?>/profile-image-upload/?from_file=1&f_id=<?php echo $fundraiser_id ?>&return=<?php echo $return; ?>">Upload
                                        Picture From File</a>
                                </p>
                                <p style="text-align: center">
                                    <a href="javascript:void(0);" onclick="$('#upload_pro_pic').modal('hide');">Skip</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <!--WRAPPER start-->
        <div class="wrapper wrap_div" >

            <!--HEADER start-->
            <header class="<?php echo $header_class; ?>">
                <noscript>
                <div class="noscript_div" >
                    <span style="color:red">JavaScript is not enabled in this browser!</span>
                </div>
                </noscript>
                <!--CONTAINER start-->
                <div class="container clearfix">
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
                <!--CONTAINER end-->
                <?php
                if ( $page_name == "page-single-fundraiser.php" ) {
                    $invite_donor_url = (!$ended_fundraiser) ? get_bloginfo( 'url' ) . "/invite-start/?fundraiser_id=" . $fundraiser_id . "&type=admin" : "#";
	                $check_upload_page = get_bloginfo( 'url' ) . "/participant-check-upload/?fundraiser_id=$fundraiser_id&type=permalink&uid=0";
	                ?>
                    <script src="https://apis.google.com/js/client:plusone.js"></script>
                    <script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/cors_upload.js?ts=<?php echo time() ?>"></script>
                    <script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/upload_video.js?ts=<?php echo time() ?>"></script>
                    <script type="text/javascript"
                    src="<?php bloginfo( 'template_directory' ); ?>/assets/js/fundraiser.js?ts=<?php echo time() ?>"></script>

                    <div class="sec_header landing1">

                        <div class="container clearfix">
                            <div class="submeni_toggle">
                                <a id="submenu_thumb" style="">Admin Menu
                                </a>
                                <div class="close_bt"><a class="">X</a></div>
                            </div>
                            <script>
                $("#submenu_thumb").click(function () {
                    $(".close_bt").toggle('slow');
                    $("ul.sec_menu ").toggle('slow')
                })
                $(".close_bt").click(function () {
                    $(".close_bt").toggle('slow');
                    $("ul.sec_menu ").toggle('slow')
                })
                            </script>

                            <ul class="sec_menu">
                                <li>
                                    <a href="#" class="edit_fundraiser">Edit Campaign</a>
                                </li>
                                <li>
                                    <a href="/my-account?duplicate_f_id=<?php echo $fundraiser_id; ?>" class="duplicate_fundraiser">Duplicate Fundraiser</a>
                                </li>
                                <li>
                                    <a href="<?php bloginfo( 'url' ); ?>/single-fundraiser/?fundraiser_id=<?php echo $fundraiser_id; ?>&print_inst=true"
                                       target="_blank" class="">Print Quick Launch - Parent Letter</a>
                                </li>
                                <li>
                                    <a href="<?php bloginfo( 'url' ); ?>/single-fundraiser/?fundraiser_id=<?php echo $fundraiser_id; ?>&print_parent=true"
                                       target="_blank" class="">Print Front load - Parent Letter</a>
                                </li>
	                            <?php if ( is_mobile_new() ) { ?>
                                    <li>
                                        <a href="<?php echo $check_upload_page ?>" >Check upload</a>
                                    </li>
	                            <?php } ?>
                                <li>
                                    <!--                            invite_donors-->
                                    <a class="fancyboxInvite participant_invite <?php echo ($ended_fundraiser) ? 'no-event' : ''; ?>" data-fancybox-type="iframe"
                                       href="<?php echo $invite_donor_url; ?>">invite
                                        donors</a>
                                </li>
                                <li>
                                    <a href="<?php bloginfo( 'url' ); ?>/single-fundraiser/?fundraiser_id=<?php echo $fundraiser_id; ?>&report=true">run
                                        report</a>
                                </li>
                            </ul>

                        </div>

                    </div>
                    <?php
                } else if ( $page_name == "page-participant-fundraiser.php" ) {
                    $check_upload_page = get_bloginfo( 'url' ) . "/participant-check-upload/?fundraiser_id=$fundraiser_id&type=permalink&uid=$uid";
                    $invite_url = (!$ended_fundraiser) ? get_bloginfo( 'url' ) . "/invite-start/?fundraiser_id=" . $fundraiser_id . "&type=participant" : "#";
                    ?>
                    <div class="sec_header landing1">

                        <div class="container clearfix">
                            <ul>
                                <li>
                                    <a class="fancyboxInvite participant_invite <?php echo ($ended_fundraiser) ? 'no-event' : ''; ?>" data-fancybox-type="iframe"
                                       href="<?php echo $invite_url ?>" >invite
                                        wizard</a>
                                </li>
                                <?php if ( is_mobile_new() ) { ?>
                                <li>
                                    <a
                                       href="<?php echo $check_upload_page ?>" >check upload</a>
                                </li>
	                            <?php } ?>
                                <li>
                                    <a href="<?php bloginfo( 'url' ); ?>/participant-fundraiser/?fundraiser_id=<?php echo $fundraiser_id; ?>&print_inst=true"
                                       target="_blank">Print instructions</a>
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
                <?php
                if ( $page_name == "page-my-account.php" ) {
                    get_template_part( 'template-parts/createfund_popup' );
                }
                ?>
                <?php get_template_part( 'template-parts/success_popup' ); ?>

                <?php
                if ( $page_name == "page-single-fundraiser.php" ) {
                    //   EditFundariser popup modal    -->
                    get_template_part( 'template-parts/editfund_popup' );
                    //   inviteAdmin popup modal    -->
                    get_template_part( 'template-parts/inviteAdmin_popup' );
                }
                ?>
            <?php }
