<?php
ob_start();

use classes\app\fundraiser\Fundraiser_Media;       //Fundraiser Media Class Object

// 3 Critical Variables used around the site
$fundraiser_id = (!empty( $_GET['fundraiser_id'] ) ) ? $_GET['fundraiser_id'] : '';
$display_type  = (!empty( $_GET['display_type'] ) ) ? $_GET['display_type'] : '';
$uid           = ( isset( $_GET['uid'] ) ) ? $_GET['uid'] : '';

global $template;
$page_name   = basename( $template );
$return_url  = '';
$return_type = '';

if ( isset( $_GET['action'] ) && $_GET['action'] != '' ) {

    //simple spread invite in landing page by clicking "text" and "email" button.
    $return_type = "SimpleSpread";
} else {

    // When to use the 'done' js close links
    $count  = 0;
    $append = '';
    if ( isset( $_GET['fundraiser_id'] ) ) {
        $count++;
        if ( $count == 1 ) {
            $append = '?';
        } else {
            $append .= '&';
        }
        $append .= "fundraiser_id={$_GET['fundraiser_id']}";
    }

    if ( isset( $_GET['uid'] ) ) {
        $count++;
        if ( $count == 1 ) {
            $append = '?';
        } else {
            $append .= '&';
        }
        $append .= "uid={$_GET['uid']}";
    }
    // additional parameter in Thank you page popup
    if ( isset( $_GET['fname'] ) ) {
        $count++;
        if ( $count == 1 ) {
            $append = '?';
        } else {
            $append .= '&';
        }
        $append .= "fname={$_GET['fname']}";
    }

    if ( isset( $_GET['lname'] ) ) {
        $count++;
        if ( $count == 1 ) {
            $append = '?';
        } else {
            $append .= '&';
        }
        $append .= "lname={$_GET['lname']}";
    }
    if ( isset( $_GET['email'] ) ) {
        $count++;
        if ( $count == 1 ) {
            $append = '?';
        } else {
            $append .= '&';
        }
        $append .= "email={$_GET['email']}";
    }
    if ( isset( $_GET['media'] ) ) {
        $count++;
        if ( $count == 1 ) {
            $append = '?';
        } else {
            $append .= '&';
        }
        $append .= "media={$_GET['media']}";
    }

    if ( isset( $_GET['display_type'] ) && $_GET['display_type'] == 'single' ) {
        //thank you page popup
        $return_type = 'thankyou';
        $return_url  = "/thank-you-payment" . $append;
    } else {
//        participant and admin popup
        if ( isset( $_GET['type'] ) && $_GET['type'] == 'participant' ) {

            $return_type = 'participant';
            $return_url  = "/participant-fundraiser" . $append;
        } elseif ( isset( $_GET['type'] ) && $_GET['type'] == 'admin' ) {

            $return_type = 'admin';
            $return_url  = "/single-fundraiser" . $append;
        }

        if ( is_user_logged_in() ) {
            if ( current_user_can( 'administrator' ) ) {
                $return_type = 'permalink';
                $return_url  = get_permalink( $fundraiser_id );
            }
        }
    }


    if ( isset( $_GET['parent'] ) && $_GET['parent'] == '1' ) {
        $return_type = 'parent';
        $permalink   = get_permalink( $fundraiser_id );
        $return_url  = $permalink . "sms/" . $uid;
    }
//
//    $return_type = 'participant';
//    $return_url = "/participant-fundraiser/?fundraiser_id=" . $_GET['fundraiser_id'];
}

//var_dump ($return_url);exit;
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
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE10,chrome=1"/>
        <!-- Responsive and mobile friendly stuff -->
        <!--    <meta name="HandheldFriendly" content="True">-->
        <!--    <meta name="MobileOptimized" content="320">    -->
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
        <link rel="stylesheet" href="<?php bloginfo ( "template_url" ); ?>/assets/css/vendor/jquery.Jcrop.css">

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
              href="<?php bloginfo( 'template_directory' ); ?>/assets/sass/invite-parent.css?ts=<?php echo time() ?>">
        <!-- END: STYLESHEET -->


        <?php
        $fundraise_mediaObj = new Fundraiser_Media();
        $image_url          = $fundraise_mediaObj->get_fundraiser_logo( $fundraiser_id );
        ?>
        <meta property="og:url" content="<?php echo get_the_permalink( $fundraiser_id ); ?>"/>
        <meta property="og:type" content="article"/>
        <meta property="og:title" content="Click here to support the <?php echo get_the_title( $fundraiser_id ); ?>"/>
        <meta property="og:description" content='<?php echo get_post_meta( $fundraiser_id, 'campaign_msg', true ); ?>'/>
        <meta property="og:site_name" content="<?php echo get_bloginfo( 'name' ); ?>"/>
        <meta property="og:image" content="<?php echo $image_url; ?>"/>
        <meta property="og:image:width" content="400"/>
        <meta property="og:image:height" content="400"/>

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Click here to support the <?php echo get_the_title( $fundraiser_id ); ?>">
        <meta name="twitter:description" content="<?php echo get_post_meta( $fundraiser_id, 'campaign_msg', true ); ?>">
        <meta name="twitter:image" content="<?php echo $image_url; ?>">

        <meta property='fb:app_id' content='<?php echo _FACEBOOK_APP_ID ?>'/>

        <script>
            // Global Ajax Variable
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


        <script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.fancybox.pack.js"></script>


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
        <script type="text/javascript"
        src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.buttonLoader.js?ts=<?php echo time() ?>"></script>


        <script type="text/javascript"
        src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.form.min.js"></script>

        <meta property='fb:app_id' content='<?php echo _FACEBOOK_APP_ID ?>'/>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/mailcheck.js?ts=<?php echo time() ?>"></script>
        <script type="text/javascript"
        src="<?php bloginfo( 'template_directory' ); ?>/assets/js/custom.js?ts=<?php echo time() ?>"></script>
        <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.steps.min.js"></script>
        <script src="<?php bloginfo ( "template_url" ); ?>/assets/js/avatar/vendor/jquery.Jcrop.js"></script>

        <script>
            var return_type = '<?php echo $return_type ?>'

            $(document).ready(function () {

<?php if ( is_mobile() ) { ?>
                    $("#invite_body #invite_step .close").on('click', function () {

                        if ( return_type == 'SimpleSpread' ) {
                            window.history.back();
                        } else if ( return_type == 'admin' || return_type == 'participant' || return_type == 'parent' || return_type == 'permalink' ) {
                            window.location.href = '<?php echo $return_url ?>'
                        } else if ( return_type == 'thankyou' ) {
                            window.location.href = '<?php echo $return_url ?>&spreadClose=1';
                        }
                    });

<?php } ?>
            });
        </script>


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
        ?>

        <?php

        // Display Google Analytics
        use \classes\app\google_analytics\GoogleAnalytics;

        $GoogleAnalytics = new GoogleAnalytics();
        echo $GoogleAnalytics->display();
        ?>

    </head>
    <?php
    if ( is_mobile_new() ) {
        $scroll = 'scroll';
    } else {
        $scroll = 'hidden';
        if ( $page_name == 'page-invite-parent-start.php' ) {
            $scroll = 'scroll';
        }
    }
    ?>
    <body <?php body_class(); ?> id="invite_body" style="overflow-y: <?php echo $scroll ?>">

        <div class="wrapper">
            <noscript>
            <div class="noscript_div" >
                <span style="color:red">JavaScript is not enabled in this browser!</span>
            </div>
            </noscript>

