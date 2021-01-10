<?php 

/* Template Name: Participants Invite - Complete */ 

if ( is_user_logged_in() ) {
    
    get_header(); 
    
    global $user_ID;

?>
<div class="invite_menu">
    <?php wp_nav_menu(array('theme_location' => 'invitemenu')); ?>
</div>
<?php 
    
    
    $campaign_participations = get_user_meta($user_ID, 'campaign_participations', true);
    $participations_array = json_decode($campaign_participations);
    
    if ( !empty($participations_array) ) {
        if ( !in_array($user_ID, $participations_array) ) {
            $uid = '/' . $user_ID;
        }
    }

    $post_id = $_GET['fundraiser_id']; 
    
    if ( isset($_GET['success']) && $_GET['success'] == 1 && $_GET['media'] == 'facebook') {
        $facebook_share = json_decode(get_post_meta($post_id, 'facebook_share', true), true);
        if ( empty($facebook_share) ) {
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
        header("Location: " . get_permalink($post_id));
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
                    <?php endwhile;

                        $return_url = urlencode(get_the_permalink(678) . '?fundraiser_id=' . $post_id . '&success=1&media=facebook');
    
                    ?>
                    <hr>
                    <?php include_once(get_template_directory() . '/prev_next_buttons.php');?>
                </div>                         
	        </div>
	    </div>
	</div>
</div>
<?php 
    
get_footer();
    
} else {
    header('Location: ' . get_bloginfo('url') . '/login');
}