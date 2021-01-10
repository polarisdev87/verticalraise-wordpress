<?php

/**
 * Sidebar-fundraiser.php displays the fundraiser goal, goal status, donations and the donation button.
 */
use \classes\app\fundraiser\Fundraiser_Ended;

load_class('goals.class.php');
$goal = new Goals;

// Get the fundraiser ID
if ( is_single() ) {
    $post_id = get_the_ID();
} else {
    $post_id = (int) $_GET['fundraiser_id'];
}

// Fundraiser info
$title     = get_the_title($post_id);
$image_url = wp_get_attachment_url(get_post_thumbnail_id($post_id), 'fundraiser-logo');
$status    = get_post_status($post_id);
$site_name = get_bloginfo("name");
$post      = get_post($post_id);

//Fundraiser End
$fundraiser_end = new Fundraiser_Ended($post_id);
$ended          = $fundraiser_end->check_end();
$dayleft        = $fundraiser_end->get_fundraiser_enddate();

// Set the goal info
$goal_amount = $goal->get_goal($post_id);
$fund_amount = $goal->get_amount($post_id);

$public_goal = $goal_amount;

if ( $ended == false ) {

    while ( $public_goal <= $fund_amount ) {
        $public_goal = $public_goal + 1000;
    }
}

// Update Goal if necessary
/* if ( $goal_amount <= $fund_amount ) {
  $goal->refactor_goal($post_id);
  $goal_amount = $goal->get_goal($post_id);
  } */

$currency         = '$';
$percentile       = ( $fund_amount / $public_goal ) * 100;
$percentile       = ( $percentile > 100 ) ? $percentile       = 100 : $percentile;
$supporters_total = $goal->get_num_supporters($post_id);
$supporters       = $goal->get_donators($post_id);

// URLs
$base_url          = get_site_url();
$fundraiser_string = '/donation/?fundraiser_id=' . $post_id;
$sharing_string    = '/invite-start/?fundraiser_id=' . $post_id;
$permalink         = get_permalink($post_id);

// Corporate Sponsors
$corporate_sponsors = get_field('corporate_sponsors', $post_id);

if ( !empty($corporate_sponsors) ) {
    ?>
    <div class="box" style="text-align: center;">
        <h2>Thanks to our Corporate Sponsors</h2>
        <?php the_field('corporate_sponsors'); ?>
    </div>
    <?php
}
?>
<div class="box">

    <div class="fundraiser_logo">
        <?php if ( $image_url != null ) {
            ?><img src="<?php echo $image_url; ?>" /><?php } ?></div>
    <h1 style="text-align: center; margin: 15px 0; font-size: 28px;"><?php echo $title; ?></h1>
    <?php
    if ( get_field('show_doller_amount', $post_id) == 1 ) {
        ?>
        <div class="goal">
            <span class="fund_amount">
                <span><?php echo $currency; ?></span>
                <?php echo number_format($fund_amount); ?>
            </span> of <?php echo $currency; ?><?php echo number_format($public_goal); ?>
        </div>
        <?php
    }

    if ( get_field('show_progressbar', $post_id) == 1 ) {
        ?>
        <div id="progressBar" class="big-green">
            <div></div>
        </div>
        <script>
            jQuery(document).ready(function () {
                progressBar(<?php echo $percentile; ?>, jQuery('#progressBar'));
            });
        </script>
        <?php
    }
    ?>

    <div style="clear: both;"></div>

    <?php
    if ( $status == 'publish' ) {

        if ( !is_page(array ('donation', 'donation-pay', 'participant-fundraiser')) ) {

            if ( is_user_logged_in() ) {

                global $user_ID;

                // Campaigns user is attached to:
                $campaign_participations = get_user_meta($user_ID, 'campaign_participations', true);
                $participations_array    = json_decode($campaign_participations);

                if ( !empty($participations_array) ) {
                    if ( in_array($post_id, $participations_array) ) {
                        $uid = $user_ID;
                    } else {
                        if ( isset($wp_query->query_vars['uid']) ) {
                            $uid = urldecode($wp_query->query_vars['uid']);
                        } else {
                            $uid = $user_ID;
                        }
                    }
                } else {
                    $uid = $user_ID;
                }
            } else {
                if ( isset($wp_query->query_vars['uid']) ) {
                    $uid = urldecode($wp_query->query_vars['uid']);
                }
            }

            if ( isset($wp_query->query_vars['media']) ) {
                $media = urldecode($wp_query->query_vars['media']);
            }

            if ( $ended == true ) {
                ?>
                <a class="donate_button" href="javascript:void(0);" class="donate">Campaign Ended</a>
            <?php } else { ?>
                <a class="donate_button" href="<?php echo $base_url; ?>/donation/?fundraiser_id=<?php echo $post_id; ?><?php
                if ( isset($media) ) {
                    echo '&media=' . $media;
                } if ( isset($uid) ) {
                    echo '&uid=' . $uid;
                }
                ?>" class="donate">Donate Now</a>
               <?php } ?>
            <div class="share_emb">
                <?php
                if ( is_user_logged_in() ) {

                    global $user_ID;
                    $campaign_participations = get_user_meta($user_ID, 'campaign_participations', true);
                    $participations_array    = json_decode($campaign_participations);

                    if ( !empty($participations_array) ) {
                        if ( !in_array($user_ID, $participations_array) ) {
                            $uid = '/' . $user_ID;
                        }
                    }
                } else {

                    if ( isset($wp_query->query_vars['media']) ) {
                        $media = '/' . urldecode($wp_query->query_vars['media']);
                    } else {
                        $media = '';
                    }
                    if ( isset($wp_query->query_vars['uid']) ) {
                        $uid = '/' . urldecode($wp_query->query_vars['uid']);
                    } else {
                        $uid = '';
                    }
                }

                if ( is_user_logged_in() ) {
                    $permalink_facebook = urlencode($permalink . 'f' . $uid);
                } else {
                    $permalink_facebook = urlencode($permalink . 'f' . $uid);
                }

                $image          = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full');
                $featured_image = $image[0];

                if ( !is_single() ) {
                    ?>
                    <a class="facebook" style="display: block; margin: 15px 0 0 0; text-transform: uppercase; padding: 10px 0; text-align: center;" href="https://www.facebook.com/dialog/feed?app_id=1567989536863012&display=popup&caption=<?php echo urlencode($title); ?>&link=<?php echo $permalink_facebook; ?>&redirect_uri=<?php echo urlencode(getCurrentURL()); ?>"><i class="fa fa-facebook"></i>Share on Facebook</a>
                <?php } ?>
            </div>
            <?php
        }
    }
    if ( is_page('participant-fundraiser') ) {

        global $user_ID;

        $permalink_copy = $permalink . 'c/' . $user_ID;
        ?>
        <a class="donate_button" href="<?php echo $permalink_copy; ?>" target="_blank" class="donate">Click here to go to your landing page</a>
    <?php } ?>
</div>
<?php
if ( is_single() ) {

    $uidq = '';
    if ( isset($wp_query->query_vars['uid']) ) {
        $uidq = urldecode($wp_query->query_vars['uid']);
    }

    // Sharing URL
    $display_string = '&display_type=single';
    $user_string    = '&user_id=' . $uidq;
    $share_url      = $base_url . $sharing_string . $display_string . $user_string;
    ?>
    <div class="share_box">
        <div class="share_emb1">
            <h3 style="text-decoration: underline; margin-bottom: 0;"><a style="color: inherit;" class="fancyboxInvite" data-fancybox-type="iframe" href="<?php echo $share_url; ?>">HELP BY SHARING</a></h3>
            <p style="color: black;">Click below to share by Facebook, text, email and Twitter</p>
            <a class="fancyboxInvite" data-fancybox-type="iframe" href="<?php echo $share_url; ?>"><img src="<?php bloginfo('template_directory'); ?>/assets/images/social_share_icons.png"/></a>
        </div>
    </div>
<?php } ?>
<div class="box">
    <div class="days_left"><?php echo $dayleft; ?></div>
    <?php
    global $user_ID;

    $is_author = 0;

    if ( $user_ID == $post->post_author ) {
        $is_author = 1;
    }
    ?>
    <div class="supporters_wrap"><div class="supporters"><?php echo $supporters_total; ?><br />Supporters</div></div>
    <div style="clear: both;"></div>
</div>
<?php
if ( !is_mobile_new() ) {

    if ( $supporters_total > 0 ) {
        ?>
        <div class="box supporter">
            <h2 style="margin-bottom: 25px;">Our Supporters</h2>
            <?php
            foreach ( $supporters as $supporter ) {
                ?>
                <div class="single_supporters">
                    <h1><sup><?php echo $currency; ?></sup><?php echo $supporter['amount']; ?></h1>
                    <?php if ( $supporter['anonymous'] != 1 ) { ?>
                        <b><?php echo $supporter['name']; ?></b>
                    <?php } else { ?>
                        <b>Anonymous</b>
                    <?php } ?>

                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }

    /**
     * Generate Donation Button.
     */
    if ( !is_page(array ('donation', 'donation-pay')) ) {

        // Media
        if ( isset($wp_query->query_vars['media']) ) {
            $media = urldecode($wp_query->query_vars['media']);
        }

        // Uid
        if ( isset($wp_query->query_vars['uid']) ) {
            $uid = urldecode($wp_query->query_vars['uid']);
        } else {
            if ( is_user_logged_in() ) {
                $uid = $user_ID;
            }
        }

        // If the fundraiser has ended
        if ( $ended == false ) {

            // Donate URL Params
            $media_string = ( isset($media) ) ? '&media=' . $media : '';
            $uid_string   = ( isset($uid) ) ? '&uid=' . $uid : '';

            // Donation URL
            $donation_url = $base_url . $fundraiser_string . $media_string . $uid_string;
            ?>
            <a class="donate_button" href="<?php echo $donation_url; ?>" class="donate">Please Donate</a>
            <?php
        }
    }
}