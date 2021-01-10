<link rel="stylesheet" href="//cdn.datatables.net/1.10.11/assets/css/jquery.dataTables.min.css" />
<script src="//cdn.datatables.net/1.10.11/js/jquery.dataTables.min.js"></script>
<script>
jQuery(document).ready(function(){
    jQuery('#myTable').DataTable();
});
</script>
<?php
$args = array(
    'post_type' => 'fundraiser',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'p' => $_GET['id']
);
$fundraiser_query = new WP_Query($args);
if ( $fundraiser_query->have_posts() ) :
while ( $fundraiser_query->have_posts() ) : $fundraiser_query->the_post();
?>
<h1><?php the_title(); ?></h1>
<form method="GET" action="">
    <input type="hidden" name="fundraiser_id" value="<?php echo get_the_ID(); ?>" />
    <input type="hidden" name="page" value="single-report" />
    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
    <input type="submit" name="download_report" value="Download Report" class="button button-primary button-large" />
</form>
    <p><br /></p>  
    <table id="myTable">
        <thead>
            <tr>
                <th>Participant Name</th>
                <th>Participant Email</th>
                <th>Email Shares</th>
                <th>SMS Shares</th>
                <th>Twitter Shares</th>
                <th>Facebook Shares</th>
                <th>Flyer Shares</th>
                <th>Total Donations</th>
                <th>Total Raised</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $email_share = json_decode(get_post_meta($_GET['id'], 'email_share', true), true);
            $sms_share = json_decode(get_post_meta($_GET['id'], 'sms_share', true), true);
            $facebook_share = json_decode(get_post_meta($_GET['id'], 'facebook_share', true), true);
            $twitter_share = json_decode(get_post_meta($_GET['id'], 'twitter_share', true), true);
            $flyer_share = json_decode(get_post_meta($_GET['id'], 'flyer_share', true), true);
            $campaign_participations1 = json_decode(get_post_meta($_GET['id'], 'campaign_participations', true));
            if ($campaign_participations1 === null) {
                $campaign_participations1 = array();
            }
            $campaign_participations2 = array();
            $user_query = new WP_User_Query(array( 'role' => '' ));
            if ( ! empty( $user_query->results ) ) {
                foreach ($user_query->results as $user) {
                    $user_participation = json_decode(get_user_meta($user->ID, 'campaign_participations', true));
                    if(!empty($user_participation)) {
                        if(in_array($_GET['id'], $user_participation)) {
                            array_push($campaign_participations2, $user->ID);
                        }
                    }
                }
            }
            $campaign_participations = array_unique(array_merge($campaign_participations1, $campaign_participations2));
            if(!empty($campaign_participations)){            
            foreach($campaign_participations as $participant) { 
        ?>
        <tr>
            <?php
                $net_amount = 0;
                $supporters = 0;
                $email = 0;
                $facebook = 0;
                $twitter = 0;
                $sms = 0;
                $args = array(
                    'post_type' => 'supporter',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'post_parent' => $_GET['id'],
                    'meta_query' => array(
                        array(
                            'key' => 'uid',
                            'value' => $participant,
                            'type' => 'CHAR',
                            'compare' => '='
                        )
                    )
                );
                $supporter_query = new WP_Query($args);
                if ( $supporter_query->have_posts() ) :
                while ($supporter_query->have_posts()) : $supporter_query->the_post();
                    $amount = get_post_meta(get_the_ID(), 'amount', true);
                    $net_amount = $net_amount + $amount;
                    /*$media = get_post_meta(get_the_ID(), 'media', true);
                    switch ($media) {
                        case "email":
                            $email++;
                            break;
                        case "f":
                            $facebook++;
                            break;
                        case "t":
                            $twitter++;
                            break;
                        case "sms":
                            $sms++;
                            break;
                        default:
                    }*/
                endwhile;
                    $supporters = $supporter_query->found_posts;
                endif;
                        
                $email = 0;
                $sms = 0;
                $facebook = 0;
                $twitter = 0;
                $flyer = 0;
                        
                if(!empty($email_share)) {
                    foreach($email_share['user_array'] as $user_array) {
                        if($user_array['uid'] == $participant) {
                            $email = $user_array['total'];
                        }
                    }
                } else {                    
                    $email = 0;
                }
                if(!empty($sms_share)) {
                    foreach($sms_share['user_array'] as $user_array) {
                        if($user_array['uid'] == $participant) {
                            $sms = $user_array['total'];
                        }
                    }
                } else {                    
                    $sms = 0;
                }
                if(!empty($facebook_share)) {
                    foreach($facebook_share['user_array'] as $user_array) {
                        if($user_array['uid'] == $participant) {
                            $facebook = $user_array['total'];
                        }
                    }
                } else {                    
                    $facebook = 0;
                }
                if(!empty($twitter_share)) {
                    foreach($twitter_share['user_array'] as $user_array) {
                        if($user_array['uid'] == $participant) {
                            $twitter = $user_array['total'];
                        }
                    }
                } else {                    
                    $twitter = 0;
                }
                if(!empty($flyer_share)) {
                    foreach($flyer_share['user_array'] as $user_array) {
                        if($user_array['uid'] == $participant) {
                            $flyer = $user_array['total'];
                        }
                    }
                } else {                    
                    $flyer = 0;
                }
            ?>
            <td class="title">
                <?php $user_info = get_userdata($participant); ?>
                <strong><?php echo $user_info->display_name; ?></strong>
            </td>
            <td><?php echo $user_info->user_email; ?></td>
            <td style="text-align: center;"><?php echo $email; ?></td>
            <td style="text-align: center;"><?php echo $sms; ?></td>
            <td style="text-align: center;"><?php echo $twitter; ?></td>
            <td style="text-align: center;"><?php echo $facebook; ?></td>
            <td style="text-align: center;"><?php echo $flyer; ?></td>
            <td style="text-align: center;"><?php echo $supporters; ?></td>
            <td style="text-align: center;">$<?php echo $net_amount; ?></td>
        </tr>
        <?php } ?>
        <?php } else { ?>
        <tr>
            <td>No Participant found...</td>
        </tr>
        <?php } ?>                
        </tbody>
    </table>    
<?php
    endwhile;
    endif;
    wp_reset_postdata();
?>