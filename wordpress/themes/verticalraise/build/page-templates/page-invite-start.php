<?php
/* Template Name: Participants Invite - Start */

// Load Classes
use classes\app\fundraiser\Fundraiser_Media;  //Fundraiser Media Class Object
use classes\app\utm\UTM;


load_class( 'invite_wizard.class.php' );
load_class( 'sharing.class.php' );
load_class( 'participant_records.class.php' );

get_header( 'invite' );

$p_invite_wizard    = new Invite_Wizard();                           // Parent Invite Wizard class object
$sharing            = new Sharing();                                 // Sharing class object
$user_ID            = $sharing->user_ID;                             // Define user ID
$fundraiser_ID      = $sharing->fundraiser_ID;                       // Define fundraiser ID
$post_id            = $fundraiser_ID;
$fundraise_mediaObj = new Fundraiser_Media();
$sharing_records    = new Participant_Sharing_Totals();

$campaign_participations = get_user_meta( $user_ID, 'campaign_participations', true );
$participations_array    = json_decode( $campaign_participations );

if ( $participations_array == NULL )
    $participations_array = array();

// Set the source - (is the user logged in and is he part of this campaign?)
if ( ( is_user_logged_in() && in_array( $post_id, $participations_array ) ) || (!empty( $_GET['parent'] ) && $_GET['parent'] == 1 ) ) {
    $source = 'invite';
} else {
    $source = '';
}

$single = false;
$uid    = 0;

if ( isset( $_GET['display_type'] ) && $_GET['display_type'] == 'single' ) {
    $single = true;
    if ( isset( $_GET['uid'] ) && !empty( $_GET['uid'] ) ) {
        $uid = $_GET['uid'];
    }

    if ( isset( $wp_query->query_vars['uid'] ) ) {
        $uid = urldecode( $wp_query->query_vars['uid'] );
    }
} else {
    if ( is_user_logged_in() ) {
        $uid = $user_ID;
    }
}

$permalink          = get_permalink( $post_id );
$permalink_facebook = $permalink . 'f/' . $uid;
$clipboard_share_text = $permalink . 'c/' . $uid;
$image_url = $fundraise_mediaObj->get_fundraiser_logo( $post_id );

$utm = new UTM;
if ( $single == true ) {
    $clipboard_share_text = $utm->createUTMLink($clipboard_share_text, 'URL_Share');
    if( isset( $_GET['page'] ) && $_GET['page'] == 'thankyou') {
        // Tahnks you facebook share
        $permalink_facebook = $utm->createUTMLink( $permalink_facebook, 'Thank_You_Facebook_Share' );
    }else {
        // Facebook share
        $permalink_facebook = $utm->createUTMLink( $permalink_facebook, 'Facebook_Share' );
    }
}else{
    
    if (!empty( $_GET['parent'] ) && $_GET['parent'] == 1 ) {
        $clipboard_share_text = $utm->createUTMLink($clipboard_share_text, 'Parent_URL_Invite');
    }else{
        $clipboard_share_text = $utm->createUTMLink($clipboard_share_text, 'URL_Invite');
    }
}

$args = array(
    'post_type'   => 'fundraiser',
    'post_status' => array( 'pending', 'publish', 'rejected' ),
    'p'           => $_GET['fundraiser_id']
);

?><script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/clipboard.min.js"></script><?php

while ( have_posts() ) : the_post();
    ?>

    <main>

        <?php if ( $single == true ) { ?>
            <script>

                window.fbAsyncInit = function () {

                    FB.init({
                        appId: <?php echo _FACEBOOK_APP_ID; ?>,
                        cookie: true,
                        xfbml: true,
                        version: 'v2.8'
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
                    js.src = "//connect.facebook.net/en_US/sdk.js";
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));


                function popup1() {
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
                                    var next_href = $(".pop_nav a.next").attr('href');
                                    var new_href = next_href.replace('share-on-facebook', 'challenge-on-facebook');
                                    $(".pop_nav a.next").attr("href", new_href);
                                    window.location.href = $(".pop_nav a.next").attr('href');

                                }
                            });

                        } else {
                            jQuery('.message_hidden').hide();
                        }
                    });
                }
            </script>

            <div class="modal invite_step tnx_modal tnx_a" id="invite_step" tabindex="-1" role="" data-backdrop="static"
                 aria-labelledby=""
                 aria-hidden="" style="display: block;">
                <div class="" role="document">
                    <div class="modal-content">
                        <div class="modal-header">

                            <h3>The most important step is sharing!</h3>
                            <h6>Every Share Can Raise an Average of $38</h6>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="javascript:parent.$.fancybox.close();">
                                <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/close-btn1.png"
                                     alt="">
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="fundraiser_img">
                                <!--- fundraiser youtube video thumbnail image -->
	                            <?php
	                            $youtubeUrl = get_post_meta( $_GET['fundraiser_id'], 'youtube_url', true );
	                            $youtubeImg = $image_url;
	                            if ( ! empty( $youtubeUrl ) ) {
		                            $imgFlag = $fundraise_mediaObj->get_fundraiser_youtube_image( $youtubeUrl );
		                            if ( $imgFlag ) {
			                            //$youtubeImg = $imgFlag;
		                            }
	                            }
	                            ?>
                                <img src="<?php echo $youtubeImg ?>" width='300' alt="">
                                <!--- fundraiser youtube video thumbnail image -->
                                <b><?php echo get_the_title( $_GET['fundraiser_id'] ); ?></b>

                            </div>
                        </div>

                        <div class="modal-footer">

                            <div class="message_hidden">
                                <h2>*You have successfully posted to Facebook*</h2>
                            </div>


                            <?php if ( !is_mobile_new() ) { ?>
                                <a href="javascript:void(0);" onclick="popup1()" class="fb_link">
                                    <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/soc3.png"
                                         alt="">
                                    share on facebook
                                </a>
                            <?php } else { ?>

                                <a class="fb_link"
                                   href="https://www.facebook.com/dialog/feed?app_id=<?php echo _FACEBOOK_APP_ID ?>&display=popup&caption=<?php echo urlencode( $title ); ?>&link=<?php echo $permalink_facebook; ?>&redirect_uri=<?php echo urlencode( getCurrentURL() ); ?>">
                                    <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/soc3.png"
                                         alt="">
                                    share on facebook
                                </a>
                            <?php } ?>

                            <?php include_once ( get_template_directory() . '/prev_next_buttons.php' ); ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php
        } else {
    
    
            // Get sharing results
            $results = $sharing_records->get_single_row($post_id, $user_ID);
        
            if ( $results != null ) {
                $email_count    = $results->email;
                $sms_count      = $results->sms;
                $parent_count   = $results->parents;
                $facebook_count = $results->facebook;
            } else {
                $email_count    = 0;
                $sms_count      = 0;
                $parent_count   = 0;
                $facebook_count = 0;
            }

            /*
             * get invite user type $_GET['type']
             * variable: $usertype = particpant or admin
             */

            $usertype = ( isset( $_GET['type'] ) ) ? $_GET['type'] : '';
            $base     = get_site_url();

            $invite_params = "fundraiser_id=" . $_GET['fundraiser_id'] . "&type=" . $usertype;
            if ( current_user_can( 'administrator' ) ) {
                $invite_params = "fundraiser_id=" . $_GET['fundraiser_id'] . "&uid=" . $uid . "&type=" . $usertype;
            }
            ?>

            <div class="modal invite_step " id="invite_step" tabindex="-1" role="" data-backdrop="static"
                 aria-labelledby=""
                 aria-hidden="" style="display: block;">
                <div class="" role="document">
                    <div class="modal-content">
                        <div class="modal-header model_title">

                            <img class="modal_logo" src="<?php
                            echo ( $image_url != null ) ?
                                    $image_url : bloginfo( 'template_directory' ) . '/assets/images/default-logo.png';
                            ?>"
                                 width="220" alt="">
                            <table id="table_button_copy">
                                <tr>
                                    <td>
                                        <p>This wizard will take you step-by-step through the invite process. Just click NEXT</p>
                                    </td>
                                    <td>
                                        <button class="copy-button"
                                                data-clipboard-text="<?php echo $clipboard_share_text; ?>">
                                            <i class="fa fa-link"></i><span></span>
                                        </button>
                                    </td>
                                </tr>
                            </table>
                            <br>
                            <br>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="javascript:parent.$.fancybox.close();">
                                <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/close-btn1.png"
                                     alt="">
                            </button>
                        </div>
                        <div class="modal-body">
                            <?php if ( !$single ) { ?>
                                <a class="nav_invite" href="<?php echo $base ?>/parent/?<?php echo $invite_params ?>">
                                    <ul>
                                        <li class="left">
                                            <input type="radio" disabled="true"
                                                   name="spread1" <?php echo( $parent_count > 0 ) ? "checked" : "" ?>
                                                   id="spread1" class="" onclick="return false;">
                                            <label for="spread1">Add Parent(s)</label>
                                        </li>
                                        <li class="right">
                                            <b><img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon16.png"
                                                    alt=""></b><span class="invite_label">Parent</span>
                                        </li>
                                    </ul>
                                </a>
                            <?php } ?>
                            <a class="nav_invite" href="<?php echo $base ?>/invite-by-email/?<?php echo $invite_params ?>">
                                <ul>
                                    <li class="left">
                                        <input type="radio" disabled="true"
                                               name="spread2" <?php echo ( $email_count > 0 ) ? "checked" : "" ?>
                                               id="spread2" class="" onclick="return false;">
                                        <label for="spread2">Send Valid Emails</label>
                                    </li>
                                    <li class="right">
                                        <b><img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon17.png"
                                                alt=""></b>
                                        <span class="invite_label">Email</span>
                                    </li>
                                </ul>
                            </a>
                            <a class="nav_invite" href="<?php echo $base ?>/invite-by-text-message-sms/?<?php echo $invite_params ?>">
                                <ul>
                                    <li class="left">
                                        <input type="radio" disabled="true"
                                               name="spread3" <?php echo ( $sms_count > 0 ) ? "checked" : "" ?>
                                               id="spread3" class="" onclick="return false;">
                                        <label for="spread3">Send Text Messages</label>
                                    </li>
                                    <li class="right">
                                        <b><img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon18.png"
                                                alt=""></b><span class="invite_label">Text</span>
                                    </li>
                                </ul>
                            </a>
                            <a class="nav_invite" href="<?php echo $base ?>/share-on-facebook/?<?php echo $invite_params ?>">
                                <ul>
                                    <li class="left">
                                        <input type="radio" disabled="true"
                                               name="spread4" <?php echo ( $facebook_count > 0 ) ? "checked" : "" ?>
                                               id="spread4" onclick="return false;">
                                        <label for="spread4">Share on Social Media</label>
                                    </li>
                                    <li class="right">
                                        <b>
                                            <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon19.png" alt="Facebook Icon">
                                        </b>
                                        <b>
                                            <img class="tw_btn_img" src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon8.png" alt="Twitter Icon">
                                        </b>
                                    </li>
                                </ul>
                            </a>
                        </div>
                        <div class="modal-footer">
                            <?php include_once( get_template_directory() . '/prev_next_buttons.php' ); ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php } ?>
    </main>

    <?php
endwhile;
?>
<script>
    var clipboard = new ClipboardJS('.copy-button');
    clipboard.on('success', function (e) {
        $('.copy-button span').text('Copied!');
        console.log(e);
    });

    clipboard.on('error', function (e) {
        console.log(e);
    });
</script>
