<?php
/* Template Name: How to mail a check */

use \classes\app\fundraiser\Fundraiser_Ended;
use \classes\app\sidebar\Sidebar;

get_header( 'how-to-mail-a-check' );


$fundraiser_id = $_GET['fundraiser_id'];
$uid           = $_GET['uid'];


$title = get_the_title( $fundraiser_id );

$user_info = get_userdata( $uid );


load_class( 'goals.class.php' );
load_class( 'payment_records.class.php' );

$goal             = new Goals;
$supporters_total = $goal->get_num_supporters( $fundraiser_id );
$supporters       = $goal->get_donators( $fundraiser_id );


$fundraiser_end = new Fundraiser_Ended( $fundraiser_id );
$ended          = $fundraiser_end->check_end();
$dayleft        = $fundraiser_end->get_fundraiser_enddate();

// Set the goal info
$goal_amount = $goal->get_goal( $fundraiser_id );
$fund_amount = $goal->get_amount( $fundraiser_id );

$public_goal = $goal_amount;

if ( $ended == false ) {
	while ( $public_goal <= $fund_amount ) {
		$public_goal = $public_goal + 1000;
	}
	$media_string = ( isset( $media ) ) ? '&media=' . $media : '';
	$uid_string   = ( isset( $uid ) ) ? '&uid=' . $uid : '';
	$email_string = ( isset( $semail ) ) ? '&semail=' . $semail : '';

	// Donation URL
	$donation_url =  get_bloginfo('url') ."/donation/?fundraiser_id=$fundraiser_id&uid=$uid";
	$donation_btn = "Donate Now";
}else {
	$donation_url = "#";
	$donation_btn = "Campaign Ended";
}
$payments = new Payment_Records();

$currency   = '$';
$percentile = ( $fund_amount / $public_goal ) * 100;
$percentile = ( $percentile > 100 ) ? 100 : $percentile;
$p_amount   = $payments->get_total_by_user_id( $uid, $fundraiser_id );

$pgoal = _PARTICIPATION_GOAL;
if ($pgoal < $p_amount) {
	$pgoal = $p_amount + 100;
}

$part_percentile = ( $p_amount / $pgoal ) * 100;
$part_percentile = ( $part_percentile <= 100 ) ? $part_percentile : 100;

?>


<div class="sec_header mci_page">
    <div class="container clearfix">

        <ul>
            <li>
                <a href="<?php bloginfo('url'); ?>/donation/?fundraiser_id=<?php echo $fundraiser_id; ?>&amp;media=c&amp;uid=<?php echo $uid; ?>&amp;semail=">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/icon6.png" alt="">
                    <span>Donate</span>
                </a>
            </li>
            <li>
                <a href="javascript:void(0);" onclick="popup_facebookshare()">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/icon7.png" alt="">
                    <span>share on facebook</span>
                </a>


            </li>
            <li>
                <a href="javascript:void(0);" onclick="popup_tweetshare()">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/icon8.png" alt="">
                    <span>Tweet</span>
                </a>
            </li>
        </ul>

    </div>

</div>

<div id="hmc_container">
    <div class="left_side">
        <h1 style="text-align: center;">Instructions for Mailing<br/> a Donation Check</h1>
        <ol id="mci_list">
            <li><b>Make check payable to:</b>
                <div>
	                <?php echo "<span>" . $title . "</span>"; ?>
                </div>
            </li>
            <li><b>Include the following on the memo line:</b>
                <div>
					<?php if ( $user_info ) {
						echo "<span>" . $user_info->display_name . "</span>";
					} ?>
                    <span class="your_email_memo_line">Your email for a thank you receipt (*optional*)</span>
                </div>
            </li>
            <li><b>Mail check to:</b>
                <div>
                    <span>Vertical Raise</span>
					<?php echo "<span>" . $title . "</span>"; ?>
                    <span>505 E Front Ave #300-3</span>
                    <span>Coeur d Alene, ID 83814</span>
                </div>
            </li>
        </ol>

        <h1 class="donate_instead">Don't want to waste a stamp? Donate instantly &
            securely by Debit/Credit Card.
        </h1>
        <span class="dn_btn_container"><a class="donate_link"
                                          href="<?php echo $donation_url; ?>">
                                        <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon13.png"
                                             alt="">
				<?php echo $donation_btn ?>
                </a></span>

        <div class="dni_container">
            <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/SSL-security-seal2.png">
            <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/lock-icon.jpg">
            <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/money_back_guarantee_seal.png">
        </div>

    </div> <!-- left -->
    <div class="right_side" >

        <div class="widgets individual_profile">
			<?php if ( is_mobile_new() == false ) { ?>
                <div class="individual_profile" style="padding-top:10px;">
                    <div class="user_name user">
                        <div class="useravada_wrapper">
                            <div class="">
								<?php
								if ( is_mobile_new() ) {
									echo get_avatar( $uid, 130 );
								} else {
									echo get_avatar( $uid, 150 );
								}
								?>
                            </div>
                        </div>

                        <h3 style="color: #58595B;"><?php if ( $user_info ) {
								echo $user_info->display_name;
							} ?></h3>
                    </div>

                    <div class="quote_container">
                        <h2 class="quote" style="text-align: center;margin-bottom: 10px">&ldquo;thank you for helping<br> me reach my goal&rdquo;</h2>
                    </div>
					<?php


					if ( get_field( 'show_progressbar', $fundraiser_id ) == 1 ) {
						?>
                        <div id="progressBar5" class="default">
                            <img style="background: black"
                                 src="<?php bloginfo( 'template_directory' ); ?>/assets/images/layer22.png"
                                 alt="">
                            <div style="z-index: 9999"></div>
                        </div>
                        <script>
                            jQuery(document).ready(function () {
                                progressBar(<?php echo $part_percentile; ?>, jQuery('#progressBar5'),<?php echo $pgoal ?>, <?php echo $p_amount ?>);
                            });
                            jQuery(document).resize(function () {
                                progressBar(<?php echo $part_percentile; ?>, jQuery('#progressBar5'),<?php echo $pgoal ?>, <?php echo $p_amount ?>);
                            });
                        </script>

                        <h3 class="hide_mob" style="text-align: center;margin-top: 10px;">Total Raised:
                            <b>$<?php echo number_format( $p_amount ); ?>
                                <em>of $<?php echo $pgoal ?></em>
                            </b>
                        </h3>
						<?php
					}


					?>
                    <div class="days_left" style="margin-bottom: 30px;margin-top: 30px;">
						<?php echo $dayleft; ?>
                    </div>

                </div>
			<?php } ?>

        </div>


		<?php


		$sidebar = new Sidebar();

		if ( $supporters_total > 0 ) { ?>
            <div class="widgets supporters_comments" >
                <h3>Thank you to our supporters!</h3>
                <ul class="supporters_list">
					<?php
					$n = 0;
					foreach ( $supporters as $supporter ) {
						$n ++;

						// Donation date
						$donation_date = $sidebar->donation_date( $supporter['time'] );

						// Days ago
						$days_ago = $sidebar->days_ago( $donation_date );

						// Donation amount
						$donation_amount = $sidebar->format_donation_amount( $supporter['amount'] );

						// Donator name
						$donator_name = $sidebar->donator_name( $supporter['name'], $supporter['anonymous'] );

						$default_avatar   = ( is_mobile_new() ) ? get_template_directory_uri() . "/assets/images/small-user-avatar.png" : get_template_directory_uri() . "/assets/images/user-avatar.png";
						$supporter_avatar = ( ! isset( $comments[ $supporter['id'] ] ) || $comments[ $supporter['id'] ]['avatar_url'] == 'default' ) ? $default_avatar : $comments[ $supporter['id'] ]['avatar_url'];

						?>
                        <li class="<?php echo ( $n > 3 && is_mobile() ) ? 'hideClass' : '' ?>">
                            <div class="user">
                                <div class="img" style="background-color: black;"><img
                                            src="<?php echo $supporter_avatar; ?>"></div>
                                <div class="detail">
                                    <h5><?php echo $donation_amount; ?></h5>
                                    <b><?php echo $donator_name; ?></b>
                                    <h6><?php echo $days_ago ?></h6>
                                </div>
                            </div>
                            <div class="like">
                                <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/like.png" alt="">
                            </div>

							<?php if ( isset( $comments[ $supporter['id'] ] ) && ! empty( $comments[ $supporter['id'] ] ) ) { ?>

                                <p class="comment_text">
                                    &ldquo;<?php echo str_replace( "\\", "", $comments[ $supporter['id'] ]['comment'] ); ?>&rdquo;
                                </p>

							<?php } ?>
                        </li>
						<?php
					}
					if ( is_mobile() && $n > 3 ) { ?>
                        <li class="extraBtn">
                            <a class="morelist">
                                <b>+</b> Show More
                            </a>
                            <div class="donation-count-view">
                                Viewing <span class="js-donation-count">3</span> of <?php echo count( $supporters ) ?>
                                Donations
                            </div>
                        </li>
					<?php } ?>

                </ul>
            </div>
		<?php } else { ?>
            <div class="widgets supporters_comments">
                <h3>SUPPORTERS 0</h3>
            </div>
		<?php } ?>

    </div> <!-- right hide in mobile -->
</div>
<?php
//get_footer();
?>

