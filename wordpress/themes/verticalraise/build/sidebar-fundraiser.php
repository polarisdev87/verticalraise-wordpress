<?php

/**
 * Sidebar-fundraiser.php displays the fundraiser goal, goal status, donations and the donation button.
 */
use \classes\app\fundraiser\Fundraiser_Ended;

load_class ( 'goals.class.php' );
$goal = new Goals;

// Get the fundraiser ID
if ( is_single () ) {
    $post_id = get_the_ID ();
} else {
    $post_id = (int)$_GET['fundraiser_id'];
}

// Fundraiser info
$title = get_the_title ( $post_id );
$image_url = wp_get_attachment_url ( get_post_thumbnail_id ( $post_id ), 'fundraiser-logo' );
$status = get_post_status ( $post_id );
$site_name = get_bloginfo ( "name" );
$post = get_post ( $post_id );

//Fundraiser End
$fundraiser_end = new Fundraiser_Ended($post_id);

$ended   = $fundraiser_end->check_end();
$dayleft = $fundraiser_end->get_fundraiser_enddate();

// Set the goal info
$goal_amount = $goal -> get_goal ( $post_id );
$fund_amount = $goal -> get_amount ( $post_id );

$public_goal = $goal_amount;

if ( $ended == false ) {

    while ( $public_goal <= $fund_amount ) {
        $public_goal = $public_goal + 1000;
    }

}

// Update Goal if necessary
/*if ( $goal_amount <= $fund_amount ) {
    $goal->refactor_goal($post_id);
    $goal_amount = $goal->get_goal($post_id);
}*/

$currency = '$';
$percentile = ( $fund_amount / $public_goal ) * 100;
$percentile = ( $percentile > 100 ) ? $percentile = 100 : $percentile;
$supporters_total = $goal -> get_num_supporters ( $post_id );
$supporters = $goal -> get_donators ( $post_id );

// URLs
$base_url = get_site_url ();
$fundraiser_string = '/donation/?fundraiser_id=' . $post_id;
$sharing_string = '/invite-start/?fundraiser_id=' . $post_id;
$permalink = get_permalink ( $post_id );

// Corporate Sponsors
$corporate_sponsors = get_field ( 'corporate_sponsors', $post_id );

// Comments
use \classes\models\tables\Donation_Comments;

$donation_comments = new Donation_Comments();
$comments = $donation_comments -> get_by_fundraiser_id ( $post_id );

// Sidebar
use \classes\app\sidebar\Sidebar;
$sidebar = new Sidebar();

if ( $status == 'publish' ) {

    if ( !is_page ( array ( 'donation', 'donation-pay', 'participant-fundraiser' ) ) ) { ?>

        <div class="widgets individual_profile">
            <?php
            global $user_ID;
            $permalink_copy = $permalink . 'c/' . $user_ID;
            ?>
            <span class="display_table">
                <a class="donate_link landing_link" href="<?php echo $permalink_copy; ?>"
                   target="_blank">go to your landing page</a></span>

            <div class="days_left">
                <?php echo $dayleft;?>
            </div>
        </div>


        <?php if ( $supporters_total > 0 ) { ?>
            <div class="widgets supporters_comments">
                <h3>Thank you to our supporters!</h3>
                <ul class="supporters_list">
                    <?php
                    $n = 0;
                    foreach ( $supporters as $supporter ) {
                        $n++;

                        // Donation date
                        $donation_date = $sidebar->donation_date($supporter['time']);

                        // Days ago
                        $days_ago = $sidebar->days_ago($donation_date);

                        // Donation amount
                        $donation_amount = $sidebar->format_donation_amount($supporter['amount']);

                        // Doantor name
                        $donator_name = $sidebar->donator_name($supporter['name'], $supporter['anonymous']);

                        $default_avatar = (is_mobile_new())?get_template_directory_uri()."/assets/images/small-user-avatar.png":get_template_directory_uri()."/assets/images/user-avatar.png";
                        $supporter_avatar = (!isset($comments[$supporter['id']]) || $comments[$supporter['id']]['avatar_url'] == 'default' ) ? $default_avatar : $comments[$supporter['id']]['avatar_url'];

                        ?>
                        <li class="<?php echo ( $n > 3 && is_mobile () ) ? 'hideClass' : '' ?>">
                            <div class="user">
                                <div class="img" style="background-color: #000000;"><img
                                            src="<?php echo $supporter_avatar; ?>"></div>
                                <div class="detail">
                                    <h5><?php echo $donation_amount; ?></h5>

                                    <b><?php echo $donator_name; ?></b>

                                    <h6><?php echo $days_ago ?></h6>
                                </div>
                            </div>
                            <div class="like">
                                <img src="<?php bloginfo ( 'template_directory' ); ?>/assets/images/like.png" alt="">
                            </div>

                            <?php if ( isset( $comments[$supporter['id']] ) && !empty( $comments[$supporter['id']] ) ) { ?>

                                <p class="comment_text">
                                    &ldquo;<?php echo str_replace ( "\\", "", $comments[$supporter['id']]['comment'] ); ?>&rdquo;
                                </p>

                            <?php } ?>


                        </li>
                    <?php }

                    if ( is_mobile () && $n > 3 ) { ?>
                        <li class="extraBtn">
                            <a class="morelist">
                                Show More
                            </a>
                        </li>
                    <?php } ?>

                </ul>
            </div>
        <?php } else { ?>
            <div class="widgets supporters_comments">
                <h3>SUPPORTERS 0</h3>
            </div>
        <?php }
    }
}
if ( is_page ( 'participant-fundraiser' ) ) {

    global $user_ID;

    $permalink_copy = $permalink . 'c/' . $user_ID;

    ?>
    <a class="donate_button" href="<?php echo $permalink_copy; ?>" target="_blank" class="donate">Click here to
        go to your landing page</a>
<?php }
