<?php
/**
 * Template Name: Donation V1 - Stripe Original
 *
 * @package VerticalRaise
 */

error_reporting( E_ALL );

use classes\app\stripe\Stripe_Form;            // Stripe Form Class Object.
use classes\app\fundraiser\Fundraiser_Media;   // Fundraiser Media Class Object.
use classes\models\tables\GiveAmply;           // Give Amply.
use classes\app\fundraiser\Fundraiser_Details; // Fundraiser Details.
use \classes\models\tables\Donation_Comments;  // Comments.
use \classes\app\sidebar\Sidebar;              // Sidebar.

require_once get_template_directory() . '/stripe-php/config.php';

setcookie( 'transactionID', '', time() + 2 * 24 * 60 * 60 );

load_class( 'goals.class.php' );
load_class( 'payment_records.class.php' );

$payments = new Payment_Records();
$goal     = new Goals;

global $ab_category;
global $ab_action;

$ab_category = 'Donation A/B Test';
$ab_action   = 'DonationV1';

get_header( 'donation' );

$fundraiser_id = (int) $_GET['fundraiser_id'];
$fid = $fundraiser_id;
$uid = ( ! isset( $_GET['uid'] ) || empty( $_GET['uid'] ) ) ? 0 : $_GET['uid'];
$fundraiser_title = get_the_title( $fid);
$end_date = Fundraiser_Details::get_end_date( $fid, true );
$permalink = get_permalink( $fid );

$fundraiser_media = new Fundraiser_Media();
$image_url = $fundraiser_media->get_fundraiser_logo_stripe( $fid );

$site_name = 'VerticalRaise';
$multiple  = 100;

$sadmin    = json_decode( get_user_meta( $uid, 'campaign_sadmin', true ) );
$author_id = get_post_field( 'post_author', $fundraiser_id );

if ( $author_id == $uid || in_array_my( $fundraiser_id, $sadmin ) || $uid == 0 ) {
    $p_supporters_total = $goal->get_num_supporters( $fundraiser_id );
    $p_supporters       = $goal->get_donators( $fundraiser_id );
    $p_amount           = $payments->get_total_by_fundraiser_id( $fundraiser_id );
    $user_type          = 'team';
} else {
    $p_supporters_total = $payments->get_number_supporters_by_user_id( $uid, $fundraiser_id );
    $p_supporters       = $payments->get_all_payments_by_fundraiser_uid( $uid, $fundraiser_id );
    $p_amount           = $payments->get_total_by_user_id( $uid, $fundraiser_id );
    $user_type          = 'participant';
    $user_info          = get_userdata( $uid );
}

$give_amply = new GiveAmply();
$amply_org_id = $give_amply->get_org_id( $fid );

// Comments
$donation_comments = new Donation_Comments();
$comments = $donation_comments->get_by_fundraiser_id( $fid );

// Sidebar
$sidebar = new Sidebar();

$goal_amount = $goal->get_goal( $fid );
$currency    = 'USD';

$args = array(
    'post_type'   => 'fundraiser',
    'post_status' => 'publish',
    'p'           => $fid
);

$fundraiser_query = new WP_Query( $args );

while ( $fundraiser_query->have_posts() ) : $fundraiser_query->the_post();
    ?>
    <style>
        .loader {
            display: none;
            width: 32px;
            height: 32px;
            background-color: transparent;
            border-radius: 5px;
            position: fixed;
            top: 50%;
            left: 50%;
            margin-left: -16px;
            text-align: center;
            z-index: 3;
            overflow: auto;
        }
    </style>
    <main>
        <!--LANDING PAGE MAIN CONTENT start-->
        <div class="landing_page_main_content donation_page <?php
        if ( is_mobile_new() && !is_user_logged_in() ) {
            echo 'no_padding';
        }
        ?>">
            <div class="container">
                <div class="row">
                    <div class="col-md-5 col-sm-5 col-xs-12 col eql col_right">
                        <?php if ( !is_mobile_new() ) { ?>
                            <div class="widgets individual_profile">

                                <?php if ( $author_id == $uid || in_array_my( $fundraiser_id, $sadmin ) || $uid == 0 ) { ?>

                                    <div class="user_name">
                                        <div class="useravada">
                                            <?php if ( $image_url != null ) {
                                                ?><img src="<?php echo $image_url; ?>" /><?php } ?>
                                        </div>
                                        <h4 class="team_name"><?php echo get_post_meta( $_GET['fundraiser_id'], 'team_name', true ); ?></h4>
                                        <p>“thank you for helping <br>
                                            me reach my goal”</p>
                                    </div>
                                    <div class="total_goal">
                                        <?php
                                        $goal_amount = ( $user_type == 'team' ) ? $goal_amount : _PARTICIPATION_GOAL;
                                        $current     = $p_amount;
                                        while ( $current >= $goal_amount ) {
                                            $goal_amount = $goal_amount + 100;
                                        }

                                        $part_percentile = ( $p_amount / $goal_amount ) * 100;
                                        if ( $part_percentile <= 100 ) {

                                            if ( get_field( 'show_progressbar', $fundraiser_id ) == 1 ) {
                                                ?>
                                                <div id="progressBar6" class="default">
                                                    <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/layer22.png"
                                                         alt="">
                                                    <div></div>
                                                </div>
                                                <script>
                                                    jQuery(window).load(function () {
                                                        progressBar(<?php echo $part_percentile; ?>, jQuery('#progressBar6'),<?php echo $goal_amount ?>, <?php echo $current ?>);
                                                    });
                                                </script>
                                                <?php
                                            }
                                        }
                                        ?>
                                        <h5><b>$<?php echo number_format( $p_amount ); ?></b> of
                                            $<?php echo number_format( $goal_amount ) ?> goal</h5>
                                    </div>
                                <?php } else { ?>
                                    <div class="user_name">
                                        <div class="useravada">

                                            <a href="#" id="avada_change" class="">
                                                <?php echo get_avatar( $uid, 170 ); ?>
                                            </a>
                                        </div>

                                        <h4><?php echo $user_info->first_name . " " . $user_info->last_name; ?></h4>

                                        <p>“thank you for helping <br>
                                            me reach my goal”</p>
                                    </div>
                                    <div class="total_goal">
                                        <?php
                                        $goal_amount = _PARTICIPATION_GOAL;
                                        $current     = $p_amount;
                                        while ( $current >= $goal_amount ) {
                                            $goal_amount = $goal_amount + 100;
                                        }
                                        $part_percentile = ( $p_amount / $goal_amount ) * 100;
                                        if ( $part_percentile <= 100 ) {

                                            if ( get_field( 'show_progressbar', $fundraiser_id ) == 1 ) {
                                                ?>
                                                <div id="progressBar6" class="big-white">
                                                    <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/layer22.png"
                                                         alt="">
                                                    <div></div>
                                                </div>
                                                <script>
                                                    jQuery(document).ready(function () {
                                                        progressBar(<?php echo $part_percentile; ?>, jQuery('#progressBar6'),<?php echo $goal_amount ?>, <?php echo $current ?>);
                                                    });
                                                </script>
                                                <?php
                                            }
                                        }
                                        ?>
                                        <h5><b>$<?php echo number_format( $p_amount ); ?></b> of
                                            $<?php echo number_format( $goal_amount ) ?> goal</h5>
                                    </div>

                                <?php } ?>
                            </div>

                            <div class="widgets supporters_comments">
                                <h3>Thank you to our supporters!</h3>
                                <ul class="supporters_list">
                                    <?php
                                    if ( $p_supporters_total != 0 ) {

                                        $n = 0;
                                        foreach ( $p_supporters as $supporter ) {
                                            $n++;

                                            // Donation date
                                            $donation_date = $sidebar->donation_date($supporter['time']);

                                            // Days ago
                                            $days_ago = $sidebar->days_ago($donation_date);

                                            // Donation amount
                                            $donation_amount = $sidebar->format_donation_amount($supporter['amount']);

                                            // Donator name
                                            $donator_name = $sidebar->donator_name($supporter['name'], $supporter['anonymous']);

                                            $default_avatar   = (is_mobile_new()) ? get_template_directory_uri() . "/assets/images/small-user-avatar.png" : get_template_directory_uri() . "/assets/images/user-avatar.png";
                                            $supporter_avatar = (!isset( $comments[$supporter['id']] ) || $comments[$supporter['id']]['avatar_url'] == 'default' ) ? $default_avatar : $comments[$supporter['id']]['avatar_url'];
                                            ?>
                                            <li class="<?php echo ( $n > 3 && is_mobile_new() ) ? 'hideClass' : '' ?>">
                                                <div class="user">
                                                    <div class="img" style="background-color: black;">
                                                        <img src="<?php echo $supporter_avatar; ?>">
                                                    </div>
                                                    <div class="detail">
                                                        <h5><?php echo $donation_amount; ?></h5>
                                                        <b><?php echo $donator_name; ?></b>
                                                        <h6><?php echo $days_ago ?></h6>
                                                    </div>
                                                </div>
                                                <div class="like">
                                                    <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/like.png" alt="">
                                                </div>

                                                <?php if ( isset( $comments[$supporter['id']] ) && !empty( $comments[$supporter['id']] ) ) { ?>
                                                    <p class="comment_text">
                                                        &ldquo;<?php echo str_replace( "\\", "", $comments[$supporter['id']]['comment'] ); ?>&rdquo;
                                                    </p>

                                                <?php } ?>
                                            </li>
                                        <?php } ?>
                                        <?php if ( is_mobile_new() && $n > 3 ) { ?>
                                            <li class="extraBtn">
                                                <a class="morelist">
                                                    <b>+</b> Show More
                                                </a>
                                            </li>
                                        <?php } ?>
                                    <?php } ?>

                                </ul>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="col-md-7 col-sm-7 col-xs-12 col eql col_left">

                        <form id="donationForm">


                            <h3>Enter your donation amount </h3>

                            <div class="donationbox">
                                <div class="currency matchheight">USD</span></div>
                                <input id="donate_amount" class="amount_ip" autocomplete="off" placeholder=""
                                       onkeypress='return event.charCode >= 48 && event.charCode <= 57' type="number"
                                       name="amount" value="<?php
                                       if ( isset( $_GET['donation_amount'] ) ) {
                                           echo $_GET['donation_amount'];
                                       }
                                       ?>" required="required" maxlength="18" min="10"
                                       oninvalid="this.setCustomValidity('Please enter at least $10')"
                                       oninput="setCustomValidity('')" tabindex="1"/>
                                <div class="decimal matchheight">.00</div>
                                <div style="clear: both;"></div>
                            </div>


                            <h3 class="select_prefill">or select a donation pre-fill</h3>

                            <ul class="select_donation clearfix">
                                <li>
                                    <input type="radio" <?php
                                    if ( isset( $_GET['donation_amount'] ) && $_GET['donation_amount'] == 500 ) {
                                        echo 'checked="checked"';
                                    }
                                    ?> name="donation" id="amt2" onclick="prefill(500);">
                                    <label for="amt2">500</label>
                                </li>
                                <li>
                                    <input type="radio" <?php
                                    if ( isset( $_GET['donation_amount'] ) && $_GET['donation_amount'] == 250 ) {
                                        echo 'checked="checked"';
                                    }
                                    ?> name="donation" id="amt3" onclick="prefill(250);">
                                    <label for="amt3">250</label>
                                </li>
                                <li>
                                    <input type="radio" <?php
                                    if ( isset( $_GET['donation_amount'] ) && $_GET['donation_amount'] == 150 ) {
                                        echo 'checked="checked"';
                                    }
                                    ?> name="donation" id="amt4" onclick="prefill(150);">
                                    <label for="amt4">150</label>
                                </li>
                                <li>
                                    <input type="radio" <?php
                                    if ( isset( $_GET['donation_amount'] ) && $_GET['donation_amount'] == 100 ) {
                                        echo 'checked="checked"';
                                    }
                                    ?> name="donation" id="amt5" onclick="prefill(100);">
                                    <label for="amt5">100</label>
                                </li>
                                <li>
                                    <input type="radio" <?php
                                    if ( isset( $_GET['donation_amount'] ) && $_GET['donation_amount'] == 50 ) {
                                        echo 'checked="checked"';
                                    }
                                    ?> name="donation" id="amt6" onclick="prefill(50);">
                                    <label for="amt6">50</label>
                                </li>
                            </ul>
	                        <?php $on = 1; if ( ! empty( $amply_org_id ) && $on == 1 ) { ?>
								<link rel="stylesheet" type="text/css" href="https://giveamply.com/css/company-search.css"/>
                            <style>
                                #amplyResults{
                                    width: 100%;
                                    padding: 0;
                                    border-radius: 0;
                                    margin: 0;
                                    background: none;
                                    color: #FFFFFF;
                                }
                                #amplyResults > h3:nth-child(1){
                                    color: #52B6D5;
                                }

                                @media screen and (min-width: 768px) {
                                    #amplyResults > h3:nth-child(1){
                                        font-size: 40px;
                                    }
                                }

                                @media screen and (max-width: 767px) {
                                    #amplyResults > h3:nth-child(1){
                                        font-size: 25px;
                                    }
                                }

                                #amplyResults > h3:nth-child(1){
                                    margin-bottom: 0;
                                }
                                #amplyResults > div:nth-child(6) > small:nth-child(1){
                                    display: none;
                                }
                            </style>
                            <br>
                            <h3  style="color: #52B6D5; font-size: 40x; text-align: center; font-family: dkm-night,TrebuchetMS,sans-serif;
    letter-spacing: 1px; margin-bottom: 0;">Double Your Donation</h3>
							<p style="text-align: center;">Enter your employer in the box below and our automated system will send your donation info to them to be matched through their corporate match program</p>
                            <div class="field_row">
                                <div class="full_col fb_info" style="margin-top: 0;">
                                    <input id="amplyMatchInput" type="text" name="company" placeholder="Employer Name"
                                           class="ip"/>
                                </div>
                            </div>
							<script src="https://giveamply.com/assets/js/vr-company-search.js"></script>

                            <script>
                                var amplyOrgId = <?php echo $amply_org_id; ?>;
                                amplyProcess();
                            </script>
	                        <?php } ?>
                            <div class="don_anonymous">
                                <div class="checkboxDIv">
                                    <input type="checkbox" class="icheckbox_flat" name="anonymousCheck" value="1"
                                    <?php
                                    if ( isset( $_GET['anonymous'] ) && $_GET['anonymous'] == 1 ) {
                                        echo ' checked="checked"';
                                    }
                                    ?> tabindex="2"/>
                                    <label for="anonymous" class="anonymousLabel" style="">
                                        Make my donation anonymous
                                    </label>
                                    <input type="hidden" name="anonymous" id="AnonymousCheckStatus" value="0"/>
                                </div>
                                <script>
                                    $(".anonymousLabel").on("click", function () {
                                        $(".iCheck-helper").trigger("click");
                                    })
                                </script>
                            </div>

                            <div class="fb_info">
                                <div class="field_row clearfix">
                                    <div class="half_col">
                                        <input id="fname" type="text" name="fname" placeholder="First Name*"
                                               required="" tabindex="3"
                                               class="ip"/>
                                    </div>
                                    <div class="half_col">
                                        <input id="lname" type="text" name="lname" placeholder="Last Name*"
                                               required="" tabindex="4"
                                               class="ip"/>
                                    </div>
                                </div>

                                <div class="field_row">
                                    <div class="full_col email_field">
                                        <input id="email" type="email" name="email" value="<?php echo (isset( $_GET['semail'] ) ? $_GET['semail'] : '') ?>"
                                               placeholder="Your Email for Receipt*" required="" tabindex="5" autocomplete="off"
                                               class="ip"/>
                                        <div class="tc-result" title="Validated by TheChecker.co" >
                                            <img class="tc-result-icon" src="<?php bloginfo( 'template_directory' ); ?>/assets/images/error.png">
                                        </div>
                                        <p id="suggestion" ></p>
                                        <div id="invalid"></div>
                                    </div>
                                </div>

                                <div class="field_row">
                                    <div class="full_col">
                                        <?php
                                        // Get fundraiser participants
                                        $participants_f = get_fundraiser_participants( $fundraiser_id );

                                        $participant_array = array();
                                        foreach ( $participants_f as $item ) {
                                            $participant_array[$item->ID] = $item->display_name;
                                        }

                                        $get_uid        = (isset( $_GET['uid'] )) ? $_GET['uid'] : 0;
                                        $is_participant = (isset( $participant_array[$get_uid] )) ? 1 : 0;
                                        ?>

                                        <p class="selecte_paticipant_name">
                                            <label>Participant: </label>
                                            <span class="participant_name">
                                                <?php
                                                if ( $get_uid != 0 && $is_participant ) {
                                                    echo $participant_array[$get_uid];
                                                } elseif ( $get_uid != 0 && !$is_participant ) {
                                                    echo get_post_meta( $_GET['fundraiser_id'], 'team_name', true );
                                                } else {
                                                    echo '';
                                                }
                                                ?>

                                            </span>
                                            <span class="change_participant"> <?php echo ($get_uid != 0) ? '(Change)' : '' ?></span>
                                        </p>

                                        <select id="participant-select" name="participant-select"
                                                oninvalid="this.setCustomValidity('Please select a participant')"
                                                oninput="setCustomValidity('')" tabindex="6" style="display: <?php echo ($is_participant) ? 'none' : 'block' ?>">
                                                    <?php
                                                    $campaign_participations1 = json_decode( get_post_meta( $fundraiser_id, 'campaign_participations', true ) );
                                                    if ( $campaign_participations1 === null ) {
                                                        $campaign_participations1 = array();
                                                    }

                                                    echo '<option value="0">Select Participant to Support</option>';

                                                    /**
                                                     * Lookup user ids attached to this fundraiser
                                                     */
                                                    if ( !empty( $participant_array ) ) {
                                                        echo '<option value="0" data-participant-name="' . get_post_meta( $_GET['fundraiser_id'], 'team_name', true ) . '">' . get_post_meta( $_GET['fundraiser_id'], 'team_name', true ) . '</option>';
                                                        foreach ( $participant_array as $p_key => $p_user ) {
                                                            $participants_by_id[$p_key] = $p_user;
                                                            $participant_ids[]          = $p_key;
                                                            if ( isset( $_GET['uid'] ) && $_GET['uid'] == $p_key ) {
                                                                echo '<option selected="selected" value="' . $p_key . '" data-participant-name="' . $p_user . '">' . $p_user . '</option>';
                                                            } else {
                                                                echo '<option value="' . $p_key . '" data-participant-name="' . $p_user . '">' . $p_user . '</option>';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                        </select>
                                    </div>
                                </div>

                                <input type="hidden" name="fundraiser_id" value="<?php echo get_the_ID(); ?>"/>

                                <?php if ( isset( $_GET['media'] ) ) { ?>
                                    <input type="hidden" name="media" id="media" value="<?php echo $_GET['media']; ?>"/>
                                <?php } ?>


                                <div class="comment_part">
                                    <div class="fb_add">
                                        <div class="table_wrap">
                                            <div class="wrap">
                                                <div class="fbuser">
                                                    <?php if ( is_mobile_new() ) { ?>
                                                        <img class="user" src="<?php echo get_bloginfo( 'template_directory' ) ?>/assets/images/small-user-avatar.png" alt="">
                                                    <?php } else { ?>
                                                        <img class="user" src="<?php echo get_bloginfo( 'template_directory' ) ?>/assets/images/user-avatar.png" alt="">
                                                    <?php } ?>
                                                </div>
                                                <p>
                                                    <a href="javascript:void(0);" onclick="fbLogin('avadar')">
                                                        <img src="<?php echo get_bloginfo( 'template_directory' ) ?>/assets/images/fb.png"
                                                             alt="">
                                                        Add
                                                    </a>
                                                </p>
                                                <input type="hidden" name="avatar_url" id="avatar_url" value="default"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="comment_area">
                                        <textarea name="comment" id="comment_txt" placeholder="Leave a comment (optional)"
                                                  maxlength="250"
                                                  onkeyup="textCounter(this, 'counter', 250)"></textarea>
                                        <p id="counter">Allowed max length is 250 characters.</p>

                                    </div>
                                </div>


                                <div class="agree_terms">
                                    <?php
                                    if ( isset( $_SESSION['FIRSTNAME'] ) ) {
                                        session_destroy();
                                    }
                                    ?>

                                    <p>By continuing you are agreeing to Vertical Raise’s <a
                                            href="<?php echo get_the_permalink( 157 ); ?>"
                                            target="_blank">Terms</a>
                                        and <a
                                            href="<?php echo get_the_permalink( 379 ); ?>" target="_blank">Privacy
                                            Policy</a></p>

                                </div>

                                <div class="donate_btn">
                                    <button id="payment-button" type="submit" name="continue" data-loading="Donating..."
                                            class="submit_btn has-spinner" tabindex="7">Donate Now
                                    </button>
                                </div>

                                <div id="payment_error" style="display: none">
                                    <p id="payment_error_p">Your payment failed because: <span id="payment_error_message"></span> </p>
                                </div>

                                <div class="card">
                                    <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/cards.png"
                                         alt="">
                                </div>

                            </div>

                            <?php
                            if ( get_post_meta( $_GET['fundraiser_id'], 'stripe_connect', true ) == 1 ||
                                 get_post_meta( $_GET['fundraiser_id'], 'force_connect', true ) == 1 ) {
                                $stripe_connect = new Stripe_Form();
                                $get_account    = $stripe_connect->get_account_id( $_GET['fundraiser_id'] );
                                $account_id     = $get_account->stripe_account_id;
                            }
                            ?>
                            <input type="hidden" name="rep_code" value ="<?php echo get_post_meta( $_GET['fundraiser_id'], 'coach_code', true ) ?>" />
                            <input type="hidden" name="stripe_connect" value ="<?php echo get_post_meta( $_GET['fundraiser_id'], 'stripe_connect', true ) ?>" />
                            <input type="hidden" name="our_fee" value ="<?php echo get_post_meta( $_GET['fundraiser_id'], 'our_fee', true ) ?>" />
                            <input type="hidden" name="stripe_account_id" value="<?php echo!empty( $account_id ) ? $account_id : 0 ?>" />
                            <input type="hidden" name="force_connect" value="<?php echo  get_post_meta( $_GET['fundraiser_id'], 'force_connect', true ) ?>" />

                        </form>

                    </div>

                </div>

            </div>

        </div>
        <div class="loader">
            <center>
                <img class="loading-image"
                     src="<?php bloginfo( 'template_directory' ); ?>/assets/images/ajax-loader.gif"
                     alt="loading..">
            </center>
        </div>

        <!--LANDING PAGE MAIN CONTENT end-->

    </main>
    <!--MAIN end-->

    <script>
        $(window).load(function () {
            comment_moreless(150, $(".comment_text"));
            //            fbLogin('avadar');
        })
    </script>
    <script>
        $(function () {
            $(".change_participant").on("click", function () {
                $(".fb_info select").toggle()
            })


            $(".fb_info select").change(function () {
                var sel_participant_name = $('#participant-select option:selected').data('participant-name');

                if ( sel_participant_name != undefined ) {
                    $("p.selecte_paticipant_name span.participant_name").text(sel_participant_name);
                    $(".fb_info select").toggle()
                    $(".change_participant").text('(Change)')
                } else {
                    $("p.selecte_paticipant_name span.participant_name").text('');
                    $(".change_participant").text('')
                }
            })
        })

        // Prefill
        function prefill(amount) {
            jQuery('#donate_amount').val(amount);
            var donation_box = jQuery('.donationbox #donate_amount')[0];
            donation_box.setCustomValidity('');
            var _href = jQuery('a.facebook_info').attr('href');
            jQuery('a.facebook_info').attr('href', _href + '&amount=' + amount);
        }

        // Anonymous
        jQuery('input[name="anonymous"]').change(function () {
            var val = jQuery('input[name="anonymous"]').val();
            var _href = jQuery('a.facebook_info').attr('href');
            jQuery('a.facebook_info').attr('href', _href + '&anonymous=' + val);
        });

        // FB Url
        jQuery('input[name="amount"]').change(function () {
            var val = jQuery('input[name="amount"]').val();
            var _href = jQuery('a.facebook_info').attr('href');
            jQuery('a.facebook_info').attr('href', _href + '&amount=' + val);
        });

        // Validate email address
        function isValidEmailAddress(emailAddress) {
            var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
            return pattern.test(emailAddress);
        }
        ;

        function textCounter(field, field2, maxlimit) {
            var countfield = $("#" + field2);
            if ( $(field).val().length >= maxlimit ) {
                countfield.text('Max Characters: ' + maxlimit);
                return false;

            } else {
                var remain = maxlimit - $(field).val().length;
                countfield.text(remain + ' characters remain.')
            }
        }

    </script>
    <script>
        //Facebook Login
        window.fbAsyncInit = function () {
            // FB JavaScript SDK configuration and setup
            FB.init({
                appId: '<?php echo _FACEBOOK_APP_ID ?>', // FB App ID
                cookie: true, // enable cookies to allow the server to access the session
                xfbml: true, // parse social plugins on this page
                version: 'v2.8' // use graph api version 2.8
            });
        };

        // Load the JavaScript SDK asynchronously
        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if ( d.getElementById(id) )
                return;
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        // Facebook login with JavaScript SDK
        function fbLogin(param) {
            jQuery('#fbLogin').hide();
            jQuery('.ajax_loader').show();
            FB.login(function (response) {
                if ( response.authResponse ) {
                    // Get and display the user profile data
                    getFbUserData(param);
                } else {
                    document.getElementById('status').innerHTML = 'User cancelled login or did not fully authorize.';
                }
            }, {
                scope: 'email,public_profile,user_birthday'
            });
        }
        // Fetch the user profile data from facebook
        function getFbUserData(param) {
            FB.api('/me', {
                locale: 'en_US',
                fields: 'id,first_name,last_name,email,link,gender,locale,picture,birthday'
            },
                    function (response) {
                        console.log(response);

                        if ( param == 'info' ) {
                            jQuery('#fname').val(response.first_name);
                            jQuery('#lname').val(response.last_name);
                            jQuery('#email').val(response.email);

                            var fname = jQuery('#fname')[0];
                            var lname = jQuery('#lname')[0];
                            var email = jQuery('#email')[0];
                            fname.setCustomValidity('');
                            lname.setCustomValidity('');
                            email.setCustomValidity('');
                        } else {
                            var image = response.picture.data.url;
                            $(".comment_part img.user").attr("src", image);
                            jQuery('#avatar_url').val(image);
                        }
                    });
        }
        //END

    </script>


    <script src="https://checkout.stripe.com/checkout.js"></script>
    <script>
		function checkEndDate() {
			let end_date = <?php echo $end_date; ?>;
			if ( end_date < Math.floor(Date.now()/1000) ) {
				console.log('fundraiser ended');
				window.location.replace('<?php echo $permalink;?>');
				return true;
			}

			return false;
		}

		checkEndDate();

		jQuery (document).click(function() {
			checkEndDate();
		});
        jQuery('#donationForm').submit(function (e) {
            e.preventDefault();

			// Check if fundraiser ended
			let end_date = checkEndDate();
			if ( end_date ) {
				return;
			}

            // Launch Stripe payment window
            var site_name = "<?php echo $site_name ?>";
            var fundraiser_title = "<?php echo $fundraiser_title ?>";
            var amount = jQuery('input[name="amount"]').val() * <?php echo $multiple ?>;
            var email = $('#email').val();
            //var username = $("#fname").val() + " " + $("#lname").val();

            function anonymous() {
                if ( jQuery('input[name="anonymousCheck"]').is(":checked") ) {
                    return 1;
                }
                return 0;
            }

            function description() {
                var line = fundraiser_title;

                return line;
            }

            function site_title() {

                var line = "VerticalRaise";
                var uid = jQuery('#participant-select option:selected').val();
                if ( uid != 0 ) {
                    var participant_name = jQuery('#participant-select option:selected').data('participant-name');
                    line = participant_name;
                }
                return line;
                //get Donation User name from input box

            }

            // Stripe Window
            $("#payment-button").buttonLoader('start');


            var handler = StripeCheckout.configure({
                key: '<?php echo $stripe['publishable_key'] ?>',
                image: '',
                locale: 'auto',
                <?php if ( get_post_meta( $_GET['fundraiser_id'], 'stripe_connect', true ) != 1  &&  get_post_meta( $_GET['fundraiser_id'], 'force_connect', true ) != 1 ) { ?>
                source: function (source) {
                    jQuery('.PaymentOptions').hide();
                    jQuery('#loading').show();
                    jQuery.ajax({
                        beforeSend: function () {
                            jQuery('.loader').show();
                            jQuery('.cover').show();
                            jQuery('#payment_error').hide();
                        },
                        complete: function () {
                            jQuery('.loader').hide();
                            jQuery('.loader').hide();
                        },
                        type: "POST",
                        url: "<?php get_bloginfo( 'url' ); ?>/ajax-payment",
                        data: {
                            stripeToken: source.id,
                            stripeEmail: source.owner.email,
                            stripe_connect: jQuery('input[name=stripe_connect]').val(),
                            force_connect: jQuery('input[name=force_connect]').val(),
                            stripe_account_id: jQuery('input[name=stripe_account_id]').val(),
                            our_fee: jQuery('input[name=our_fee]').val(),
                            rep_code: jQuery('input[name=rep_code]').val(),
                            amount: jQuery('input[name="amount"]').val(),
                            anonymous: anonymous(),
                            fname: jQuery('#fname').val(),
                            lname: jQuery('#lname').val(),
                            email: jQuery('#email').val(),
                            fundraiser_id: jQuery('input[name="fundraiser_id"]').val(),
                            uid: jQuery('#participant-select option:selected').val(),
                            media: jQuery('input[name="media"]').val(),
                            comment: jQuery('#comment_txt').val(),
                            avatar_url: jQuery('#avatar_url').val(),
                            company_name: jQuery('#amplyMatchInput').val(),
                            nonce: '<?php echo wp_create_nonce( 'make - payment_ ' . $fundraiser_id . '_ ' ); ?>'
                        },

                        success: function (data) {
                            var fundraiser_id = jQuery('input[name="fundraiser_id"]').val();
                            var fname = jQuery('#fname').val();
                            var lname = jQuery('#lname').val();
                            var email = jQuery('#email').val();
                            var media = jQuery('#media').val();
                            var uid = jQuery('#participant-select option:selected').val();
                            var amount = jQuery('input[name="amount"]').val();
                            var data1 = JSON.parse(data);
//                            console.log("message----", data1,"----", data1.message);
                            if ( data1.message == 'success' ) {

                                var transaction_id = data1.message2;

                                window.location.href = "<?php echo get_bloginfo( 'url' ); ?>/thank-you-payment/?fundraiser_id=" +
                                        fundraiser_id + "&fname=" + fname + "&lname=" + lname + "&email=" +
                                        email + "&media=" + media + "&uid=" + uid + "&transaction_id=" + encodeURI(transaction_id) + "&tamount=" + amount;
                            } else {
                                console.log(data);
                                jQuery('.PaymentError').show();
                                jQuery('#loading').hide();
                                jQuery('.PaymentOptions').show();
                            }
                        },
                        error: function (e) {
                            if ( typeof e.responseJSON.message !== 'undefined') {
                                jQuery('#payment_error_message').text(e.responseJSON.message);
                            }
                            console.log(e);
                            jQuery('#payment_error').show();
                            jQuery('#loading').hide();
                            jQuery('.PaymentOptions').show();
                        },
                    });
                },
                <?php } else { ?>
                    token: function (token) {
                    jQuery('.PaymentOptions').hide();
                    jQuery('#loading').show();
                    jQuery.ajax({
                        beforeSend: function () {
                            jQuery('.loader').show();
                            jQuery('.cover').show();
                            jQuery('#payment_error').hide();
                        },
                        complete: function () {
                            jQuery('.loader').hide();
                            jQuery('.loader').hide();
                        },
                        type: "POST",
                        url: "<?php get_bloginfo( 'url' ); ?>/ajax-payment",
                        data: {
                            stripeToken: token.id,
                            stripeEmail: token.email,
                            stripe_connect: jQuery('input[name=stripe_connect]').val(),
                            force_connect: jQuery('input[name=force_connect]').val(),
                            stripe_account_id: jQuery('input[name=stripe_account_id]').val(),
                            our_fee: jQuery('input[name=our_fee]').val(),
                            rep_code: jQuery('input[name=rep_code]').val(),
                            amount: jQuery('input[name="amount"]').val(),
                            anonymous: anonymous(),
                            fname: jQuery('#fname').val(),
                            lname: jQuery('#lname').val(),
                            email: jQuery('#email').val(),
                            fundraiser_id: jQuery('input[name="fundraiser_id"]').val(),
                            uid: jQuery('#participant-select option:selected').val(),
                            media: jQuery('input[name="media"]').val(),
                            comment: jQuery('#comment_txt').val(),
                            avatar_url: jQuery('#avatar_url').val(),
                            company_name: jQuery('#amplyMatchInput').val(),
                            nonce: '<?php echo wp_create_nonce( 'make - payment_ ' . $fundraiser_id . '_ ' ); ?>'
                        },

                        success: function (data) {

                            var fundraiser_id = jQuery('input[name="fundraiser_id"]').val();
                            var fname = jQuery('#fname').val();
                            var lname = jQuery('#lname').val();
                            var email = jQuery('#email').val();
                            var media = jQuery('#media').val();
                            var uid = jQuery('#participant-select option:selected').val();
                            var amount = jQuery('input[name="amount"]').val();
                            var data = JSON.parse(data);

                            if ( data.message == 'success' ) {
                                var transaction_id = data.message2;
                                window.location.href = "<?php echo get_bloginfo( 'url' ); ?>/thank-you-payment/?fundraiser_id=" +
                                        fundraiser_id + "&fname=" + fname + "&lname=" + lname + "&email=" +
                                        email + "&media=" + media + "&uid=" + uid + "&transaction_id=" + transaction_id + "&tamount=" + amount;
                            } else {
                                console.log(data);
                                jQuery('.PaymentError').show();
                                jQuery('#loading').hide();
                                jQuery('.PaymentOptions').show();
                            }
                        },
                        error: function (e) {
                            if ( typeof e.responseJSON.message !== 'undefined') {
                                jQuery('#payment_error_message').text(e.responseJSON.message);
                            }
                            console.log(e);
                            jQuery('#payment_error').show();
                            jQuery('#loading').hide();
                            jQuery('.PaymentOptions').show();
                        },
                    });
                },
                <?php } ?>
                opened: function () {
                    $("#payment-button").buttonLoader('stop');
                }
            });

            handler.open({
                image: "<?php echo $image_url ?>",
                name: "" + site_title() + "",
                description: "" + description() + "",
                amount: amount,
                email: email,
                allowRememberMe: false
            });

            e.preventDefault();

            window.addEventListener('popstate', function () {
                handler.close();
            });
        });

        jQuery("form input, form select, form textarea").on("invalid", function () {
            jQuery(this).closest('form').addClass('invalid');
        });


        $(document).ready(function () {
            /*$('#donationForm input[name=email]').on('blur', function () {
             email_verification($(this));
             })*/
            $('#donationForm input[name=email]').on('keyup', function () {
                email_checker($(this));
            });
            $('#donationForm #suggestion').on('click', 'span', function () {
                // On click, fill in the field with the suggestion and remove the hint
                $('#donationForm input[name=email]').val($(this).text());
                $('#donationForm #suggestion').fadeOut(200, function () {
                    $(this).empty();
                    //email_verification($('#donationForm input[name=email]'));
                });
            });
        });


        /*function email_verification(emailObj) {
         //                $('#signupForm #invalid').empty();
         var email = emailObj.val();
         if (email.length > 0) {
         $("#donationForm .tc-result").show();
         } else {
         $("#donationForm .tc-result").hide();
         }
         $.get("https://api.thechecker.co/v1/verify?email=" + email + "&api_key=<?php echo _THE_CHECKER_API_KEY ?>", function (data, status) {
         if (status != 'success') {
         return false;
         } else {

         var allowed_result = ['deliverable', 'risky', 'unknown'];
         if (allowed_result.indexOf(data.result) > -1) {
         $("#donationForm .tc-result-icon").attr("src", TEMP_DIRECTORY + "/assets/images/success.png");
         $('#donationForm #invalid').empty();
         emailObj.get(0).setCustomValidity('');
         } else {
         $("#donationForm .tc-result-icon").attr("src", TEMP_DIRECTORY + "/assets/images/error.png");
         $('#donationForm #invalid').text("This email is not valid.");
         emailObj.get(0).setCustomValidity('Please enter valid email.');
         }
         }
         });
         }*/

        function email_checker(emailObj) {
            $('#donationForm #invalid').empty();
            var topLevelDomains = ["com", "net", "org"];
            emailObj.mailcheck({
                topLevelDomains: topLevelDomains,
                suggested: function (element, suggestion) {
                    $('#donationForm #suggestion').fadeIn(200);
                    $('#donationForm #suggestion').html("Did you mean <span >" + suggestion.full + "</span> ?");
                },
                empty: function (element) {
                    $("#donationForm #suggestion").empty();
                }
            });
        }

    </script>
    <?php
endwhile;

get_footer();
