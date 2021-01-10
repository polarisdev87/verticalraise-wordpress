<?php
ob_start();

use classes\app\cookie\Check_Cookie;

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
        <meta name="robots" content="follow">

        <title>Vertical Raise. Elevate your programs fundraising to new heights.</title>
        <!-- SET: FAVICON -->
        <link rel="shortcut icon" type="image/png" sizes="4x4"
              href="<?php bloginfo('template_directory'); ?>/assets/images/favicon.png">
        <!-- END: FAVICON -->

        <!-- SET: FONTS -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900" rel="stylesheet">
        <!-- END: FONTS -->

        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo('template_directory'); ?>/assets/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/assets/css/icon-font.min.css">
        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo('template_directory'); ?>/assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo('template_directory'); ?>/assets/css/style.css?ts=<?php echo time() ?>">
        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo('template_directory'); ?>/assets/css/responsive.css?ts=<?php echo time() ?>">
        <link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/assets/css/skins/all.css"/>

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
                <div class="container clearfix">
                    <!--LOGO START-->
                    <div class="logo <?php echo $logo_class ?>">
                        <a href="<?php echo get_bloginfo('url') ?>" title="VERTICALRAISE">
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

                        <?php if (!is_user_logged_in()) { ?>
                            <ul>
                                <li>
                                    <a href="<?php bloginfo('url'); ?>/">Home</a>
                                </li>
                                <li>
                                    <a class="loginLink" href="#login">Login</a>
                                </li>
                                <li>
                                    <a class="signupLink" href="#signup">Sign-up</a>
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
                                    <div class="mega_menu">
                                        <div class=" clearfix">
                                            <div class="menu_content">
                                                <div class="row">
                                                    <div class="col col_left">
                                                        <?php echo get_avatar($user_ID, 170); ?>
                                                    </div>
                                                    <div class="col col_right">
                                                        <h4><?php echo $user_info->first_name . " " . $user_info->last_name; ?></h4>
                                                        <ul>
                                                            <li>
                                                                <a href="<?php bloginfo('url'); ?>/my-account/">my
                                                                    account</a>
                                                            </li>
                                                            <li>
                                                                <a href="<?php bloginfo('url'); ?>/edit-profile/">edit
                                                                    profile</a>
                                                            </li>
                                                            <li>
                                                                <a href="<?php bloginfo('url'); ?>/my-account/"
                                                                   class="join_fundraiser">join a fundraiser(s)</a>
                                                            </li>
                                                            <li>
                                                                <a href="#" class="create_fundraiser">create a fundraiser</a>
                                                            </li>
                                                            <li>
                                                                <a href="<?php bloginfo('url'); ?>/my-account/"
                                                                   class="current_fundraiser">current fundraisers</a>
                                                            </li>
                                                            <li>
                                                                <a href="<?php bloginfo('url'); ?>/my-account/"
                                                                   class="upcoming_fundraiser">upcoming fundraisers</a>
                                                            </li>
                                                            <li>
                                                                <a href="<?php echo wp_logout_url(get_bloginfo('url')); ?>">logout</a>
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
                                    <a href="<?php echo wp_logout_url(get_bloginfo('url')); ?>">Log Out</a>
                                </li>
                            </ul>
                        <?php } ?>
                    </div>
                    <!--RIGHT HEADER end-->
                </div>
                <!--CONTAINER end-->

            </header>


            <!--Login and SignUp popup modal -->
            <?php get_template_part('template-parts/login_popup'); ?>
            <?php get_template_part('template-parts/signup_popup'); ?>
            <?php get_template_part('template-parts/forgotpassword_popup'); ?>

            <!--MAIN start-->
            <main>
                <!--SECTION1 start-->
                <div class="section1">
                    <!--BANNER start-->
                    <div class="banner home_banner">
                        <!--CONTAINER start-->
                        <div class="container">
                            <div class="banner_content">
                                <h1>Elevate your program's Online <br>
                                    Fundraising to new heights</h1>
                                <p>Use our online fundraising system and raise more money with less time</p>
                                <div class="get_started">
                                    <span class="display_table"><a class="link signupLink" href="#">Get Started now</a></span>
                                </div>
                            </div>
                        </div>
                        <!--CONTAINER end-->
                    </div>
                    <!--BANNER end-->

                    <!--WHAT MAKES VERTICAL BATTER start-->
                    <div class="makes_vertical_bettter">
                        <!--CONTAINER start-->
                        <div class="container">
                            <div class="title">
                                <h2>What makes vertical better?</h2>
                            </div>
                            <p>Vertical Raise is the premier online donation platform. We utilize email, text messaging and
                                social media campaigns to exponentially increase the reach of your fundraiser. The foundation of
                                our company’s success is built upon our best-in-class email deliverability, detailed tracking
                                and the complete personalization of every page. Combining this proprietary system, with
                                experienced representatives has made us the most effective donation platform available. We
                                look forward to the opportunity in helping elevate your program and you becoming part of the
                                Vertical family. Rise up!</p>
                        </div>
                        <!--CONTAINER end-->
                    </div>
                    <!--WHAT MAKES VERTICAL BATTER end-->
                </div>
                <!--SECTION1 end-->

                <!--HOW IT WORKS start-->
                <div class="how_works">
                    <!--CONTAINER start-->
                    <div class="container">
                        <div class="title">
                            <h2>How it works</h2>
                        </div>

                        <div class="row">
                            <div class="col-md-4 col-sm-4 col-xs-12 col">
                                <div class="icon">
                                    <b>
                                        <img src="<?php bloginfo('template_directory'); ?>/assets/images/work1.png" alt="">
                                    </b>
                                </div>
                                <h3>1. Launch Fundraiser</h3>
                                <p>After we create the teams fundraising page, your fundraising director will be on site to help
                                    launch the campaign. We will assist each participant in joining the fundraiser and use the
                                    invite process to share the campaign with potential supporters via social media, text and
                                    email.</p>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 col">
                                <div class="icon">
                                    <b>
                                        <img src="<?php bloginfo('template_directory'); ?>/assets/images/work2.png" alt="">
                                    </b>
                                </div>
                                <h3>2. Monitor Participation</h3>
                                <p>Track your progress and participation with Vertical Raise’s user friendly admin dashboard.
                                    The dashboard tracks levels of participation including the number of email invites sent,
                                    total dollars raised and social media shares.</p>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 col">
                                <div class="icon">
                                    <b>
                                        <img src="<?php bloginfo('template_directory'); ?>/assets/images/work3.png" alt="">
                                    </b>
                                </div>
                                <h3>3. Expedited Deposit of Funds</h3>
                                <p>Funds will be delivered to you in a timely manner via check or direct deposit. Our goal is
                                    get your team the funds as soon as possible so you can have a successful season. All funds
                                    are secured using the worlds leading ecommerce platform and our 128-bit encrypted site.</p>
                            </div>
                        </div>

                        <div class="get_started">
                            <span class="display_table"><a href="#"
                                                           class="link signupLink">GET STARTED now<span>→</span></a></span>
                        </div>
                    </div>
                    <!--CONTAINER end-->
                </div>
                <!--HOW IT WORKS end-->


                <?php get_template_part( 'template-parts/marketing/real_fundraisers_section' ); ?>


                <?php get_template_part( 'template-parts/marketing/review_section' ); ?>

                <!--HAVE QUESTION start-->
                <div class="have_question">

                    <!--CONTAINER start-->
                    <div class="container">
                        <div class="title">
                            <h2>Have Questions or<br> want to get started?</h2>
                        </div>
                        <?php // echo do_shortcode('[contact-form-7 id="53511" title="Contact us Home"]'); ?>
                        <div class="btn_row">
                            <a href="/contact-us/" class="link contactus_link">CONTACT US</a>
                        </div>

                    </div>
                    <!--CONTAINER end-->
                </div>
                <!--HAVE QUESTION end-->
            </main>
            <!--MAIN end-->

            <footer>
                <!--CONTAINER start-->
                <div class="container">

                    <div class="footer_logo">
                        <a href="#">
                            <?php if (is_mobile_new()) { ?>
                                <img src="<?php bloginfo('template_directory'); ?>/assets/images/mobile-footer-logo.png" alt="">
                            <?php } else { ?>
                                <img src="<?php bloginfo('template_directory'); ?>/assets/images/footer-logo.png" alt="">
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
                                <div id="pagelinks">
                                    <a href="https://verticalraise.com/bandfundraising/">Bands Fundraising</a>
                                    <a href="https://verticalraise.com/teamfundraising/">Teams Fundraising</a>
                                    <a href="https://verticalraise.com/volleyballfundraising/">Volleyball Fundraising</a>
                                </div>
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
            var intervalRef = null;

            $(document).ready(function () {

                var signupFlag = '<?php echo $popupType ?>';

                if (signupFlag == 'signup') {
                    $("#signup_model").modal('show')
                } else if (signupFlag == 'login') {
                    $("#login_model").modal('show')
                }

                /*
                 $('#signupForm input[name=reg_email]').on('blur', function () {
                 email_verification($(this));
                 })*/
                $('#signupForm input[name=reg_email]').on('keyup', function () {
                    email_checker($(this));
                });
                $('#signupForm #suggestion').on('click', 'span', function () {
                    // On click, fill in the field with the suggestion and remove the hint
                    $('#signupForm input[name=reg_email]').val($(this).text());
                    $('#signupForm #suggestion').fadeOut(200, function () {
                        $(this).empty();
                        //email_verification($('#signupForm input[name=reg_email]'));
                    });
                });



            });

            /*
             function email_verification(emailObj) {
             var email = emailObj.val();
             if (email.length > 0) {
             $("#signupForm .tc-result").show();
             } else {
             $("#signupForm .tc-result").hide();
             }
             $.get("https://api.thechecker.co/v1/verify?email=" + email + "&api_key=<?php echo _THE_CHECKER_API_KEY ?>", function (data, status) {
             if (status != 'success') {
             return false;
             } else {

             var allowed_result = ['deliverable', 'risky', 'unknown'];
             if (allowed_result.indexOf(data.result) > -1) {
             $("#signupForm .tc-result-icon").attr("src", TEMP_DIRECTORY + "/assets/images/success.png");
             //                            $('#signupForm #invalid').empty();
             emailObj.data("validEmail", "valid");
             } else {
             $("#signupForm .tc-result-icon").attr("src", TEMP_DIRECTORY + "/assets/images/error.png");
             //                            $('#signupForm #invalid').text("This email is not valid.");
             emailObj.data("validEmail", "invalid");
             }
             $("#signupForm").validate().element("#e1");
             }
             });
             }*/

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

    </body>
</html>
