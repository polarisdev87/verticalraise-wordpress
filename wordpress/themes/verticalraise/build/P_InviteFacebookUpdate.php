<?php 

/* Template Name: Participants Invite - Facebooks Update */ 

if(is_user_logged_in()) { 
    
    get_header();
                               
    global $user_ID;
    $campaign_participations = get_user_meta($user_ID, 'campaign_participations', true);
    $participations_array = json_decode($campaign_participations);
    if ( !empty($participations_array) ) {
        if ( !in_array($user_ID, $participations_array) ) {
            $uid = '/'.$user_ID;
        }
    }

    $post_id = $_GET['fundraiser_id']; 
    
    if ( $_GET['success'] == 1 ) {
        $facebook_share = json_decode(get_post_meta($post_id, 'facebook_share', true), true);
        if ( empty($facebook_share) ){
            $facebook_share = array();
            $facebook_share['total'] = 1;
            $facebook_share['user_array'] = array();
            $user_array = array();
            $user_array['uid'] = $user_ID;
            $user_array['total'] = 1;
            array_push($facebook_share['user_array'], $user_array);
            update_post_meta($post_id, 'facebook_share', json_encode($facebook_share));
        } else {
            $flag = 0;
            $facebook_share['total'] = $facebook_share['total']+ 1;
            foreach ( $facebook_share['user_array'] as $user_array ) {
                if ( $user_array['uid'] == $user_ID ) {
                    $user_array['total'] = $user_array['total'] + 1;
                    $flag = 1;
                }
            }
            if ( $flag == 0 ) {                        
                $user_array = array();
                $user_array['uid'] = $user_ID;
                $user_array['total'] = 1;
                array_push($facebook_share['user_array'], $user_array);
            }
            update_post_meta($post_id, 'facebook_share', json_encode($facebook_share));
        }
    }
    if ( $_GET['token'] == 1 ) {
        $return_url = urlencode(get_the_permalink(672).'?fundraiser_id='.$post_id.'&token=1');
        $accessToken = processURL('https://graph.facebook.com/oauth/access_token?client_id=' . _FACEBOOK_CLIENT_ID . '&redirect_uri='.$return_url.'&client_secret=' . _FACEBOOK_CLIENT_SECRET . '&code='.$_GET['code']);
        //echo 'https://graph.facebook.com/oauth/access_token?client_id=1567989536863012&redirect_uri='.$return_url.'&client_secret=7bfa46a00782b3624d38e0c91fc459dc&code='.$_GET['code'];
        $startsAt = strpos($accessToken, "access_token=") + strlen("access_token=");
        $endsAt = strpos($accessToken, "&expires=", $startsAt);
        $result = substr($accessToken, $startsAt, $endsAt - $startsAt);
        
        global $user_ID;
        $facebook_token = json_decode(get_post_meta($post_id, 'facebook_token', true), true);
        if ( empty($facebook_token) ){
            $facebook_token = array();
            $facebook_token['user_array'] = array();
            $user_array = array();
            $user_array['uid'] = $user_ID;
            $user_array['access_token'] = $result;
            array_push($facebook_token['user_array'], $user_array);
            update_post_meta($post_id, 'facebook_token', json_encode($facebook_token));
        } else {
            $flag = 0;
            foreach ( $facebook_token['user_array'] as $user_array ) {
                if ( $user_array['uid'] == $user_ID ) {
                    $user_array['access_token'] = $result;
                    $flag = 1;
                }
            }
            if ( $flag == 0 ) {                        
                $user_array = array();
                $user_array['uid'] = $user_ID;
                $user_array['access_token'] = $result;
                array_push($facebook_token['user_array'], $user_array);
            }
            update_post_meta($post_id, 'facebook_token', json_encode($facebook_token));
        }
        header("Location: ".get_permalink(216)."?fundraiser_id=".$_GET['fundraiser_id']);
    }
?>
<div id="content">
	<div class="maincontent">
	    <div class="section group">
	        <div class="col span_12_of_12"> 
                <div class="invite" style="padding: 0;">
                    <h1><?php the_title() ?></h1>
                    <?php
                        $site_name = get_bloginfo("name");                            
                    	$permalink = get_permalink($post_id);
                        $permalink_facebook = urlencode($permalink.'f'.$uid);
                        $image1 = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'fundraiser-logo' );
                        $image = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'full' );
                    	$featured_image = $image[0];
                    	$post_title = rawurlencode(get_the_title($url));
                        $return_url = urlencode(get_the_permalink(672).'?fundraiser_id='.$post_id.'&token=1');
                    ?>
                    <div class="thumb" style="background: #ebf0f6;">
                        <p style="text-align: center;">
                            <img src="<?php bloginfo('template_directory'); ?>/assets/images/facebook_update.png" />
                            <br />
                            Keep your friends and family informed with campaign updates on Facebook
                        </p>
                    </div>
                    <div style="text-align: center;">
                        <div class="section group">
                            <div class="col span_3_of_12">
                                <p><a href="<?php echo get_permalink(221); ?>?fundraiser_id=<?php echo $_GET['fundraiser_id']; ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;Go Back</a></p>
                            </div>
                            <div class="col span_6_of_12">
                                <div class="share_emb"><a class="facebook" style="margin: 15px 0 0 0; text-transform: uppercase; padding: 10px 30px; text-align: center;" class="custom_button" href="https://www.facebook.com/dialog/oauth?client_id=<?php echo _FACEBOOK_CLIENT_ID; ?>&redirect_uri=<?php echo $return_url; ?>&scope=manage_pages,publish_actions" class="facebook"><i class="fa fa-facebook"></i>Continue</a></div>
                            </div>
                            <div class="col span_3_of_12">
                                <p><a href="<?php echo get_permalink(216); ?>?fundraiser_id=<?php echo $_GET['fundraiser_id']; ?>"></i>Skip</a></p>
                            </div>
                        </div>
                    </div>
                    <p style="text-align: center;">This is a bi-weekly post to your facebook wall</p>
                </div>                         
	        </div>
	    </div>
	</div>
</div>
<?php get_footer(); ?>
<?php } else {
    header('Location: '.get_bloginfo('url').'/login');
} ?>