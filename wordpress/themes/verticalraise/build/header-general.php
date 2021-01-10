<?php
ob_start();

use \classes\app\fundraiser\Fundraiser_Ended;      // Fundraiser Ended Class Object
use \classes\app\google_analytics\GoogleAnalytics; // Google analytics Class Object
use classes\app\fundraiser\Fundraiser_Media;       // Fundraiser Media Class Object
use \classes\app\utm\UTMJavascript;
use \classes\app\utm\UTM;
use classes\app\encryption\Encryption;

$fundraiser_id = (!empty( $_GET['fundraiser_id'] ) ) ? $_GET['fundraiser_id'] : '';
$display_type  = (!empty( $_GET['display_type'] ) ) ? $_GET['display_type'] : '';
$uid           = (!empty( $_GET['uid'] ) ) ? $_GET['uid'] : 0;

// Fancy box redirects
$page_name = basename( get_page_template() );

if ( is_user_logged_in() ) {
    global $user_ID;
    $user_info = get_userdata( $user_ID );
}
$return      = '';
$return_type = '';

if ( $page_name == "page-my-account.php" ) {
    $return      = "my-account";
    $return_type = 1;
} else if ( isset( $_GET['fundraiser_id'] ) ) {
    $return      = "participant";
    $return_type = 2;
} else if ( $page_name == "single-fundraiser.php" ) {
    $return = 'permalink';

    $return_type = 1;
} else if ( $page_name == "page-edit-profile.php" ) {
    $return = "edit-profile";
}

if ( is_singular( 'fundraiser' ) ) {
    if ( isset( $wp_query->query_vars['media'] ) ) {
        $media = urldecode( $wp_query->query_vars['media'] );
    } else {
        $media = '';
    }

    if ( isset( $wp_query->query_vars['uid'] ) ) {
        $uid = urldecode( $wp_query->query_vars['uid'] );
    } else {
        $uid = 0;
    }
    if ( isset( $wp_query->query_vars['semail'] ) ) {
        $semail = urldecode( $wp_query->query_vars['semail'] );
    } else {
        $semail = '';
    }

    $post_id = get_the_ID();
} else {
    $post_id = $fundraiser_id;
}
global $template;

//$page_name   = basename($template);
//$return      = '';
//$return_type = '';
$base_url   = get_template_directory_uri();
$return_url = '';

if ( $return != '' ) {
    switch ( $return ) {
        case 'participant' :
            $return_url = get_site_url() . '/participant-fundraiser/?fundraiser_id=' . $post_id;
            break;
        case 'my-account':
            $return_url = get_site_url() . '/my-account';
            break;
        case 'edit-profile':
            $return_url = get_site_url() . '/edit-profile';
            break;
        case 'permalink':
            $return_url = get_permalink( $post_id );
            break;
    }
}

if ( $page_name == "page-thank-you-payment.php" ) {
    $header_class = "";
    $logo_img     = $base_url . "/assets/images/logo2.png";
    $logo_class   = "small_logo";
} else if ( $page_name == "single-fundraiser.php" ) {
    $header_class = "landing_page_header";
    $logo_img     = $base_url . "/assets/images/logo2.png";
    $logo_class   = "small_logo";
} else {

    $header_class = "dashboard_page_header";
    $logo_img     = $base_url . "/assets/images/logo2.png";
    $logo_class   = "small_logo";
}
$admin = 0;
if ( is_user_logged_in() ) {
    if ( current_user_can( 'administrator' ) ) {
        $admin        = 1;
        $header_class = "wizard_header";
    }
}

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

$permalink                = get_permalink( $post_id );
$permalink_facebook       = $permalink . 'f/' . $uid;
$permalink_facebook_embed = urlencode( $permalink . 'f/' . $uid );
$url                      = urlencode( $permalink . 't/' . $uid );


$utm = new UTM;
if ( isset( $_GET['page'] ) && $_GET['page'] == 'thankyou') {
    // Tahnk you facebook share
    if ( !is_mobile_new() ) {
        $permalink_facebook = $utm->createUTMLink( $permalink_facebook, 'Thank_You_Facebook_Share' );
    } else {
        $permalink_facebook_embed = $utm->createUTMLink( $permalink_facebook_embed, 'Thank_You_Facebook_Share' );
    }
    // Thank you twitter share
    $permalink_twitter = $utm->createUTMLink( $url, 'Thank_You_Twitter_Share' );
} else {
    // Facebook share
    if ( !is_mobile_new() ) {
        $permalink_facebook = $utm->createUTMLink( $permalink_facebook, 'Facebook_Share' );
    } else {
        $permalink_facebook_embed = $utm->createUTMLink( $permalink_facebook_embed, 'Facebook_Share' );
    }
    // Twitter Share
    $permalink_twitter = $utm->createUTMLink( $url, 'Twitter_Share' );
}
$twitter_share_text       = "Click Here to support the " . get_the_title( $post_id ) . "%0A" . $permalink_twitter;

$title            = get_the_title( $post_id );
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
<html <?php language_attributes(); ?> style="margin-top: 0 !important;">
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
            $site_description = get_bloginfo( 'description', 'display' );
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
        <!-- END: STYLESHEET -->

        <script>
            // Global variable
            var LoginAjaxUrl = '<?php bloginfo( 'url' ) ?>';
            var ismobile = '<?php echo ( is_mobile_new() ) ? '1' : '0' ?>';
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

        <!--    icheckbox -->
        <link type="text/css" rel="stylesheet" href="<?php bloginfo( 'template_directory' ); ?>/assets/css/skins/all.css"/>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/icheck.js"></script>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/form-icheck.js"></script>

        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo( 'template_directory' ); ?>/assets/css/jquery.fancybox.css"/>
        <script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/modernizr-2.8.2-min.js"></script>
        <script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.fancybox.pack.js"></script>
        <script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.countdown.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $(".fancybox").fancybox({
                    afterClose: function () {
                        location.href = "<?php echo $return_url; ?>";
                    }
                });

                <?php if ( !is_mobile_new() ) { ?>
                    //

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

        <!--    --><?php //if ( is_singular('fundraiser') ) {           ?>
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
        <meta name="twitter:title" content="Click here to support the <?php echo get_the_title( get_the_ID() ); ?>">
        <meta name="twitter:description" content="<?php echo get_post_meta( $post_id, 'campaign_msg', true ); ?>">
        <meta name="twitter:image" content="<?php echo $image_url; ?>">
        <!--    --><?php //}           ?>

        <?php if ( $admin == 1 ) { ?>
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
        src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.inputmask.bundle.js"></script>

        <script type="text/javascript"
        src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.buttonLoader.js?ts=<?php echo time() ?>"></script>
        <script type="text/javascript"
        src="<?php bloginfo( 'template_directory' ); ?>/assets/js/custom.js?ts=<?php echo time() ?>"></script>

        <script type="text/javascript"
        src="<?php bloginfo( 'template_directory' ); ?>/assets/js/auth.js?ts=<?php echo time() ?>"></script>
        <script type="text/javascript"
        src="<?php bloginfo( 'template_directory' ); ?>/assets/js/myaccount.js?ts=<?php echo time() ?>"></script>
        <script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/teamlogo_custom.js?ts=<?php echo time() ?>"></script>
        <!-- END: SCRIPTS -->


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

        // Display Google Analytics
        if ( $page_name == "page-thank-you-payment.php" ) {

			$GoogleAnalytics = new GoogleAnalytics();

            $encryption = new Encryption;

            // Get incoming transaction_id
            if ( !empty( $_GET['transaction_id'] ) ) {

                // Unencrypt the encrypted transaction id
                $decrypt = $encryption->decrypt( urldecode( $_GET['transaction_id'] ) );
                $str_get = explode( '-', $decrypt );

				$one_min_ago = strtotime( '1 min ago', current_time( "timestamp" ) );

				$transaction_time = (int) $str_get[1];
				$plus_one_minute = strtotime("plus 1 minutes", $transaction_time);

				$fire_pixel = false;

				// Case 1: User has no `transaction_id` cookie
				if ( empty( $_COOKIE["transactionID"] ) &&
					 $plus_one_minute <= time() &&
					 !empty($_GET['tamount']) ) {
						$fire_pixel = true;
				}

				// Case 2: User has a `transaction_id` cookie for another transaction
				if (
					!empty( $_COOKIE["transactionID"] ) &&
					$str_get[0] != $_COOKIE["transactionID"] &&
					$plus_one_minute <= time() &&
					!empty($_GET['tamount'])) {
						$fire_pixel = true;
				}

                // Check to see if the cookie has already visited this page, and if a cookie has been set
                if ( $fire_pixel == true ) {

                    $tamount = (float) $_GET['tamount'];

                    $option_ids = [
                        '1' => [ 'name' => 'custom', 'amt' => null],
                        '2' => [ 'name' => 'prefill 50', 'amt' => '50.00'],
                        '3' => [ 'name' => 'prefill 100', 'amt' => '100.00'],
                        '4' => [ 'name' => 'prefill 150', 'amt' => '150.00'],
                        '5' => [ 'name' => 'prefill 250', 'amt' => '250.00'],
                        '6' => [ 'name' => 'prefill 500', 'amt' => '500.00']
                    ];

                    $option_id = '1';

                    foreach ( $option_ids as $key => $opt) {
                        if ( $opt['amt'] == $tamount || $opt['amt'] == $tamount ) {
                            $option_id = $key;
                        }
                    }

                    $option_name = $option_ids[$option_id]['name'];

                    // Configure params for Google Analytics
                    $params['transaction_id']    = $str_get[0];
                    $params['amount']            = $tamount;
                    $params['option_id']         = $option_id;
                    $params['option_name']       = $option_name;

                    // Display Google Analytics
                    echo $GoogleAnalytics->display();
                    echo $GoogleAnalytics->ecommerce($params);

                    setcookie( "transactionID", $str_get[0], time() + 2 * 2 * 4 * 60 * 60 );

                }
            } else {
				echo $GoogleAnalytics->display();
			}

        } else {
            $utm_javascript = new UTMJavascript( $_GET );
            $utm_javascript->display();
        }
        ?>
    </head>

    <body <?php body_class(); ?>>

        <!--PAST FUNDRAISER POPUP start-->
        <div class="modal fade upload_pic_modal" id="upload_pro_pic">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- body -->
                    <div class="modal-body">

                        <div class="modal-header model_title">
                            <button type="button" class="close show_in_mob" data-dismiss="modal"
                                aria-label="Close">
                            </button>
                            <?php echo get_avatar( $user_ID, 170 ); ?>
                        </div>
                        <div class="modal-body">
                            <div style="text-align: center;">
                                <h2 style="text-decoration: underline;">Please upload a photo of yourself</h2>
                                <p>Adding a photo of yourself personalizes your<br/>fundraiser page and helps you raise more.</p>

                                <p style="text-align: center">
                                    <a class="custom_button fancybox" data-fancybox-type="iframe" href="<?php bloginfo( 'url' ); ?>/profile-image-upload/?from_file=1&f_id=<?php echo $fundraiser_id ?>&return=<?php echo $return; ?>">Upload
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
        </div>
        <!--WRAPPER start-->
        <div class="wrapper">
            <!--HEADER start-->
            <header class="<?php echo $header_class; ?> <?php
            if ( is_mobile_new() && !is_user_logged_in() ) {
                echo "no_stick";
            }
            ?>">
                <!--CONTAINER start-->

                <div class="container clearfix" style="display:<?php echo (is_mobile_new() && !is_user_logged_in()) ? 'none' : 'block' ?>">
                    <!--LOGO START-->
                    <div class="logo <?php echo $logo_class ?>">
                        <a href="<?php echo get_bloginfo( 'url' ) ?>" title="VERTICALRAISE">
                            <img src="<?php echo $logo_img; ?>" alt="">
                        </a>
                    </div>
                    <!--LOGO END-->
                    <?php if ( is_user_logged_in() ) { ?>
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
                                        <div class=" clearfix"> <!--container -->
                                            <div class="menu_content">
                                                <div class="row">
                                                    <div class="col col_left">
                                                        <?php echo get_avatar( $user_ID, 170 ); ?>
                                                    </div>
                                                    <div class="col col_right">
                                                        <h4><?php echo $user_info->first_name . " " . $user_info->last_name; ?> </h4>
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

                        </div>
                    <?php } ?>
                    <!--RIGHT HEADER end-->
                </div>
                <!--CONTAINER end-->

                <?php
                if ( $header_class != "" ) {
                    if ( $admin == 0 ) {
                        ?>
                        <div class="sec_header landing <?php echo (is_mobile_new() && !is_user_logged_in()) ? 'mob' : '' ?>">
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
                        <?php
                    } else {
                        $fundraiser_end   = new Fundraiser_Ended( $post_id );
                        $ended_fundraiser = $fundraiser_end->check_end();
                        $invite_donor_url = (!$ended_fundraiser) ? get_bloginfo( 'url' ) . "/invite-start/?fundraiser_id=" . $post_id . "&type=permalink" : "#";
                        ?>

                        <script src="https://apis.google.com/js/client:plusone.js"></script>
                        <script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/cors_upload.js?ts=<?php echo time() ?>"></script>
                        <script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/upload_video.js?ts=<?php echo time() ?>"></script>
                        <script type="text/javascript"
                        src="<?php bloginfo( 'template_directory' ); ?>/assets/js/fundraiser.js?ts=<?php echo time() ?>"></script>

                        <?php if($page_name == "page.php") {?>
                        <script src="https://code.highcharts.com/highcharts.js"></script>
                        <script src="https://code.highcharts.com/highcharts-more.js"></script>
                        <script src="https://code.highcharts.com/modules/exporting.js"></script>
                        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/meter.js?ts=<?php echo time(); ?>"></script>
                        <?php } ?>
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
                                        <a href="/my-account?duplicate_f_id=<?php echo $post_id; ?>"
                                           class="duplicate_fundraiser">Duplicate Fundraiser</a>
                                    </li>
                                    <li>
                                        <a href="<?php the_permalink( $post_id ); ?>?print_inst=true"
                                           target="_blank" class="">Print participant instructions</a>
                                    </li>
                                    <li>
                                        <a href="<?php the_permalink( $post_id ); ?>?print_parent=true"
                                           target="_blank" class="">Print Parent Letter</a>
                                    </li>
                                    <li class="border_0">
                                        <a href="#" class="invite_admin <?php echo ($ended_fundraiser) ? 'no-event' : ''; ?>">Invite admin</a>
                                    </li>
                                    <li>
                                        <!--                            invite_donors-->
                                        <a class="fancyboxInvite participant_invite <?php echo ($ended_fundraiser) ? 'no-event' : ''; ?>"
                                           data-fancybox-type="iframe"
                                           href="<?php echo $invite_donor_url; ?>&uid=<?php echo $uid ?>">
                                            invite donors
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php the_permalink( $post_id ); ?>?report=true">run report</a>
                                    </li>
                                </ul>

                            </div>

                        </div>
                    <?php } ?>
                <?php } ?>

            </header>


            <!--PAST FUNDRAISER POPUP end-->
            <!--HEADER end-->

            <!--Login and SignUp popup modal -->
            <?php get_template_part( 'template-parts/login_popup' ); ?>
            <?php get_template_part( 'template-parts/signup_popup' ); ?>
            <?php get_template_part( 'template-parts/forgotpassword_popup' ); ?>
            <!--Login and SignUp popup modal -->

            <?php if ( is_user_logged_in() ) { ?>
                <!--    CreateFundariser popup modal    -->
                <?php get_template_part( 'template-parts/createfund_popup' ); ?>

                <?php
                if ( $admin == 1 ) {
                    //   EditFundariser popup modal    -->
                    get_template_part( 'template-parts/editfund_popup' );
                    //   inviteAdmin popup modal    -->
                    get_template_part( 'template-parts/inviteAdmin_popup' );
                }
                ?>
            <?php } ?>
