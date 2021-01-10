<?php

/* Template Name: Participants Invite - Parent Start */

use classes\app\fundraiser\Fundraiser_Media;  //Fundraiser Media Class Object

// Load classes
load_class( 'participant_records.class.php' );
load_class( 'invite_wizard.class.php' );
load_class( 'sharing.class.php' );
load_class( 'payment_records.class.php' );

get_header( 'invite' );
/**
 * Instantiate Classes.
 */
$p_invite_wizard     = new Invite_Wizard();                // Parent Invite Wizard class object
$sharing             = new Sharing();                      // Sharing class object
$participant_records = new Participant_Sharing_Totals();   // Participant sharing totals class object

$fundraiser_id = $sharing->fundraiser_ID;                  // Define fundraiser ID
$uid           = (isset( $_GET['uid'] )) ? $_GET['uid'] : '0';

$user_data            = get_userdata( $uid );
if ($user_data) {
    $participant_name = $user_data->display_name;
} else {
    $participant_name = "None";
}
$fundraiser_name = get_the_title( $fundraiser_id );
$contact_name    = get_post_meta( $fundraiser_id, 'con_name', true );
$contact_name    = (!empty( $contact_name ) ) ? $contact_name : 'VerticalRaise';

$has_shared = $participant_records->get_single_row( $fundraiser_id, $uid );

if ( empty( $has_shared->parents ) ) {
    // Add a parent share
    $participant_records->adjust( $fundraiser_id, $uid, 'parents', 1 );
}

$opening_line = "";
if ( !empty( $user_data ) ) {
    $opening_line = "Help {$user_data->display_name} reach their minimum participation goal. Thank you for the support!";
}

$fundraise_mediaObj = new Fundraiser_Media();
$image_url          = $fundraise_mediaObj->get_fundraiser_logo( $fundraiser_id );
$base_url           = get_site_url();


$payments           = new Payment_Records();
$participants       = new Participants();

if ( is_mobile_new() ) {
    $default_size = 150;
    $p_size = '20px';
} else {
    $default_size = 282;
    $p_size = '16px';
}
$avatar_img_tag = get_avatar( $uid, $default_size );
$re = '/user-avatar(.*).png/m';
if ( preg_match( $re, $avatar_img_tag, $matches ) ) {
    $avatar_img_tag = "<img src=\"$image_url\" width=\"$default_size\""
                        . " height=\"$default_size\" alt=\"$fundraiser_name\" "
                        . " class=\"avatar avatar-$default_size wp-user-avatar "
                        . " wp-user-avatar-$default_size alignnone photo\">";
}

while ( have_posts() ) : the_post(); ?>
    <main>
        <div class="modal invite_step customcss " id="invite_step" tabindex="-1" role="" data-backdrop="static" aria-labelledby=""
             aria-hidden="" style="display: block;">
            <div class="" role="document">
                <div class="modal-content ">
                    <div class="modal-header model_title chalkboard_here landing_page_banner particiants_dashboard_banner">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 col_left">
                                    <div class="row">
                                        <?php
                                        $p_amount  = $payments->get_total_by_user_id( $uid, $fundraiser_id );
                                        $goal      = _PARTICIPATION_GOAL;
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
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="parent_invite_instructions">
                            <div>
                                <h1>Instructions</h1>
                                <p>Please help <?php echo ucwords ($participant_name); ?> reach their participation goal for our
                                    <?php echo $fundraiser_name; ?> by clicking next and following all the steps. Thank you so much for
                                    your support!
                                </p>
                                <p>
                                    -<b><?php echo ucwords($contact_name); ?></b>
                                </p>
                            </div>
                            <div class="share_instructions">
                                <h1>Participation Goal</h1>
                                <ul class="piw_participation_requirement_table">
                                    <li>-20 Quality Emails <span>*Most Important*</span></li>
                                    <li>-5 Text Messages</li>
                                    <li>-1 Post to Facebook</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                            <?php include_once ( get_template_directory() . '/prev_next_buttons.php' ); ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php endwhile; 