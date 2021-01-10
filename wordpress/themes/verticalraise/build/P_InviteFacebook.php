<?php /* Template Name: Participants Invite - Facebooks Message */ 

use classes\app\utm\UTM;

$utm = new UTM;
if ( is_user_logged_in() ) { 
    
    get_header();

    if ( !is_desktop() ) {
        header("Location: " . get_permalink(223) . "?fundraiser_id=" . $_GET['fundraiser_id']);
    }
    
    global $user_ID;
                               
    $campaign_participations = get_user_meta($user_ID, 'campaign_participations', true);
    $participations_array    = json_decode($campaign_participations);
    if ( !empty($participations_array) ) {
        if ( !in_array($user_ID, $participations_array) ) {
            $uid = '/'.$user_ID;
        }
    }

    $post_id = $_GET['fundraiser_id']; ?>
<div id="content">
	<div class="maincontent">
	    <div class="section group">
	        <div class="col span_12_of_12"> 
                <div class="invite" style="padding: 0;">
                    <h1><?php the_title() ?></h1>
                    <?php                            
                   	    $permalink          = get_permalink($post_id);
                        $permalink_facebook = urlencode($permalink.'f'.$uid);
                        $return_url         = urlencode(get_the_permalink(223).'?fundraiser_id='.$post_id.'&success=1');
                    ?>
                    <div class="thumb" style="background: #ebf0f6;">
                        <p style="text-align: center;">
                            <img src="<?php bloginfo('template_directory'); ?>/assets/images/fbmessenger-icon.jpg" />
                            <br />
                            <span style="color: blue;">Send personal message to facebook.</span> Be direct ask them to share your page with their friends.
                        </p>
                    </div>
                    <div style="text-align: center;">
                        <div class="section group">
                            <div class="col span_3_of_12">
                                <?php
                                    $url = urlencode(get_permalink(672)."?fundraiser_id=".$_GET['fundraiser_id']);
                                    $utm_link = $utm->createUTMLink($url, 'Facebook_Invite');
                                ?>
                                <p><a href="<?php echo $utm_link; ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;Go Back</a></p>
                            </div>
                            <div class="col span_6_of_12">
                                <div class="share_emb"><a class="facebook" style="margin: 15px 0 0 0; text-transform: uppercase; padding: 10px 30px; text-align: center;" class="custom_button" href="http://www.facebook.com/dialog/send?app_id=<?php echo _FACEBOOK_APP_ID ?>&amp;link=<?php echo $permalink_facebook; ?>&amp;redirect_uri=<?php echo $return_url; ?>">Compose Message</a></div>
                            </div>
                            <div class="col span_3_of_12">
                                <?php 
                                    $url = urlencode(get_permalink(223)."?fundraiser_id=".$_GET['fundraiser_id']);
                                    $utm_link = $utm->createUTMLink($url, 'Facebook_Invite');
                                ?>
                                <p><a href="<?php echo $utm_link; ?>"></i>Skip</a></p>
                            </div>
                        </div>
                    </div>
                </div>                         
	        </div>
	    </div>
	</div>
</div>
<?php get_footer();
                              
    } else {
    header('Location: '.get_bloginfo('url').'/login');
} ?>