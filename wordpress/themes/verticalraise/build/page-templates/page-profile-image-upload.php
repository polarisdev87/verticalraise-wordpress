<?php /* Template Name: Profile Image Upload */


if ( is_user_logged_in () ) {

    global $user_ID;

    $user_info       = get_userdata ( $user_ID );
    $upload_dir      = wp_upload_dir ();
    $upload_path_rel = $upload_dir['basedir'] . '/profile_img_thumb/';
    $upload_path     = get_bloginfo ( 'url' ) . '/wp-content/uploads/profile_img_thumb/';

    $return_url = '';
    if ( isset( $_GET['return'] ) ) {
        switch ( $_GET['return'] ) {
            case 'participant' :
                $return_url =  get_site_url () .'/participant-fundraiser/?fundraiser_id=' . $_GET['f_id'];
                break;
            case 'my-account':
                $return_url =  get_site_url () .'/my-account';
                break;
            case 'edit-profile':
                $return_url =  get_site_url () .'/edit-profile';
                break;
            case 'permalink':
                $return_url = get_permalink ( $_GET['f_id'] );
                break;
        }
    }

    ?>
    <html class="profile_upload_html" style="<?php if ( is_mobile () ) { ?>overflow:scroll !important;<?php } ?>">
    <head>
        <?php wp_head (); ?>

        <meta charset="<?php bloginfo ( 'charset' ); ?>"/>
        <!-- Responsive and mobile friendly stuff -->
        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <link rel="stylesheet" href="<?php bloginfo ( "template_url" ); ?>/assets/css/vendor/jquery.Jcrop.css">
        <!-- SET: FONTS -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900" rel="stylesheet">
        <!-- END: FONTS -->

        <!-- SET: STYLESHEET -->
        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo ( 'template_directory' ); ?>/assets/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?php bloginfo ( 'template_directory' ); ?>/assets/css/icon-font.min.css">
        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo ( 'template_directory' ); ?>/assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo ( 'template_directory' ); ?>/assets/css/jquery.mCustomScrollbar.min.css">
        <link rel="stylesheet" type="text/css" href="<?php bloginfo ( 'template_directory' ); ?>/assets/css/style.css?ts=<?php echo time ()?>">
        <link rel="stylesheet" type="text/css"
              href="<?php bloginfo ( 'template_directory' ); ?>/assets/css/responsive.css?ts=<?php echo time ()?>">
        <script type="text/javascript"
                src="<?php bloginfo ( 'template_directory' ); ?>/assets/js/jquery-1.12.4.min.js"></script>
        <!-- END: STYLESHEET -->

        <script>
            $(document).ready(function () {
                $("#file-input").change(function () {
                    readUrl(this);
                });
            })
            function readUrl(input) {
                var filename = input.value.replace("C:\\fakepath\\", "")
                $(".selFileName").text(filename);
            }

            // After uploaded image and then redirect to original page
            function reload_original_page() {
                setTimeout(function () {
                    location.href = $("#success-screen #close").attr("href");
                }, 1000);
            }
        </script>


    </head>
    <body class="iframe_imageUpload">
    <div class="mobile_popup_fix" id="profile_image_upload">
        <input type="hidden" value="profile_image" id="upload_type" />
        <div id="title">

            <!--img src="<?php bloginfo ( 'template_directory' ); ?>/assets/images/icon1.png" alt=""-->
            <h1 class="modal-title"><?php the_title (); ?></h1>
        </div>

        <?php if ( !is_mobile_new () ) { ?>
            <a type="button" class="close upload_close" href="javascript:parent.$.fancybox.close();">
                <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/close-btn1.png"
                     alt="">
            </a>
        <?php } else { ?>
            <a type="button" class="close upload_close" href="<?php echo $return_url ?>">
                <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/close-btn1.png"
                     alt="">
            </a>

        <?php } ?>

        <div id="upload-screen">
            <p>*Please select and crop your profile image below*</p>

            <span class="btn green fileinput-button">
                <span> Select Photo </span>
                <input type="file" id="file-input">
            </span>

            <p><span class="selFileName">No selected</span></p>

            <?php if ( wp_is_mobile () == false ) { ?>
                <p>Or <strong>drag &amp; drop</strong> an image file onto this webpage.</p>
            <?php } ?>
            <div id="result" class="result">
                <p>This demo works only in browsers with support for the <a
                            href="https://developer.mozilla.org/en/DOM/window.URL">URL</a> or <a
                            href="https://developer.mozilla.org/en/DOM/FileReader">FileReader</a> API.</p>
            </div>

            <br>
            <div id="upload-button" style="display: none;">
                <form action='upload.php' method="post" enctype="multipart/form-data" id="upload">
                    <a href="#" id="crop" class="custom_button upload_btn">Upload</a>
                </form>
            </div>
        </div>
        <div id="loading" style="display: none; text-align: center;">
            <div id="profile-pic-loader">
                <center>Uploading ...</center>
                <br>
                <img src="<?php bloginfo ( "template_url" ); ?>/assets/images/bx_loader.gif"
                     style="margin: 0 auto; margin-bottom:20px;">
            </div>
            <div class="progress" style="width:200px; margin:0 auto;height:inherit">
                <div class="bar"
                     style="width: 200px; background: white;">
                    <div id="progressBar"
                         style="background: #52B6D5; width: 0%; color: white; border: none; height: 30px;line-height: 30px;"
                         class="percent"><span>0%</span></div>
                </div>
            </div>
        </div>

        <div id="success-screen" style="display: none; text-align: center;">
            <input type="hidden" name="upload_success" id="uploadsuccess" value="" />
            <br>
            <br>
            <h4>
                Your profile picture was successfully uploaded!
            </h4>
            <br>
            <br>
            <?php if ( !is_mobile_new () ) { ?>
                <a href="javascript:parent.$.fancybox.close();" id="close" class="custom_button"
                   style="text-decoration: none !important;">Close</a>
            <?php } else { ?>
                <a href="<?php echo $return_url ?>" id="close" class="custom_button"
                   style="text-decoration: none !important;">Close</a>

            <?php } ?>
        </div>
    </div>
    <script src="<?php bloginfo ( "template_url" ); ?>/assets/js/avatar/vendor/promise-polyfill.min.js"></script>
    <script src="<?php bloginfo ( "template_url" ); ?>/assets/js/avatar/vendor/canvas-to-blob.min.js"></script>
    <script src="<?php bloginfo ( "template_url" ); ?>/assets/js/avatar/load-image/load-image.js"></script>
    <script src="<?php bloginfo ( "template_url" ); ?>/assets/js/avatar/load-image/load-image-scale.js"></script>
    <script src="<?php bloginfo ( "template_url" ); ?>/assets/js/avatar/load-image/load-image-meta.js"></script>
    <script src="<?php bloginfo ( "template_url" ); ?>/assets/js/avatar/load-image/load-image-fetch.js"></script>
    <script src="<?php bloginfo ( "template_url" ); ?>/assets/js/avatar/load-image/load-image-exif.js"></script>
    <script src="<?php bloginfo ( "template_url" ); ?>/assets/js/avatar/load-image/load-image-exif-map.js"></script>
    <script src="<?php bloginfo ( "template_url" ); ?>/assets/js/avatar/load-image/load-image-orientation.js"></script>
    <script src="<?php bloginfo ( "template_url" ); ?>/assets/js/avatar/vendor/jquery.Jcrop.js"></script>
    <script src="<?php bloginfo ( "template_url" ); ?>/assets/js/avatar/avatar.js?ts=<?php echo time () ?>"></script>

    </body>
    </html>

    <?php
} else {
    header ( 'Location: ' . get_bloginfo ( 'url' ) );
}