<?php
/* Template Name: Participants Invite - Facebooks Share  */

use classes\app\fundraiser\Fundraiser_Media;  //Fundraiser Media Class Object

use classes\app\utm\UTM;
// Load Classes
load_class( 'invite_wizard.class.php' );
load_class( 'sharing.class.php' );
load_class( 'participant_records.class.php' );

get_header( 'invite' );

$p_invite_wizard     = new Invite_Wizard();                    // Parent Invite Wizard class object
$sharing             = new Sharing();                          // Sharing class object
$participant_records = new Participant_Sharing_Totals();
$user_ID             = $sharing->user_ID;                      // Define user ID
$fundraiser_ID       = $sharing->fundraiser_ID;                // Define fundraiser ID
$post_id             = $fundraiser_ID;

$campaign_participations = get_user_meta( $user_ID, 'campaign_participations', true );
$participations_array    = json_decode( $campaign_participations );


if ( $participations_array == NULL )
    $participations_array = array();

// Set the source - (is the user logged in and is he part of this campaign?)
//( is_user_logged_in() && in_array( $post_id, $participations_array )
if ( ( is_user_logged_in() && in_array( $post_id, $participations_array )) || (!empty( $_GET['parent'] ) && $_GET['parent'] == 1 ) ) {
    $source = 'invite';
} else if ( current_user_can( 'administrator' ) ) {
    $source = 'invite';
} else {
    $source = '';
}

$single = false;
if ( isset( $_GET['display_type'] ) && $_GET['display_type'] == 'single' ) {
    $uid    = (!empty( $_GET['uid'] ) && isset( $_GET['uid'] ) ) ? $_GET['uid'] : '0';
    $single = true;
} elseif ( is_user_logged_in() && !empty( $participations_array ) && in_array( $post_id, $participations_array ) ) {
    $uid = $user_ID;
} else {
    $uid = '0';
}

if ( current_user_can( 'administrator' ) ) {
    $uid    = (!empty( $_GET['uid'] ) && isset( $_GET['uid'] ) ) ? $_GET['uid'] : '0';
}

$permalink                = get_permalink( $post_id );
$permalink_facebook       = $permalink . 'f/' . $uid;
$permalink_facebook_embed = urlencode( $permalink . 'f/' . $uid );
$f_url      = $permalink . 'f/' . $uid;
$url        = $permalink . 't/' . $uid;
$utm        = new UTM;
$fb_invite  = false;

if( $single == true ) {
    if( isset( $_GET['page'] ) && $_GET['page'] == 'thankyou') {
        // Tahnks you facebook share
        if ( !is_mobile_new() ) {
            $permalink_facebook = $utm->createUTMLink( $permalink_facebook, 'Thank_You_Facebook_Share' );
        } else {
            $permalink_facebook_embed = $utm->createUTMLink( $permalink_facebook_embed, 'Thank_You_Facebook_Share' );
        }
        // Thank you twitter share
        $permalink_twitter = $utm->createUTMLink( $url, 'Thank_You_Twitter_Share' );
    }else {
        // Facebook share
        if ( !is_mobile_new() ) {
            $permalink_facebook = $utm->createUTMLink( $permalink_facebook, 'Facebook_Share' );
        } else {
            $permalink_facebook_embed = $utm->createUTMLink( $permalink_facebook_embed, 'Facebook_Share' );
        }
        // Twitter Share
        $permalink_twitter = $utm->createUTMLink( $url, 'Twitter_Share' );
    }
    
} else {
    //    invite
    if ( isset( $_GET['parent'] ) && $_GET['parent'] == 1 ){
        // Parent_Facebook_Invite
        if ( !is_mobile_new() ) {
            $permalink_facebook = $utm->createUTMLink( $permalink_facebook, 'Parent_Facebook_Invite' );
        } else {
            $permalink_facebook_embed = $utm->createUTMLink( $permalink_facebook_embed, 'Parent_Facebook_Invite' );
        }
        // Parent Twitter Invite
        $permalink_twitter = $utm->createUTMLink( $url, 'Parent_Twitter_Invite' );
    }else{
        if ( !is_mobile_new() ) {
            $fb_invite = true;
        }else{
            $permalink_facebook_embed = $utm->createUTMLink( $permalink_facebook_embed, 'Facebook_Invite_Message' );
        }
        // Twitter Invite
        $permalink_twitter = $utm->createUTMLink( $url, 'Twitter_Invite' );
    }
}

$twitter_share_text = "Click Here to support the " . get_the_title( $post_id ) . "%0A" . urlencode($permalink_twitter);

$fundraiser_name = get_the_title( $fundraiser_ID );

if ( is_mobile_new() ) {
    $default_size = 150;
} else {
    $default_size = 282;
}

// Generate Nonce
$nonce = wp_create_nonce( 'fbshare-' . $user_ID );

$credit_url = getCurrentURL() . '&credit=1&_wpnonce=' . $nonce;

// Process sharing
if ( isset($_GET['credit']) && $_GET['credit'] == 1 && isset($_REQUEST['_wpnonce']) ) {

    $nonce = $_REQUEST['_wpnonce'];
    if ( wp_verify_nonce( $nonce, 'fbshare-' . $user_ID ) ) {
        $participant_records->adjust($post_id, $user_ID, 'facebook', 1);
        $credited = 1;
    }

}

?>
<script>
    $(window).load(function () {
        setTimeout(function () {
<?php if ( $single ) { ?>
                var next_href = $(".pop_nav a.next").attr('href');
                var new_href = next_href.replace('challenge-on-facebook', 'share-on-twitter');
                $(".pop_nav a.next").attr("href", new_href);

<?php } ?>
        });
    });

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
        
        <?php 
            if ( $fb_invite ){
                $permalink_facebook = $utm->createUTMLink( $f_url, 'Facebook_Invite_Post' );
            }
        ?>
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

    function popup2() {
    
        <?php 
            if ( $fb_invite ){
                $permalink_facebook = $utm->createUTMLink( $f_url, 'Facebook_Invite_Message' );
            }
        ?>
        FB.ui({
            method: 'send',
            mobile_iframe: true,
            //hashtag: '#',
            display: 'popup',
            link: '<?php echo $permalink_facebook; ?>',
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

    function popup3() {

        var win = window.open('https://twitter.com/intent/tweet?text=<?php echo $twitter_share_text; ?>', 'Twitter', 'width=500,height=400,scrollbars=no');
        var timer = setInterval(function () {
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
                    clearInterval(timer);
                }
            });
        }, 1000);
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

            <div class="modal invite_step tnx_modal tnx_b" id="invite_step" tabindex="-1" role="" data-backdrop="static"
                 aria-labelledby=""
                 aria-hidden="" style="display: block;">
                <div class="" role="document">
                    <div class="modal-content">
                        <div class="modal-header model_title">
                            <?php
                            echo $avatar_img_tag;
                            ?>
                            <h3>Are you sure?</h3>
                            <p>You have the power to inspire others to donate!
                                <br>
                                <br>
                                Please ask your friends and family to support this fundraiser.</p>
                            <br>
                            <br>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="javascript:parent.$.fancybox.close();">
                                <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/close-btn1.png"
                                     alt="">
                            </button>
                        </div>
                        <div class="modal-body">

                            <?php if ( !is_mobile() ) { ?>
                                <a href="javascript:void(0);" onclick="popup1()" class="fb_link">
                                    <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon19.png"
                                         alt="">facebook
                                    timeline
                                </a>
                                <b class="and">and</b>
                                <a href="javascript:void(0);" onclick="popup2()" class="fb_link">
                                    <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon19.png"
                                         alt="">facebook
                                    Message
                                </a>
                            <?php } else { ?> 
                                <a href="https://www.facebook.com/dialog/feed?app_id=<?php echo _FACEBOOK_APP_ID ?>&display=popup&caption=<?php echo urlencode( $title ); ?>&link=<?php echo $permalink_facebook_embed; ?>&redirect_uri=<?php echo urlencode( getCurrentURL() ); ?>"
                                   class="fb_link">
                                    <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon19.png"
                                         alt="">share to facebook
                                </a>                               

                            <?php } ?>
                        </div>
                        <div class="modal-footer">
                            <div class="message_hidden">
                                <h2>*You have successfully posted to Facebook*</h2>
                            </div>
                            <?php include_once ( get_template_directory() . '/prev_next_buttons.php' ); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="modal invite_step " data-backdrop="static" id="invite_step" tabindex="-1" role=""
                 aria-labelledby=""
                 aria-hidden=""
                 style="display: block;">
                <div class="" role="document">
                    <div class="modal-content">
                        <div class="modal-header model_title">
                            <h4>Share on Social Media</h4>
                            <p>Post your fundraiser page link on Facebook and Twitter.</p>
                            <br>
                            <br>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="javascript:parent.$.fancybox.close();">
                                <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/close-btn1.png"
                                     alt="">
                            </button>

                        </div>
                        <div class="modal-body">

        <?php if ( !is_mobile_new() ) { ?>
                                <a href="javascript:void(0);" onclick="popup1()" class="fb_link">
                                    <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon19.png"
                                         alt="">facebook
                                    timeline
                                </a>
                                <b class="and">and</b>
                                <a href="javascript:void(0);" onclick="popup2()" class="fb_link">
                                    <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon19.png"
                                         alt="">facebook
                                    Message
                                </a>
                                <b class="and">and</b>
                                <a href="javascript:void(0);" onclick="popup3()" class="fb_link twitter_link"
                                   style="display: flex;justify-content: center;
                                   align-items: center;">
                                    <img class="tw_btn_img"
                                         src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon8.png"
                                         alt="Twitter icon share">
                                    <p class="tw_btn_text" style="">Twitter</p>
                                </a>
        <?php } else { ?>
                                <a  href="https://www.facebook.com/dialog/feed?app_id=<?php echo _FACEBOOK_APP_ID ?>&display=popup&caption=<?php echo urlencode( $title ); ?>&link=<?php echo $permalink_facebook_embed; ?>&redirect_uri=<?php echo urlencode( $credit_url ); ?>"
                                   class="fb_link">
                                    <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon19.png"
                                         alt="">share to facebook
                                </a>
                                <b class="and">and</b>
                                <a href="javascript:void(0);" onclick="popup3()" class="fb_link twitter_link"
                                   style="display: flex;justify-content: center;
                                                       align-items: center;">
                                    <img class="tw_btn_img"
                                         src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon8.png"
                                         alt="Twitter icon share">
                                    <p class="tw_btn_text">Share to Twitter</p>
                                </a>

        <?php } ?>

                        </div>
                        <div class="modal-footer">
                            <div class="message_hidden" <?php if ( !empty($credited) ) {?>style="display: block !important;"<?php } ?>>
                                <h2>*You have successfully posted to Facebook*</h2>
                            </div>
        <?php include_once ( get_template_directory() . '/prev_next_buttons.php' ); ?>
                        </div>
                    </div>
                </div>
            </div>

    <?php } ?>

    </main>
<?php endwhile;