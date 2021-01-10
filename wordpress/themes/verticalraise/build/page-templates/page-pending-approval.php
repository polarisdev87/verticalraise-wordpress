<?php

/* Template Name: Pending Approval */

use classes\app\fundraiser\Edit_Fundraiser;
use classes\app\fundraiser\Fundraiser_Media;  //Fundraiser Media Class Object

/**
 * Load classes.
 */
load_class( 'secondary_admins.class.php' );
load_class( 'goals.class.php' );
load_class( 'sharing.class.php' );

$sharing       = new Sharing();                                 // Sharing class object
$fundraiser_id = $sharing->fundraiser_ID;

if ( !$fundraiser_id || empty( $fundraiser_id ) ) {
    header( 'Location: ' . get_bloginfo( 'url' ) . '/my-account' );
    exit();
}

$goal = new Goals;
/**
 * Instantiate classes.
 */
if ( is_user_logged_in() ) {

    global $user_ID;
    $user_info = get_userdata( $user_ID );

    $f_id = (int) $fundraiser_id;

    // Is current user attached to this fundraiser?
    $campaign_participations = get_user_meta( $user_ID, 'campaign_participations', true );
    $participations_array    = json_decode( $campaign_participations );
    $sadmin                  = json_decode( get_user_meta( $user_ID, 'campaign_sadmin', true ) );
    $author_id               = get_post_field( 'post_author', $f_id );

    if ( $author_id == $user_ID || in_array_my( $f_id, $sadmin ) || in_array( $f_id, $participations_array ) ) {
        $uid = $user_ID;
    } else {
        header( 'Location: ' . get_bloginfo( 'url' ) . '/my-account' );
        exit();
    }

    // Fundraiser info
    $title = get_the_title( $f_id );

    $img_exist = false;

    $fundraise_mediaObj = new Fundraiser_Media();
    $image_url          = $fundraise_mediaObj->get_fundraiser_logo( $f_id );
    if ( $image_url ) {
        $img_exist = true;
    }
    $status = get_post_status( $f_id );
    if ( $status == "publish" ) {
        header( 'Location: ' . get_site_url() . '/single-fundraiser/?fundraiser_id=' . $f_id );
        exit();
    }

    if ( isset( $_POST['update_media'] ) ) {
        /**
         * Process the Form Submit.
         */
        load_class( "page.edit_fundraiser.class.php" );
        $edit_fundraiser = new Edit_Fundraiser( $user_ID, $f_id );
        $edit_fundraiser->edit();
    }

    get_header();
    ?>
    <script src="https://apis.google.com/js/client:plusone.js"></script>
    <script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/cors_upload.js"></script>
    <script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/upload_video.js"></script>
    <script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/teamlogo_custom.js?ts=<?php echo time() ?>"></script>

    <script>
        $(document).ready(function () {
            $(".fund_logo .logo_change").on("click", function () {
                $("#fundlogoFile").trigger("click");

            });
            $(".fund_link .video_change").on("click", function () {
                $("#fundvideoFile").trigger("click");
            });

            function handleFileSelect(evt) {
                var orientation;
                var image, canvas;
                var files = evt.target.files;
                // Loop through the FileList and render image files as thumbnails.
                // var orientation = options.orientation
                getOrientation(document.getElementById("fundlogoFile").files[0], function (image_orientation) {
                    orientation = image_orientation;
                });
                for ( var i = 0, f; f = files[i]; i++ ) {
                    // Only process image files.
                    if ( !f.type.match('image.*') ) {
                        continue;
                    }

                    image = new Image();
                    image.src = createObjectURL(files[i]);
                    image.onload = function (e) {
                        var mybase64resized = resizeCrop(e.target, 400, 400, orientation).toDataURL(files[i].type, 90);

                        $("#team_logo").css('background-image', 'url(' + mybase64resized + ')');
                        $("#logoImage").val(mybase64resized);
                        $("#logoImageName").val(files[i].name);

                    }
                    return false;

                }
            }

            function handlevideoSelect() {
                console.log("GG", _token)

                if ( !_token ) {

                    return;
                } else {
                    if ( $('#fundvideoFile').val() ) {
                        var uploadVideo = new UploadVideo();
                        uploadVideo.ready(_token);
                    }
                }
            }

            document.getElementById('fundlogoFile').addEventListener('change', handleFileSelect, false);
            $('#fundvideoFile').on('change', function () {

                var file = $('#fundvideoFile').get(0).files[0];
                var reader = new FileReader();
                var fileType = file.type;

                reader.addEventListener('load', function () {
                    var dataUrl = reader.result;
                    var video = document.createElement("video");
                    $(video).attr("src", dataUrl)
                    var videoTagRef = $(video)[0];
                    videoTagRef.addEventListener('loadedmetadata', function (e) {
                        $("#video_width").val(videoTagRef.videoWidth)
                        $("#video_height").val(videoTagRef.videoHeight)
                    })
                }, false);

                if ( file ) {
                    reader.readAsDataURL(file)
                }

                handlevideoSelect()
            });
        });
    </script>
    <main>
        <!--MEMBER FORM SECTION start-->
        <div class="pending_fundraiser">

            <div class="fund_media_title">
                <div class="container">
                    <div class="row">
                        <h2>Fundraiser pending approval</h2>
                    </div>
                </div>
            </div>

            <div class="fund_media_form">

                <div class="container">
                    <div class="row">

                        <form id="fundmediaForm" method="post" action="" enctype="multipart/form-data">
                            <div class="upload_media">

                                <div class="col_left col fund_logo">
                                    <h3>Upload primary fundraiser
                                        <br/>image (team logo)</h3>
                                    <a class="logo_change" id="team_logo"  style="cursor:pointer;
                                       background: url(<?php echo ( $img_exist ) ? $image_url : bloginfo( 'template_directory' ) . '/assets/images/Asset5.png' ?>);
                                       background-size: cover;background-repeat: no-repeat;background-position: top center;">

                                        <b>Add/Change</b>
                                    </a>
                                    <input type="file" id="fundlogoFile" name="logo" style="display: none;"/>
                                    <input type="hidden" id="logoImage" name="logoImage"/>
                                    <input type="hidden" id="logoImageName" name="logoImageName"/>

                                </div>
                                <div class="col col_right fund_link">
                                    <h3>Upload the fundraiser video</h3>

                                    <a class="video_change" style="cursor: pointer">
                                        <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/Asset6.png"
                                             alt="">
                                    </a>

                                    <div class="upload_div">

                                        <input id="title" type="text" value="<?php echo $title ?>"
                                               style="display: none">
                                        <textarea id="description"
                                                  style="display: none"><?php echo nl2br( get_post_meta( $f_id, 'campaign_msg', true ) ) ?></textarea>

                                        <input input type="file" id="fundvideoFile" class="button" accept="video/*"
                                               style="display: none">

                                        <label id="button" class="youtube_uploadbtn"
                                               >Upload Video</label>
                                        <div class="during-upload youtube_progressDiv">
                                            <p><span id="percent-transferred"></span>% done (<span
                                                    id="bytes-transferred"></span>/<span
                                                    id="total-bytes"></span>
                                                bytes)</p>
                                            <progress id="upload-progress" max="1" value="0"></progress>
                                        </div>

                                        <input type="hidden" id="video_width" value="640"/>
                                        <input type="hidden" id="video_height" value="480"/>
                                    </div>

                                    <h3>or</h3>
                                    <input type="text" name="youtube_url" id="youtube_link" class="ip"
                                           placeholder="Paste  YouTube  Link  Here"
                                           value="<?php echo get_post_meta( $_GET['fundraiser_id'], 'youtube_url', true ); ?>"/>

                                    <div id="post-upload-status"></div>

                                    <div id="loading" style="text-align: center; display: none;">
                                        <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/ajax-loader.gif" width="35"/>
                                    </div>
                                </div>

                            </div>

                            <input type="submit" name="update_media" class="submit_btn" value="Save"/>
                        </form>

                    </div>
                </div>
            </div>

        </div>
        <!--MEMBER FORM SECTION end-->

    </main>
    <!--MAIN end-->
    <?php
    get_footer();
} else {
    header( 'Location: ' . get_bloginfo( 'url' ) );
}
?>

