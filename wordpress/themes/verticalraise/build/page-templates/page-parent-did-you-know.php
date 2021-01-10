<?php

/* Template Name: Participants Invite - Parent Complete */

use classes\app\fundraiser\Fundraiser_Media;  //Fundraiser Media Class Object

use classes\app\utm\UTM;

get_header( 'invite' );

$uid              = $_GET['uid'];
$user_info        = get_userdata( $uid );
if($user_info){
    $participant_name = $user_info->display_name;
}else{
    $participant_name = "None";
}

$post_id = $_GET['fundraiser_id'];
load_class( 'sharing.class.php' );

load_class( 'payment_records.class.php' );
load_class( 'participant_records.class.php' );

$campaign_participations = get_user_meta( $uid, 'campaign_participations', true );
$participations_array    = json_decode( $campaign_participations );


if ( !empty( $participations_array ) ) {
    if ( !in_array( $post_id, $participations_array ) ) {
        $uid = 0;
    }
}


$fundraiser_name = get_the_title( $post_id );

$base_url   = get_site_url();
$donate_url = $base_url . '/donation/?fundraiser_id=' . $post_id . '&uid=' . $uid;

$utm = new UTM;
$donate_url = $utm->createUTMLink($donate_url, 'Parent_Donate');

$fundraise_mediaObj = new Fundraiser_Media();
$image_url = $fundraise_mediaObj->get_fundraiser_logo( $post_id );

$participant_records = new Participant_Sharing_Totals();   // Participant sharing totals class object
$payments           = new Payment_Records();
$participants       = new Participants();


$contact_name    = get_post_meta( $post_id, 'con_name', true );
$contact_name    = (!empty( $contact_name ) ) ? $contact_name : 'VerticalRaise';

if ( is_mobile_new() ) {
    $default_size = 150;
} else {
    $default_size = 282;
}
$avatar_img_tag = get_avatar( $uid, $default_size );
$re = '/user-avatar(.*).png/m';
if ( preg_match ( $re, $avatar_img_tag, $matches) ) {
    $avatar_img_tag = "<img src=\"$image_url\" width=\"$default_size\""
        . " height=\"$default_size\" alt=\"$fundraiser_name\" "
        . " class=\"avatar avatar-$default_size wp-user-avatar "
        . " wp-user-avatar-$default_size alignnone photo\">";
}

?>
<?php while ( have_posts() ) : the_post(); ?>
    <main>
        <div class="modal invite_step customcss" id="invite_step" data-backdrop="static" tabindex="-1" role="" aria-labelledby=""
             aria-hidden=""
             style="display: block;">
            <div class="" role="document">
                <div class="modal-content">
                    <div class="modal-header model_title chalkboard_here landing_page_banner particiants_dashboard_banner">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 col_left">
                                    <div class="row">
                                        <?php
                                        $p_amount = $payments->get_total_by_user_id( $uid, $post_id );
                                        $goal    = _PARTICIPATION_GOAL;
                                        $user_info = get_userdata( $uid );
                                        ?>
                                        <div class="col-md-12 col-sm-12 col-xs-12 particiant_name">
                                            <div class="wrap">
                                                <div class="user">
                                                    <a href="#" id="avada_change" class="">
                                                        <?php
                                                        echo $avatar_img_tag;
                                                        ?>
                                                    </a>
                                                </div>
                                                <div class="mob_view_name">
                                                    <h3 class="name"><?php echo $participant_name; ?></h3>
                                                    <h3 class="goal">Total Raised:
                                                        <b>$<?php echo number_format( $p_amount ); ?>
                                                            <em>of $<?php echo number_format( $goal ); ?></em>
                                                        </b>
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                                onclick="javascript:parent.$.fancybox.close();">
                                            <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/close-btn1.png" alt="">
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!--PARTICIPANTS TABLE start-->
                            <div class="lp_participation_table">
                                <?php
                                $level = $participants->participant_level( $post_id , $uid );
                                $participants_total_share = $participants->total_shares_count_by_uid( $post_id, $uid );
                                ?>

                                <h3>Participation level:
                                    <b style="color: <?php echo $level[0] ?>;"><?php echo $level[1] ?></b>
                                </h3>
                                <ul class="share_level">
                                    <li>Parent shares:
                                        <b style="color: <?php echo $participants->level_color( $participants_total_share['parents'][0], 'parent' ) ?>;">
                                            <?php echo $participants_total_share['parents'][0]; ?>
                                        </b>
                                        <!-- #ed1c24,#3f9fe6,#46ce53-->
                                    </li>
                                    <li>Email shares:
                                        <b style="color: <?php echo $participants->level_color( $participants_total_share['email'][0], 'email' ) ?>;">
                                            <?php echo $participants_total_share['email'][0]; ?>
                                        </b>
                                    </li>

                                    <li>Facebook shares:
                                        <b style="color: <?php echo $participants->level_color( $participants_total_share['facebook'][0], 'facebook' ) ?>;">
                                            <?php echo $participants_total_share['facebook'][0]; ?>
                                        </b>
                                    </li>
                                    <li>SMS Donations:
                                        <b style="color: <?php echo $participants->level_color( $participants_total_share['sms'][0], 'donate' ) ?>;">
                                            $<?php echo $participants_total_share['sms'][0]; ?>
                                        </b>
                                    </li>
                                </ul>
                            </div>
                            <!--PARTICIPANTS TABLE end-->
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="parent_invite_instructions">
                            <p class="sharing_thanks">
                                Thanks for helping <b><?php echo ucwords ($participant_name); ?></b> reach their participation goal.
                                Now please help them reach their goal of raising $500 for the <b><?php echo ucwords($fundraiser_name); ?></b>
                            </p>
                            <h1 class="donate_cto">Please Donate Here!</h1>
                            <a href="<?php echo $donate_url; ?>" class="parent_donate">Donate</a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <?php include_once(get_template_directory() . '/prev_next_buttons.php'); ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?php endwhile; ?>