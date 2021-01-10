<?php
/* Template Name: Participants Invite - Twitter Share */

// Load Classes
use classes\app\fundraiser\Fundraiser_Media;  //Fundraiser Media Class Object

use classes\app\utm\UTM;

load_class( 'invite_wizard.class.php' );
load_class( 'sharing.class.php' );

get_header( 'invite' );

$p_invite_wizard    = new Invite_Wizard();                           // Parent Invite Wizard class object
$sharing            = new Sharing();                                 // Sharing class object
$user_ID            = $sharing->user_ID;                           // Define user ID
$fundraiser_ID      = $sharing->fundraiser_ID;                     // Define fundraiser ID
$post_id            = $fundraiser_ID;
$fundraise_mediaObj = new Fundraiser_Media();

$campaign_participations = get_user_meta( $user_ID, 'campaign_participations', true );
$participations_array    = json_decode( $campaign_participations );
if ( $participations_array == NULL )
    $participations_array    = array();


if ( $participations_array == NULL )
    $participations_array = array();

// Set the source - (is the user logged in and is he part of this campaign?)
if ( ( is_user_logged_in() && in_array( $post_id, $participations_array ) ) || (!empty( $_GET['parent'] ) && $_GET['parent'] == 1 ) ) {
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
    $uid = (!empty( $_GET['uid'] ) && isset( $_GET['uid'] ) ) ? $_GET['uid'] : '0';
   
}

$permalink          = get_permalink( $post_id );
$url                = $permalink . 't/' . $uid;

$utm = new UTM;

if( $single ) {
    if($_GET['page'] == 'thankyou') {
        // Thank you twitter share
        $permalink_twitter = $utm->createUTMLink( $url, 'Thank_You_Twitter_Share' );
    }else {
        // Twitter Share
        $permalink_twitter = $utm->createUTMLink( $url, 'Twitter_Share' );
    }
}

$twitter_share_text = "Click Here to support the " . get_the_title( $post_id ) . "%0A" . urlencode($permalink_twitter);

// get Teamlogo image
$image_url          = $fundraise_mediaObj->get_fundraiser_logo( $post_id );

while ( have_posts() ) : the_post();
    ?>

    <main>

        <div class="modal invite_step tnx_modal tnx_d" id="invite_step" tabindex="-1" role="" data-backdrop="static"
             aria-labelledby=""
             aria-hidden="" style="display: block;">
            <div class="" role="document">
                <div class="modal-content">
                    <div class="modal-header">

                        <h3>The most important step is sharing!</h3>
                        <h6>Every Share Can Raise an Average of $38</h6>
                        <br>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                onclick="javascript:parent.$.fancybox.close();">
                            <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/close-btn1.png" alt="">
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
                        <a href="javascript:void(0);" onclick="popup()" class="twitter_link">
                            <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/soc5.png" alt=""> share
                            on
                            twitter
                        </a>
                        <?php include_once( get_template_directory() . '/prev_next_buttons.php' ); ?>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <script>
        function popup() {
            var win = window.open('https://twitter.com/intent/tweet?text=<?php echo $twitter_share_text; ?>', 'Twitter', 'width=500,height=400,scrollbars=no');
            var timer = setInterval(function () {
                //if(win.closed) {
                clearInterval(timer);
                jQuery.ajax({
                    url: "<?php bloginfo( 'url' ); ?>/participants-invite-share-ajax/",
                    data: {
                        "success": 1,
                        "post_id": <?php echo $post_id; ?>,
                        "user_id": '<?php echo $uid; ?>',
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
    <?php
endwhile;
