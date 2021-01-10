<?php

/* Template Name: Participants Invite - Facebooks Challenge */

use classes\app\fundraiser\Fundraiser_Media;  //Fundraiser Media Class Object

// Load Classes
load_class( 'invite_wizard.class.php' );
load_class( 'sharing.class.php' );

get_header( 'invite' );

$p_invite_wizard = new Invite_Wizard();                       // Parent Invite Wizard class object
$sharing         = new Sharing();                             // Sharing class object
$user_ID         = $sharing->user_ID;                 // Define user ID
$fundraiser_ID   = $sharing->fundraiser_ID;                 // Define fundraiser ID
$post_id         = $fundraiser_ID;

$campaign_participations = get_user_meta( $user_ID, 'campaign_participations', true );
$participations_array    = json_decode( $campaign_participations );


if ( $participations_array == NULL )
    $participations_array = array();


if ( !empty( $participations_array ) ) {
    if ( !in_array( $post_id, $participations_array ) ) {
        $uid = '/' . $user_ID;
    }
}

// Set the source - (is the user logged in and is he part of this campaign?)
if ( (is_user_logged_in() && in_array( $post_id, $participations_array )) || (!empty( $_GET['parent'] ) && $_GET['parent'] == 1) ) {
    $source = 'invite';
} else {
    $source = '';
}

$single = false;
if ( isset( $_GET['display_type'] ) && $_GET['display_type'] == 'single' ) {

    if ( !empty( $_GET['uid'] ) ) {
        $uid     = '/' . $_GET['uid'];
        $user_ID = $_GET['uid'];
    } else {
        $uid = '/0';
    }
    $single = true;
} elseif ( is_user_logged_in() && in_array( $post_id, $participations_array ) ) {
    $uid = '/' . $user_ID;
} else {
    $uid = '/0';
}

$permalink          = get_permalink( $post_id );
$permalink_facebook = $permalink . 'f' . $uid;

$fundraise_mediaObj = new Fundraiser_Media();
$image_url          = $fundraise_mediaObj->get_fundraiser_logo( $fundraiser_ID );
?>
<script>
    window.fbAsyncInit = function () {

        FB.init({
            appId: <?php echo _FACEBOOK_APP_ID; ?>,
            cookie: true,
            xfbml: true,
            version: 'v2.8'
        });
        FB.AppEvents.logPageView();
//
//            FB.getLoginStatus(function (response) {
//                if (response.status === 'connected') {
//                    getFriendList();
////                    console.log(getFriendList());
//                } else {
//                    FB.login(function (response) {
//                        if (response.authResponse) {
//                            getFriendList();
//                        } else {
//                        }
//                    })
//                }
//            })

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
                        "user_id": '<?php echo $user_ID; ?>',
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

    function popup2() {

        FB.ui({
            method: 'send',
            link: '<?php echo $permalink_facebook; ?>',
        }, function (response) {
            if ( response && !response.error_code ) {
                jQuery('.message_hidden').show();
                jQuery.ajax({
                    url: "<?php bloginfo( 'url' ); ?>/participants-invite-share-ajax/",
                    data: {
                        "success": 1,
                        "post_id": <?php echo $post_id; ?>,
                        "user_id": '<?php echo $user_ID; ?>',
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
</script>


<?php
//$args = array(
//    'post_type' => 'fundraiser',
//    'post_status' => array('pending', 'publish', 'rejected'),
//    'p' => $_GET['fundraiser_id']
//);
//$fundraiser_query = new WP_Query($args);
//
//while ( $fundraiser_query -> have_posts() ) : $fundraiser_query -> the_post();

while ( have_posts() ) : the_post();
    ?>

    <main>
    <?php if ( $single == true ) { ?>

            <div class="modal invite_step tnx_modal tnx_c" id="invite_step" tabindex="-1" role="" data-backdrop="static"
                 aria-labelledby=""
                 aria-hidden="" style="display: block;">
                <div class="" role="document">
                    <div class="modal-content">
                        <div class="modal-header model_title">
                            <img class="modal_logo" src="<?php echo ( $image_url != null ) ?
                $image_url : bloginfo( 'template_directory' ) . '/assets/images/default-logo.png';
        ?>" width="220" alt="">
                            <h3>Challenge three friends to donate</h3>
                            <br>
                            <br>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="javascript:parent.$.fancybox.close();">
                                <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/close-btn1.png" alt="">
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="text" placeholder="Start typing your friend’s name" class="form_control ip">
                            <input type="text" placeholder="Start typing your friend’s name" class="form_control ip">
                            <input type="text" placeholder="Start typing your friend’s name" class="form_control ip">
                        </div>
                        <div class="modal-footer">
                            <a href="javascript:void(0);" onclick="popup2()" class="fb_link">
                                <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon19.png" alt="">
                                Send challenge
                            </a>
                            <?php include_once(get_template_directory() . '/prev_next_buttons.php'); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else { ?>

        <?php } ?>

    </main>
<?php endwhile; ?>

