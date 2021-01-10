<?php /* Template Name: Participants Invite - Link */ ?>
<?php if(is_user_logged_in()) { ?>
<?php get_header(); ?>
<?php   
    // Load Classes
    load_class('sharing.class.php');

    $sharing             = new Sharing();                                 // Sharing class object
    $user_ID             = $sharing->user_ID;                             // Define user ID
    $fundraiser_ID       = $sharing->fundraiser_ID;                       // Define fundraiser ID
                               
    $campaign_participations = get_user_meta($user_ID, 'campaign_participations', true);
    $participations_array = json_decode($campaign_participations);
    if(!empty($participations_array)) {
        if(!in_array($user_ID, $participations_array)) {
            $uid = '/'.$user_ID;
        }
    }
?>
<?php $post_id = $_GET['fundraiser_id']; ?>
<?php
    if($_GET['success'] == 1 && $_GET['media'] == 'flyer') {
        $flyer_share = json_decode(get_post_meta($post_id, 'flyer_share', true), true);
        if(empty($flyer_share)){
            $flyer_share = array();
            $flyer_share['total'] = 1;
            $flyer_share['user_array'] = array();
            $user_array = array();
            $user_array['uid'] = $user_ID;
            $user_array['total'] = 1;
            array_push($flyer_share['user_array'], $user_array);
            update_post_meta($post_id, 'flyer_share', json_encode($flyer_share));
        } else {
            $flag = 0;
            $flyer_share['total'] = $flyer_share['total']+ 1;
            foreach($flyer_share['user_array'] as $key => $user_array) {
                if($user_array['uid'] == $user_ID) {
                    $flyer_share['user_array'][$key]['total'] = $user_array['total'] + 1;
                    $flag = 1;
                }
            }
            if($flag == 0) {                        
                $user_array = array();
                $user_array['uid'] = $user_ID;
                $user_array['total'] = 1;
                array_push($flyer_share['user_array'], $user_array);
            }
            update_post_meta($post_id, 'flyer_share', json_encode($flyer_share));
        }
    }
?>
<div id="content">
	<div class="maincontent">
	    <div class="section group">
	        <div class="col span_12_of_12"> 
                <div class="invite" style="padding: 0;">
                    <?php while (have_posts()) : the_post(); ?>
                        <h1><?php the_title() ?></h1>
                        <?php the_content(); ?>
                    <?php endwhile; ?>
                    <?php                            
                    	$permalink = get_permalink($post_id);
                        $permalink_invite = $permalink.'email'.$uid;
                    ?>
                    <div class="thumb" style="background: #EEEEEE;">
                        <p class="my_link">
                            <?php echo $permalink_invite; ?>
                        </p>
                    </div>
                    <div style="border-bottom: 1px solid #cccccc;"><p>Copy, paste & share your campsign link inside of emails to spread the word</p></div>
                    <div style="text-align: center;">
                        <div class="section group">
                            <div class="col span_3_of_12">
                                <p><a href="<?php echo get_permalink(225); ?>?fundraiser_id=<?php echo $_GET['fundraiser_id']; ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;Go Back</a></p>
                            </div>
                            <div class="col span_6_of_12">
                                <div class="share_emb"><a style="margin: 15px 0 0 0; padding: 10px 30px; text-align: center;" href="<?php echo get_permalink(678); ?>?fundraiser_id=<?php echo $_GET['fundraiser_id']; ?>&success=1&media=email" class="donate">Complete</a></div>
                            </div>
                            <div class="col span_3_of_12">
                                <!--<p><a href="<?php //echo get_permalink(678); ?>?fundraiser_id=<?php //echo $_GET['fundraiser_id']; ?>&success=1&media=email"></i>Skip</a></p>-->
                            </div>
                        </div>
                    </div>
                </div>                         
	        </div>
	    </div>
	</div>
</div>
<?php get_footer(); ?>
<?php } else {
    header('Location: '.get_bloginfo('url').'/login');
} ?>