<?php 

/* Template Name: Participants Invite - Flyer Share */ 

// Load Classes
load_class('invite_wizard.class.php');
load_class('sharing.class.php');

$p_invite_wizard     = new Invite_Wizard();                    // Parent Invite Wizard class object
$sharing             = new Sharing();                                 // Sharing class object
$user_ID             = $sharing->user_ID;                             // Define user ID
$fundraiser_ID       = $sharing->fundraiser_ID;                       // Define fundraiser ID

$campaign_participations = get_user_meta($user_ID, 'campaign_participations', true);
$participations_array = json_decode($campaign_participations);
if ( !empty($participations_array) ) {
    if( !in_array($user_ID, $participations_array) ) {
        $uid = '/' . $user_ID;
    }
}

if ( isset($_GET['display_type']) && $_GET['display_type'] == 'single' ) {
    $uid = '/'. $_GET['uid'];
    $user_ID = $_GET['uid'];
}

if ( isset($_GET['print_flyer']) && $_GET['print_flyer'] == 'true' ) {
	if ( isset($_GET['display_type']) && $_GET['display_type'] == 'single' ) {
		$uid = '/' . $_GET['uid'];
		$user_ID = $_GET['uid'];
	}
    require_once (dirname(__FILE__).'/html2pdf/vendor/autoload.php');
    $html2pdf = new HTML2PDF('P','A4','en');
    
    $text = get_option('flyers');
    $text = stripslashes($text);
    if ( get_magic_quotes_gpc() ) {
        $text = stripslashes($text);
    } else {
        $text = $text;
    }
    $text = str_replace('{Fundraiser Name}', get_the_title($_GET['fundraiser_id']), $text);
    $thumb_id = get_post_thumbnail_id($_GET['fundraiser_id']);
    $thumb_url_array = wp_get_attachment_image_src($thumb_id, 'medium', true);
    $thumb_url = $thumb_url_array[0];
    $image = '<img src="'.$thumb_url.'" />';
    $text = str_replace('{Fundraiser Image}', $image, $text);
    $message = nl2br(get_post_meta($_GET['fundraiser_id'], 'campaign_msg', true));
    $text = str_replace('{Campaign message to donors}', $message, $text);
    $text = str_replace('{Fundraiser Link}', get_permalink($_GET['fundraiser_id']) . 'flyer' . $uid, $text);
    $qr_code = '<qrcode value="' . get_permalink($_GET['fundraiser_id']) .'flyer' . $uid . '" ec="H" style="width: 30mm;"></qrcode>';
    $text = str_replace('{QR Code}', $qr_code, $text);
    //$join_code = get_post_meta($_GET['fundraiser_id'], 'join_code', true);
    //$text = str_replace('{Join Code}', $join_code, $text);
    $content = '<page>' . $text . '</page>';
    
    $html2pdf->WriteHTML($content);
    $date = date('Ymd');
    $time = time();
    $pdfName = $date . '_' . $time . '_' . 'Flyer.pdf';
    $html2pdf->Output($pdfName);
    die();    
}
?>
<?php get_header(); ?>
<!-- Menu -->
<div class="invite_menu">
<?php

include_once(get_template_directory() . '/sharing-menu.php');

?>
</div>
<!-- /Menu -->
<?php $post_id = $_GET['fundraiser_id']; ?>
<?php
    if ( isset($_GET['success']) && isset($_GET['media']) && $_GET['success'] == 1 && $_GET['media'] == 'twitter') {
        $twitter_share = json_decode(get_post_meta($post_id, 'twitter_share', true), true);
        if ( empty($twitter_share) ) {
            $twitter_share = array();
            $twitter_share['total'] = 1;
            $twitter_share['user_array'] = array();
            $user_array = array();
            $user_array['uid'] = $user_ID;
            $user_array['total'] = 1;
            array_push($twitter_share['user_array'], $user_array);
            update_post_meta($post_id, 'twitter_share', json_encode($twitter_share));
        } else {
            $flag = 0;
            $twitter_share['total'] = $twitter_share['total']+ 1;
            foreach($twitter_share['user_array'] as $key => $user_array) {
                if($user_array['uid'] == $user_ID) {
                    $twitter_share['user_array'][$key]['total'] = $user_array['total'] + 1;
                    $flag = 1;
                }
            }
            if ( $flag == 0 ) {
                $user_array = array();
                $user_array['uid'] = $user_ID;
                $user_array['total'] = 1;
                array_push($twitter_share['user_array'], $user_array);
            }
            update_post_meta($post_id, 'twitter_share', json_encode($twitter_share));
        }
    }
?>
<div id="content">
	<div class="maincontent">
	    <div class="section group">
	        <div class="col span_12_of_12">
                <div class="invite_wizard">
                    <?php while (have_posts()) : the_post(); ?>
                        <h1><?php the_title(); ?></h1>
                        <div><?php the_content(); ?></div>
                    <?php endwhile; ?>
                    <?php
                    	$permalink = get_permalink($post_id);
                        $permalink_flyer = $permalink.'flyer'.$uid;
                        $image1 = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'fundraiser-logo' );
                        $image = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'full' );
                        $return_url = get_the_permalink(225).'?fundraiser_id='.$post_id.'&success=1&media=flyer';
                    ?>
                      <div style="text-align: center;">
 -                        <?php if ( isset($_GET['display_type']) && $_GET['display_type'] == 'single' ) { ?>
 -                        
 -                            <a href="<?php bloginfo('url'); ?>/invite-through-flyers/?fundraiser_id=<?php echo $_GET['fundraiser_id']; ?>&print_flyer=true&display_type=single&user_id=<?php echo $_GET['uid']; ?>" target="_blank"><img src="<?php echo $image1[0]; ?>" style="max-height: 240px; -webkit-box-shadow: 4px 4px 18px 5px rgba(0,0,0,0.7); -moz-box-shadow: 4px 4px 18px 5px rgba(0,0,0,0.7); box-shadow: 4px 4px 18px 5px rgba(0,0,0,0.7);" /></a>
 -                        
 -                        <?php } else { ?>
 -                        
 -                            <a href="<?php bloginfo('url'); ?>/invite-through-flyers/?fundraiser_id=<?php echo $_GET['fundraiser_id']; ?>&print_flyer=true" target="_blank"><img src="<?php echo $image1[0]; ?>" style="max-height: 240px; -webkit-box-shadow: 4px 4px 18px 5px rgba(0,0,0,0.7); -moz-box-shadow: 4px 4px 18px 5px rgba(0,0,0,0.7); box-shadow: 4px 4px 18px 5px rgba(0,0,0,0.7);" /></a>
 -                        
 -                        <?php } ?>
 -                    </div>

                    <hr>
                    <?php include_once(get_template_directory() . '/prev_next_buttons.php');?>
                    
                </div>
	        </div>
	    </div>
	</div>
</div>
<?php get_footer();