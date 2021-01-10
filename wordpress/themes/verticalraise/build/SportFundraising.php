<?php

/* Template Name: Sport Fundraising  */

ob_start();

use classes\app\cookie\Check_Cookie;
use classes\app\facebook\Pixel;

if (is_user_logged_in()) {
    global $user_ID;
    $uid = $user_ID;
    $user_info = get_userdata($user_ID);
    $first_login = get_user_meta($uid, 'first_login', true);
} else {
    $uid = (!empty($_GET['uid']) ) ? $_GET['uid'] : 0;
}

global $template;

$page_name = basename($template);
$return = '';
$return_type = '';

$base_url = get_template_directory_uri();
$header_class = "home_header";
$logo_img = $base_url . "/assets/images/logo.png";
$logo_class = "";

$popupType = '';
if (!is_user_logged_in()) {
    if (isset($_GET['signup'])) {
        $popupType = 'signup';
    }
    if (isset($_GET['login'])) {
        $popupType = 'login';
    }
}
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
        <meta charset="<?php bloginfo('charset'); ?>"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
        <!-- Responsive and mobile friendly stuff -->
        <!--    <meta name="HandheldFriendly" content="True">-->
        <!--    <meta name="MobileOptimized" content="320">-->
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">

        <title>VerticalRaise - <?php echo get_field( "title" ); ?></title>
        <!-- SET: FAVICON -->
        <link rel="shortcut icon" type="image/png" sizes="4x4"
              href="<?php bloginfo('template_directory'); ?>/assets/images/favicon.png">
        <!-- END: FAVICON -->
        <meta name =”robots” content=”index”>
        <meta name="description" content="High School <?php echo get_field( "type_of_team_sport" ); ?> fundraisers and Fundraising"/>
        <link rel="canonical" href="https://verticalraise.com/teamfundraising/" />
        <meta property="og:locale" content="en_US" />
        <meta property="og:type" content="article" />
        <meta property="og:title" content="<?php echo get_field( "type_of_team_sport" ); ?> Fundraising - Vertical Raise" />
        <meta property="og:description" content="High School <?php echo get_field( "type_of_team_sport" ); ?> fundraisers and Fundraising" />
        <meta property="og:url" content="https://verticalraise.com/teamfundraising/" />
        <meta property="og:site_name" content="Vertical Raise" />
        <meta property="article:publisher" content="https://www.facebook.com/VerticalRaise/" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:description" content="High School <?php echo get_field( "type_of_team_sport" ); ?> fundraisers and Fundraising" />
        <meta name="twitter:title" content="<?php echo get_field( "type_of_team_sport" ); ?> Fundraising - Vertical Raise" />
        <meta name="twitter:site" content="@VerticalRaise" />
        <meta name="twitter:creator" content="@VerticalRaise" />
        <script type='application/ld+json' class='yoast-schema-graph yoast-schema-graph--main'>{
                "@context": "https://schema.org",
                "@graph": [
                    {
                        "@type": "Organization",
                        "@id": "https://verticalraise.com/#organization",
                        "name": "VerticalRaise",
                        "url": "https://verticalraise.com/",
                        "sameAs": [
                            "https://www.facebook.com/VerticalRaise/",
                            "https://www.instagram.com/verticalraise/",
                            "https://www.linkedin.com/company/18562617/",
                            "https://twitter.com/VerticalRaise"
                        ],
                        "logo": {
                            "@type": "ImageObject",
                            "@id": "https://verticalraise.com/#logo",
                            "url": "https://verticalraise.com/wp-content/themes/VR/assets/images/logo2.png",
                            "width": 84,
                            "height": 98,
                            "caption": "VerticalRaise"
                        },
                        "image": {
                            "@id": "https://verticalraise.com/#logo"
                        }
                    },
                    {
                        "@type": "WebSite",
                        "@id": "https://verticalraise.com/#website",
                        "url": "https://verticalraise.com/",
                        "name": "Vertical Raise",
                        "publisher": {
                            "@id": "https://verticalraise.com/#organization"
                        },
                        "potentialAction": {
                            "@type": "SearchAction",
                            "target": "https://verticalraise.com/?s={search_term_string}",
                            "query-input": "required name=search_term_string"
                        }
                    },
                    {
                        "@type": "WebPage",
                        "@id": "https://verticalraise.com/teamfundraising/#webpage",
                        "url": "https://verticalraise.com/teamfundraising/",
                        "inLanguage": "en-US",
                        "name": "Band Fundraising - Vertical Raise",
                        "isPartOf": {
                            "@id": "https://verticalraise.com/#website"
                        },
                        "datePublished": "2019-05-29T22:24:08+00:00",
                        "dateModified": "2019-07-31T22:12:37+00:00",
                        "description": "High School Teams and Groups fundraisers and Fundraising"
                    }
                ]
            }</script>
        <!-- SET: FONTS -->
        <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400" rel="stylesheet">
        <!-- END: FONTS -->

        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo('template_directory'); ?>/assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo('template_directory'); ?>/assets/css/style.css?ts=<?php echo time() ?>">
        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo('template_directory'); ?>/assets/css/responsive.css?ts=<?php echo time() ?>">




        <?php
        wp_head();

        // Display Google Analytics
        use \classes\app\google_analytics\GoogleAnalytics;

        $GoogleAnalytics = new GoogleAnalytics();
        echo $GoogleAnalytics->display();
        ?>

    </head>

    <body <?php body_class(); ?>>

        <div class="wrapper">
            <!--HEADER start-->
            <header class="<?php echo $header_class; ?>">
                <noscript>
                <div class="noscript_div" >
                    <span style="color:red">JavaScript is not enabled in this browser!</span>
                </div>
                </noscript>
                <!--CONTAINER start-->
                <div class="container clearfix logo_container">
                    <!--LOGO START-->
                    <div class="logo <?php echo $logo_class ?>">
                        <a href="<?php echo get_bloginfo('url') ?>" title="VERTICALRAISE">
                            <img alt="band fundraisers" class="band-fundraiser-logo" src="<?php echo get_template_directory_uri() . "/assets/images/logo.png" ?>" alt="">
                        </a>
                    </div>
                    <!--LOGO END-->

                </div>
                <!--CONTAINER end-->

            </header>


            <!--Login and SignUp popup modal -->
            <?php get_template_part('template-parts/login_popup'); ?>
            <?php get_template_part('template-parts/signup_popup'); ?>
            <?php get_template_part('template-parts/forgotpassword_popup'); ?>
            <!--MAIN start-->
            <main>

                <?php get_template_part( 'template-parts/marketing/we_raise_section' ); ?>

                <?php get_template_part( 'template-parts/marketing/gradient_section' ); ?>

                <?php get_template_part( 'template-parts/marketing/real_fundraisers_section' ); ?>

                <?php get_template_part( 'template-parts/marketing/review_section' ); ?>

            </main>
            <!--MAIN end-->

            <footer>
                <!--CONTAINER start-->
                <div class="container">

                    <div class="footer_logo">
                        <a href="#">
                            <?php if (is_mobile_new()) { ?>
                                <img alt="band fundraising" alt="band fundraisers" src="<?php bloginfo('template_directory'); ?>/assets/images/mobile-footer-logo.png" alt="">
                            <?php } else { ?>
                                <img alt="band fundraiser" src="<?php bloginfo('template_directory'); ?>/assets/images/footer-logo.png" alt="">
                            <?php } ?>
                        </a>
                    </div>
                    <div class="soc_icons">
                        <ul>
                            <li>
                                <a class="google_link soc_link" href="<?php echo _SOCIAL_MEDIA_GOOGLE_PLUS_URL ?>" target="_blank" >
                                </a>
                            </li>
                            <li>
                                <a class="instagram_link soc_link" href="<?php echo _SOCIAL_MEDIA_INSTAGRAM_URL ?>" target="_blank">
                                </a>
                            </li>
                            <li>
                                <a class="facebook_link soc_link" href="<?php echo _SOCIAL_MEDIA_FACEBOOK_PAGE_URL ?>" target="_blank">
                                </a>
                            </li>
                            <li>
                                <a class="linkedin_link soc_link" href="<?php echo _SOCIAL_MEDIA_LINKEDIN_URL ?>" target="_blank">
                                </a>
                            </li>
                            <li>
                                <a class="twitter_link soc_link" href="<?php echo _SOCIAL_MEDIA_TWITTER_URL ?>" target="_blank">
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="page_links">
                        <?php if (!is_user_logged_in()) { ?>
                            <ul>
                                <li>
                                    <a href="<?php bloginfo('url'); ?>/">Home</a>
                                </li>
                                <li>
                                    <a class="loginLink" href="#">Login</a>
                                </li>
                                <li>
                                    <a class="signupLink" href="#">Sign-up</a>
                                </li>
                                <li>
                                    <a href="<?php bloginfo('url'); ?>/contact-us/">Contact Us</a>
                                </li>
                                <li>
                                    <a href="<?php bloginfo('url'); ?>/careers/">Careers</a>
                                </li>
                            </ul>
                        <?php } else { ?>
                            <ul>
                                <li>
                                    <a href="<?php bloginfo('url'); ?>/">Home</a>
                                </li>
                                <li>
                                    <a href="<?php bloginfo('url'); ?>/contact-us/">Contact Us</a>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="<?php bloginfo('url'); ?>/my-account/">My Account</a>

                                </li>
                                <li>
                                    <a href="<?php echo wp_logout_url(get_bloginfo('url')); ?>">Log Out</a>
                                </li>
                            </ul>
                        <?php } ?>
                    </div>


                    <div class="sec_footer">
                        <p>Copyright &copy; <?php echo date('Y') ?> vertical raise. all rights reserved.</p>
                        <ul>
                            <li>
                                <a href="<?php echo get_the_permalink(157); ?>"  target="_blank">Terms</a>
                            </li>
                            <li>
                                <a href="<?php echo get_the_permalink(379); ?>"  target="_blank">Privacy policy</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!--CONTAINER end-->
            </footer>
            <!--FOOTER end-->
        </div>


        <!--WRAPPER end-->
        <?php
        $checkcookie = new Check_Cookie();
        $checkcookie->display();
        wp_footer();
        ?>
        <script>
            // Global Ajax Variable
            var LoginAjaxUrl = '<?php bloginfo('url') ?>';
            var ismobile = '<?php echo ( is_mobile_new() ) ? '1' : '0' ?>';
            var TEMP_DIRECTORY = '<?php bloginfo('template_directory'); ?>';
        </script>
        <!-- SET: SCRIPTS -->
        <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/assets/js/jquery-1.12.4.min.js"></script>
        <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/assets/js/jquery.matchHeight.js"></script>
        <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/assets/js/bootstrap.min.js"></script>

        <!-- JQuery Validate -->
        <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/assets/js/jquery.validate.min.js"></script>

        <!--    icheckbox -->

        <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/assets/js/icheck.js"></script>
        <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/assets/js/form-icheck.js"></script>


        <script type="text/javascript">

            $(document).ready(function () {
                setInterval(function () {
                    let defaultMessage = 'Get more info →';
                    let newMessage = 'Get more info';

                    let visible = $('span.ajax-loader').css('visibility');
                    let currentMessage = $('input.wpcf7-submit').val();

                    if ( visible == "visible" && currentMessage == defaultMessage ) {
                        $('input.wpcf7-submit').val(newMessage)
                    } else if( visible != "visible" && currentMessage == newMessage ){
                        $('input.wpcf7-submit').val(defaultMessage)
                    }
                }, 200);

                $("#get_more_info").click(function (e) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: $("#interested_p").offset().top - 75
                    }, 1000);
                });



                document.addEventListener( 'wpcf7mailsent', function( event ) {
                    let email = false, name = false, phone = false;
                    try{
                        email = event.detail.formData.get('email-field-bf');
                        name = event.detail.formData.get('name-field-bf');
                        phone = event.detail.formData.get('phone-bf');
                    }catch (e) {

                    }
                    gtag('event', 'conversion', {
                            'send_to': '<?php echo _GOOGLE_AWC_CODE2; ?>',
                            'phone' : phone,
                            'name' : name,
                            'email' : email,
                        'event_callback': function() {
                                console.log('Sent!!');
                            }
                    });

                    ga('send', 'event', 'contact-form', 'submit');

                }, false );

                var signupFlag = '<?php echo $popupType ?>';

                if (signupFlag == 'signup') {
                    $("#signup_model").modal('show')
                } else if (signupFlag == 'login') {
                    $("#login_model").modal('show')
                }

                $('#signupForm input[name=reg_email]').on('keyup', function () {
                    email_checker($(this));
                });
                $('#signupForm #suggestion').on('click', 'span', function () {
                    // On click, fill in the field with the suggestion and remove the hint
                    $('#signupForm input[name=reg_email]').val($(this).text());
                    $('#signupForm #suggestion').fadeOut(200, function () {
                        $(this).empty();
                    });
                });
            });



            function email_checker(emailObj) {
                $('#signupForm #invalid').empty();
                var topLevelDomains = ["com", "net", "org"];
                emailObj.mailcheck({
                    topLevelDomains: topLevelDomains,
                    suggested: function (element, suggestion) {
                        $('#signupForm #suggestion').fadeIn(200);
                        $('#signupForm #suggestion').html("Did you mean <span >" + suggestion.full + "</span> ?");
                    },
                    empty: function (element) {
                        $("#signupForm #suggestion").empty();
                    }
                });
            }
        </script>
        <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/assets/js/custom-index.js?ts=<?php echo time() ?>"></script>
        <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/assets/js/mailcheck.js?ts=<?php echo time() ?>"></script>
        <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/assets/js/auth.js?ts=<?php echo time() ?>"></script>

        <?php echo Pixel::get_tracking_code(); ?>

    </body>
</html>
