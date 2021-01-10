<?php

use \classes\app\download_report\Download_Report;
use \classes\app\download_report\Download_Report_Subgroups;
use \classes\app\fundraiser\Fundraiser_Ended;
use \classes\app\fundraiser\Fundraiser_Statistics;
use \classes\app\fundraiser\Fundraiser_Media;
use \classes\models\tables\Subgroups;
use \classes\app\download_report\Subgroup_Participant_Data;

/**
 * ADMIN DASHBOARD/REPORTS
 * DASHBOARD
 */
$f_id = get_the_ID();

/**
 * Download Report Spreadsheet.
 */
if ( isset( $_GET['report'] ) && $_GET['report'] == 'true' ) {

	$subgroups_table = new Subgroups();
	$subgroups       = $subgroups_table->getSubgroups( $f_id );
	if ( $subgroups ) {
		$download_report = new Download_Report_Subgroups( $f_id );
		$download_report->init();
		exit();
	} else {
		$download_report = new Download_Report( $f_id );
		$download_report->init();
		exit();
	}
}

load_class( 'payment_records.class.php' );
load_class( 'participant_records.class.php' );
load_class( 'participants.class.php' );

$payments                   = new Payment_Records();
$sharing                    = new Participant_Sharing_Totals();
$participants               = new Participants();
$fundraise_mediaObj         = new Fundraiser_Media();
$subgroups_participant_data = new Subgroup_Participant_Data();
$subgroups_table_info       = $subgroups_participant_data->get_subgroup_aggregation_for( $f_id );

/*
 * check participant ID : $uid
 */
$uid = 0;

if ( isset( $wp_query->query_vars['media'] ) ) {
    $media = urldecode( $wp_query->query_vars['media'] );
} else {
    $media = '';
}
/*
 *  query_vars['uid'] is not exist : general landing pgae
 */
if ( isset( $wp_query->query_vars['uid'] ) ) {
    $uid = urldecode( $wp_query->query_vars['uid'] );
} else {
    $uid = 0;
}

if ( isset( $wp_query->query_vars['semail'] ) ) {
    $semail = urldecode( $wp_query->query_vars['semail'] );
} else {
    $semail = '';
}

load_class( 'page.invite_sms.class.php' );
$invite_sms = new Page_Invite_SMS( $uid, $f_id, 'spread_word' );

/**
 * Get Fundraiser Goal
 */
load_class( 'goals.class.php' );
$goal = new Goals;

// Get the fundraiser ID
if ( is_single() ) {
    $post_id = get_the_ID();
} else {
    $post_id = (int) $_GET['fundraiser_id'];
}

// Fundraiser info
$title = get_the_title( $post_id );

$image_url = $fundraise_mediaObj->get_fundraiser_logo( $f_id );
$status    = get_post_status( $post_id );
$site_name = get_bloginfo( "name" );
$post      = get_post( $post_id );

$author_id       = $post->post_author;
$author_id       = get_userdata( $author_id );
$fundraiser_name = $author_id->first_name . " " . $author_id->last_name;

//Fundraiser End
$fundraiser_end = new Fundraiser_Ended( $post_id );
$ended          = $fundraiser_end->check_end();
$dayleft        = $fundraiser_end->get_fundraiser_enddate();

// Set the goal info
$goal_amount = $goal->get_goal( $post_id );
$fund_amount = $goal->get_amount( $post_id );

$public_goal = $goal_amount;

if ( $ended == false ) {
    while ( $public_goal <= $fund_amount ) {
        $public_goal = $public_goal + 1000;
    }
}

$currency         = '$';
$percentile       = ( $fund_amount / $public_goal ) * 100;
$percentile       = ( $percentile > 100 ) ? 100 : $percentile;
$supporters_total = $goal->get_num_supporters( $post_id );
$supporters       = $goal->get_donators( $post_id );

// URLs
$base_url           = get_site_url();
$fundraiser_string  = '/donation/?fundraiser_id=' . $post_id;
$permalink          = get_permalink( $post_id );
$permalink_facebook = urlencode( $permalink . 'f/' . $uid );
$permalink_twitter  = urlencode( $permalink . 't/' . $uid );
$source             = '';

// Corporate Sponsors
$corporate_sponsors = get_field( 'corporate_sponsors', $post_id );

$participants_count     = $participants->get_filtered_participant_ids_by_fid_count( $f_id );
$fundraiser_total_share = ( $participants_count > 0 ) ? $participants->total_shares_count_by_fid( $f_id ) : 0;

$sadmin    = json_decode( get_user_meta( $uid, 'campaign_sadmin', true ) );
$author_id = get_post_field( 'post_author', $f_id );

$user_info = get_userdata( $uid );

/**
 * Print instructions.
 */
if ( isset( $_GET['print_inst'] ) && $_GET['print_inst'] == 'true' ) {
    load_class( 'print_instructions.class.php' );
    $instructions = new Print_Instructions( $f_id, $uid );
    $instructions->init();
    exit();
}
/**
 * Print parent letter.
 */
if ( isset( $_GET['print_parent'] ) && $_GET['print_parent'] == 'true' ) {
    load_class( 'print_parent_letter.class.php' );
    $instructions = new Print_Parent_Letter( $f_id, $uid );
    $instructions->init();
    exit();
}
get_header( 'general' );

$is_admin = 0;

use \classes\models\tables\Donation_Comments;

$donation_comments = new Donation_Comments();
$comments          = $donation_comments->get_by_fundraiser_id( $f_id );

while ( have_posts() ) : the_post();
    if ( is_user_logged_in() ) {
        if ( current_user_can( 'administrator' ) ) {
            $is_admin = 1;
        }
    }
    ?>
    <script>
        var ended = '<?php echo $ended ?>';
        var is_admin = <?php echo $is_admin ?>;
        var pecent = '<?php echo $percentile ?>';
        $(document).ready(function () {
            if ( ended ) {
                if ( !is_admin ) {
                    $(".sec_header.landing").css("display", "none");
                    $("header").removeClass("landing_page_header");
                }
            }
            comment_moreless(150, $(".comment_text"));
        })
    </script>
    <!-- Participant Landing Page -->
    <!--MAIN start-->
    <main>
        <!--LANDING PAGE BANNER start-->
        <div class="landing_page_banner <?php echo ($is_admin) ? 'admin' : '' ?> <?php echo (is_mobile_new() && !is_user_logged_in()) ? 'nologin' : '' ?>" >

            <!--CONTAINER start-->
            <div class="container remove_padding_mobile">
            <?php if ( $author_id == $uid || in_array_my( $post_id, $sadmin ) || $uid == 0 ) { ?>
                <div class="row">
                    <?php if ( $is_admin == 1 ) { ?>
                        <div class="join_code">
                            <h2>
                                Participant join code:
                                <b><?php echo get_post_meta( get_the_ID(), 'join_code', true ); ?></b>
                                <em>|</em>
                                <br>
                                Admin join code:
                                <a href="#" class="show_code">Click to Show Code</a>
                                <span class="admin_code"><b><?php echo get_post_meta( get_the_ID(), 'join_code_sadmin', true ); ?></b></span>
                                <script>
                                    $(".show_code").on("click", function () {
                                        $(this).hide();
                                        $(".admin_code").show()
                                    })
                                </script>
                            </h2>
                        </div>
                    <?php } ?>

                    <div class="col-md-5 col-sm-5 col-xs-12 col col_left">
                        <div class="fundraiser_logo">
                            <img src="<?php
                            echo ( $image_url != null ) ?
                                    $image_url : bloginfo( 'template_directory' ) . '/assets/images/default-logo.png';
                            ?>"
                                 class="logo-img"/>
                        </div>
                    </div>

                    <div class="col-md-7 col-sm-7 col-xs-12 col col_right rightarea">
                        <h1><?php echo $title; ?></h1>
	                    <?php if ( !$fundraiser_end->is_in_ssi_extra_period() ) { ?>
                        <div class="price_bar">
                            <div class="goal">
                                <h5><?php echo $currency; ?> <?php echo number_format( $public_goal ); ?><b>Fundraiser
                                        Goal</b></h5>
                            </div>

                            <?php if ( get_field( 'show_progressbar', $post_id ) == 1 ) { ?>
                                <div id="progressBar2" class="default">
                                    <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/layer22.png"
                                         alt="">
                                    <div></div>
                                </div>
                                <script>
                                    //                                    $(document).ready(function () {
                                    $(window).load(function () {
                                        if ( pecent != 0 )
                                            progressBar(<?php echo $percentile; ?>, $('#progressBar2'),<?php echo $public_goal ?>, <?php echo $fund_amount ?>);


                                    });
                                    $(window).resize(function () {
                                        if ( pecent != 0 )
                                            progressBar(<?php echo $percentile; ?>, $('#progressBar2'),<?php echo $public_goal ?>, <?php echo $fund_amount ?>);
                                    })
                                </script>
                                <?php
                            }
                            ?>

                            <div class="total_gain desk">
                                <!--                                <h5>--><?php //echo $currency;             ?><!-- -->
                                <?php //echo number_format($fund_amount);    ?><!--<b>Total-->
                                <!--                                        Raised</b></h5>-->
                                <h5><span>$0</span><b>Total Raised</b></h5>
                            </div>
                        </div>
	                    <?php } ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 mob_bar">
	                    <?php if ( !$fundraiser_end->is_in_ssi_extra_period() ) { ?>
                        <div class="price_bar extra_padding">
                            <div class="goal">
                                <h5><?php echo $currency; ?> <?php echo number_format( $public_goal ); ?><b>Fundraiser
                                        Goal</b></h5>
                            </div>

                            <?php if ( get_field( 'show_progressbar', $post_id ) == 1 ) { ?>
                                <div id="progressBar1" class="default">
                                    <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/layer22.png"
                                         alt="">
                                    <div></div>
                                </div>
                                <script>
                                    $(window).load(function () {
                                        if ( pecent != 0 )
                                            progressBar1(<?php echo $percentile; ?>, $('#progressBar1'),<?php echo $public_goal ?>, <?php echo $fund_amount ?>);
                                    });
                                    $(window).resize(function () {
                                        if ( pecent != 0 )
                                            progressBar(<?php echo $percentile; ?>, $('#progressBar1'),<?php echo $public_goal ?>, <?php echo $fund_amount ?>);
                                    })
                                </script>
                                <?php
                            }
                            ?>

                            <div class="total_gain mob" style="left:90px !important">
                                <h5><span>$0</span><b>Total Raised</b></h5>
                            </div>
                        </div>
	                    <?php } ?>
                    </div>
                </div>

            <?php } else { ?>

                <?php
                    $goal    = _PARTICIPATION_GOAL;
                    $p_amount = $payments->get_total_by_user_id( $uid, $f_id );
                    $current = $p_amount;
                    while ( $current >= $goal ) {
                        $goal = $goal + 100;
                    }
                    
                    $participant_percentile       = ( $current / $goal ) * 100;
                    $participant_percentile       = ( $participant_percentile > 100 ) ? 100 : $participant_percentile;
                ?>

                <div class="participant_landing_page_mobile">
                    <div>
                        <h1 class="name" ><?php if ( $user_info ) echo $user_info->display_name; ?></h1>
                    </div>
                    <div class="logo_and_quote">
                        <div class="logo_container">
                                <?php
                                    if ( is_mobile_new() ) {
                                        $avatar_aux =  get_avatar( $uid, 150 );
                                        echo preg_replace ('/class="(.*)"/m', 'class=""', $avatar_aux);

                                    } else {
                                        $avatar_aux =  get_avatar( $uid, 283 );
                                        echo preg_replace ('/class="(.*)"/m', 'class=""', $avatar_aux);
                                    }
                                ?>
                        </div>
                        <div class="quote_container">
                            <h2 class="quote">&ldquo;thank you for helping me reach my goal&rdquo;</h2>
                        </div>
                    </div>
                    <div class="participant_bar">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 mob_bar">
                                <div class="price_bar">
                                    <?php if ( get_field( 'show_progressbar', $post_id ) == 1 ) { ?>
                                        <div id="progressBar1" class="default">
                                            <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/layer22.png"
                                                alt="">
                                            <div></div>
                                        </div>
                                        <script>
                                            $(window).load(function () {
                                                if ( <?php echo $participant_percentile; ?> != 0 )
                                                    progressBar1(<?php echo $participant_percentile; ?>, $('#progressBar1'),<?php echo $goal ?>, <?php echo $p_amount ?>);
                                            });
                                            $(window).resize(function () {
                                                if ( <?php echo $participant_percentile; ?> != 0 )
                                                    progressBar(<?php echo $participant_percentile; ?>, $('#progressBar1'),<?php echo $goal ?>, <?php echo $p_amount ?>);
                                            })
                                        </script>
                                        <?php
                                    }
                                    ?>
                                    <div style="display: flex;flex-direction: row;justify-content: space-between;">
                                        <h5 style="text-align: left;"><span class="quote">$<?php echo $p_amount; ?></span><b>Total Raised</b></h5>
                                        <h5 style="text-align: right;" class="quote"><?php echo $currency; ?> <span class="quote"><?php echo number_format( $goal ); ?></span> <b>Participation Goal</b></h5>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


                <div class="participant_landing_page_desktop">
                    <div class="side_container">
                        <div class="logo_container">
                                <?php
                                    if ( is_mobile_new() ) {
                                        $avatar_aux =  get_avatar( $uid, 150 );
                                        echo preg_replace ('/class="(.*)"/m', 'class=""', $avatar_aux);

                                    } else {
                                        $avatar_aux =  get_avatar( $uid, 283 );
                                        echo preg_replace ('/class="(.*)"/m', 'class=""', $avatar_aux);
                                    }
                                ?>
                        </div>

                        <div>
                            <div class="name_and_logo_container">
                                <h1 class="name" ><?php if ( $user_info ) echo $user_info->display_name; ?></h1>
                            
                                <h2 class="quote">&ldquo;thank you for helping<br> me reach my goal&rdquo;</h2>
                            </div>
                            <div class="price_bar">
                                <?php
                                    $goal    = _PARTICIPATION_GOAL;
                                    $p_amount = $payments->get_total_by_user_id( $uid, $f_id );
                                    $current = $p_amount;
                                    while ( $current >= $goal ) {
                                        $goal = $goal + 100;
                                    }
                                    
                                    $participant_percentile       = ( $current / $goal ) * 100;
                                    $participant_percentile       = ( $participant_percentile > 100 ) ? 100 : $participant_percentile;
                                ?>
                                <div class="goal">
                                    <h5><?php echo $currency; ?> <?php echo number_format( $goal ); ?><b>Participation
                                            Goal</b></h5>
                                </div>

                                <?php if ( get_field( 'show_progressbar', $post_id ) == 1 ) { ?>
                                    <div id="progressBar2" class="default">
                                        <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/layer22.png"
                                            alt="">
                                        <div></div>
                                    </div>
                                    <script>
                                        //                                    $(document).ready(function () {
                                        $(window).load(function () {
                                            if ( <?php echo $participant_percentile; ?> != 0 )
                                                progressBar(<?php echo $participant_percentile; ?>, $('#progressBar2'),<?php echo $goal ?>, <?php echo $p_amount ?>);
                                        });
                                        $(window).resize(function () {
                                            if ( <?php echo $participant_percentile; ?> != 0 )
                                                progressBar(<?php echo $participant_percentile; ?>, $('#progressBar2'),<?php echo $goal ?>, <?php echo $p_amount ?>);
                                        })
                                    </script>
                                    <?php
                                }
                                ?>

                                <div class="total_gain desk">
                                    <h5><span>$<?php echo $p_amount?></span><b>Total Raised</b></h5>
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>

            <?php } ?>
                <!--CONTAINER end-->
                <?php
                if ( $is_admin == 1 ) {
                    //get fundraiser statistics
                    $fundraiser_statistics   = new Fundraiser_Statistics( $post_id );
                    $participation_score     = $fundraiser_statistics->participation_score_formatted();
                    $email_quality_score     = $fundraiser_statistics->email_quality_score_formatted();
                    $participants_score      = $fundraiser_statistics->participant_score_formatted();
                    ?>
                    <!-- Meter pane -->
                    <div class="row statistics_pane" style="">

                        <div id="meter1" class="fundraiser_meter col-md-4 col-sm-4" ></div>   
                        <div id="meter2" class="fundraiser_meter col-md-4 col-sm-4" ></div>
                        <div id="meter3" class="fundraiser_meter col-md-4 col-sm-4" ></div>
                        <script>
                            $(window).load(function () {
                                update_guage();

                            })
                            $(window).resize(function () {
                                update_guage();
                            })

                            function update_guage() {
                                $(".meter_text").remove()
                                var values = [];
                                var p_score = <?php echo $participation_score; ?>;
                                values[0] = (p_score > meterObj[0].max_value) ? meterObj[0].max_value : p_score;
                                var email_score = <?php echo $email_quality_score; ?>;
                                values[1] = (email_score > meterObj[1].max_value) ? meterObj[1].max_value : email_score;
                                var donor_score = <?php echo $participants_score; ?>;
                                values[2] = (donor_score > meterObj[2].max_value) ? meterObj[2].max_value : donor_score;

                                for ( var i = 0; i < 3; i++ ) {
                                    meterChart[i].series[0].data[0].update(values[i]);
                                    var meterText = '<p class="meter_text" style="">' + meterObj[i].text + ': ' + values[i] + '%</p>';
                                    $("#meter" + (i + 1)).append($(meterText))
                                }
                            }

                        </script>
                        <style>

                        </style>
                    </div>

                <?php if ( $subgroups_table_info ) { ?>
                    <style>
                        .subgroups_table td {
                            color: white;
                            text-align: center;
                        }

                        .subgroups_table .table_head tr th {
                            display: table-cell;
                        }

                    </style>

                    <div class="subgroups_table participant_table">
                        <h3>Group Summary (<?php echo count( $subgroups_table_info ); ?>)</h3>

                        <table class="table table-bordered ">
                            <thead class="table_head">
                            <tr>
                                <th>name</th>
                                <th>parent shares</th>
                                <th>email</th>
                                <th class="hide_mob">facebook</th>
                                <th class="hide_mob">sms</th>
                                <th>total supporters</th>
                                <th>total raised</th>
                            </tr>
                            </thead>

                            <tbody class="">
			                <?php foreach ( $subgroups_table_info as $subgroup_name => $subgroup_stats ) { ?>
                                <tr>
                                    <td><?php echo $subgroup_name; ?></td>
                                    <td><?php echo $subgroup_stats->parents; ?></td>
                                    <td><?php echo $subgroup_stats->email; ?></td>
                                    <td class="hide_mob"><?php echo $subgroup_stats->facebook; ?></td>
                                    <td class="hide_mob"><?php echo $subgroup_stats->smsp; ?></td>
                                    <td><?php echo $subgroup_stats->supporters; ?></td>
                                    <td data-total="<?php echo $subgroup_stats->net_amount; ?>">
                                        $<?php echo $subgroup_stats->net_amount; ?></td>
                                </tr>
			                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <script>
                        function removeColumnsMobile() {

                            if (window.matchMedia("(max-width: 767px)").matches) {
                                var elements = document.querySelectorAll(".subgroups_table .hide_mob");
                                for (var i = 0; i < elements.length; i++) {
                                    elements[i].parentNode.removeChild(elements[i]);
                                }
                            }
                        }

                        removeColumnsMobile();
                        window.onresize = removeColumnsMobile;
                    </script>
                <?php } ?>

                    <!--PARTICIPANTS TABLE start-->
                    <div class="participant_table">
                        <?php
                        global $wpdb;
                        $f_id                    = (int) $f_id;
                        // Get the participants from the Fundraiser ID
                        $campaign_participations = $participants->get_filtered_participant_ids_by_fid( $f_id );
                        $myrows1                 = $wpdb->get_results( "SELECT * FROM participant_fundraiser_details WHERE fundraiser = '{$f_id}' AND (total >= " . _PARTICIPATION_GOAL . " OR email >=20) ORDER BY total DESC, email DESC, participant_name ASC", OBJECT );
                        $myrows2                 = $wpdb->get_results( "SELECT * FROM participant_fundraiser_details WHERE fundraiser = '{$f_id}' AND (email >= 10 AND email < 20) AND total < " . _PARTICIPATION_GOAL . " ORDER BY total DESC, email DESC, participant_name ASC", OBJECT );
                        $myrows3                 = $wpdb->get_results( "SELECT * FROM participant_fundraiser_details WHERE fundraiser = '{$f_id}' AND email <10 AND total < " . _PARTICIPATION_GOAL . " ORDER BY total DESC, email DESC, participant_name ASC", OBJECT );

                        if ( !empty( $campaign_participations ) ) {
                            ?>
                            <h3>Registered Participants (<?php echo count($campaign_participations);?>)</h3>
                            <div class="table_head">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>User ID</th>
                                            <th>parent shares</th>
                                            <th>Email Shares</th>
                                            <th>facebook shares</th>
                                            <th>SMS Donations</th>
                                            <th>Total Supporters</th>
                                            <th>Total raised</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            <div class="table_body">
                                <table class="table table-bordered">
                                    <tbody>
                                        <?php
                                        if ( !empty( $myrows1 ) ) {
                                            foreach ( $myrows1 as $participant ) {
                                                if ( in_array_my( $participant->participant_id, $campaign_participations ) ) {
                                                    if ( $participant->total >= _PARTICIPATION_GOAL || $participant->email >= 20 ) {
                                                        $color = 'rgb(112, 173, 71)';
                                                    }
                                                    ?>
                                                    <tr class="green">
                                                        <td
                                                                class="user_id-<?php echo $participant->participant_id; ?>">
	                                                        <?php echo $participant->participant_name; ?>
	                                                        <?php if ( $participant->total == "0" ) { ?>
                                                                <a href="#" class="delete_participant"
                                                                   data-participant_id="<?php echo htmlentities( json_encode( $participant ) ); ?> ">
                                                                    <img style="max-width: 75%"
                                                                         src="<?php bloginfo( 'template_directory' ); ?>/assets/images/close-btn1.png">
                                                                </a>
	                                                        <?php } ?>
                                                        </td>
                                                        <td><?php echo $participant->participant_id; ?></td>
                                                        <td><?php echo $participant->parents; ?></td>
                                                        <td><?php echo $participant->email; ?></td>
                                                        <td><?php echo $participant->facebook; ?></td>
                                                        <td>$<?php echo $participant->sms; ?></td>
                                                        <td><?php echo $participant->supporters; ?></td>
                                                        <td data-total="<?php echo $participant->total; ?>">
                                                            $<?php echo $participant->total; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                        }
                                        if ( !empty( $myrows2 ) ) {
                                            foreach ( $myrows2 as $participant ) {
                                                if ( in_array_my( $participant->participant_id, $campaign_participations ) ) {
                                                    if ( $participant->email >= 10 && $participant->email < 20 ) {
                                                        $color = 'rgb(0, 176, 240)';
                                                    }
                                                    ?>
                                                    <tr class="blue">
                                                        <td
                                                                class="user_id-<?php echo $participant->participant_id; ?>">
	                                                        <?php echo $participant->participant_name; ?>
	                                                        <?php if ( $participant->total == "0" ) { ?>
                                                                <a href="#" class="delete_participant"
                                                                   data-participant_id="<?php echo htmlentities( json_encode( $participant ) ); ?> ">
                                                                    <img style="max-width: 75%"
                                                                         src="<?php bloginfo( 'template_directory' ); ?>/assets/images/close-btn1.png">
                                                                </a>
	                                                        <?php } ?>
                                                        </td>
                                                        <td><?php echo $participant->participant_id; ?></td>

                                                        <td><?php echo $participant->parents; ?></td>
                                                        <td><?php echo $participant->email; ?></td>
                                                        <td><?php echo $participant->facebook; ?></td>
                                                        <td>$<?php echo $participant->sms; ?></td>
                                                        <td><?php echo $participant->supporters; ?></td>
                                                        <td data-total="<?php echo $participant->total; ?>">
                                                            $<?php echo $participant->total; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                        }

                                        if ( !empty( $myrows3 ) ) {
                                            foreach ( $myrows3 as $participant ) {
                                                if ( in_array_my( $participant->participant_id, $campaign_participations ) ) {
                                                    if ( $participant->email < 10 ) {
                                                        $color = 'rgb(255, 0, 0)';
                                                    }
                                                    ?>
                                                    <tr class="red">
                                                        <td
                                                                class="user_id-<?php echo $participant->participant_id; ?>">
	                                                        <?php echo $participant->participant_name; ?>
	                                                        <?php if ( $participant->total == "0" ) { ?>
                                                                <a href="#" class="delete_participant"
                                                                   data-participant_id="<?php echo htmlentities( json_encode( $participant ) ); ?> ">
                                                                    <img style="max-width: 75%"
                                                                         src="<?php bloginfo( 'template_directory' ); ?>/assets/images/close-btn1.png">
                                                                </a>
	                                                        <?php } ?>
                                                        </td>
                                                        <td><?php echo $participant->participant_id; ?></td>
                                                        <td><?php echo $participant->parents; ?></td>
                                                        <td><?php echo $participant->email; ?></td>
                                                        <td><?php echo $participant->facebook; ?></td>
                                                        <td>$<?php echo $participant->sms; ?></td>
                                                        <td><?php echo $participant->supporters; ?></td>
                                                        <td data-total="<?php echo $participant->total; ?>">
                                                            $<?php echo $participant->total; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                        } else {
                            //echo '<h2>No Participants Found</h2>';
                        }
                        ?>

                    </div>
                    <!--PARTICIPANTS TABLE end-->
                <?php } ?>
            </div>
        </div>
        <!--LANDING PAGE BANNER end-->


        <!--LANDING PAGE MAIN CONTENT start-->
        <div class="landing_page_main_content">

            <div class="container">

                <div class="row">

                    <div class="col-md-5 col-sm-5 col-xs-12 col eql col_right">
                        <?php
                        //$p_amount = $payments->get_total_by_user_id( $uid, $f_id );
                        ?>
                        <div class="widgets individual_profile">

                            <?php if ( $author_id == $uid || in_array_my( $post_id, $sadmin ) || $uid == 0 ) { ?>

                                <!-- <div class="user_name">
                                    <p>“thank you for helping <br>
                                        me reach my goal”</p>
                                </div> -->
                                <?php
                            } else {

                                ?>


                            <?php } ?>

                            <?php
                            if ( $ended == false ) {
                                // Donate URL Params
                                $media_string = ( isset( $media ) ) ? '&media=' . $media : '';
                                $uid_string   = ( isset( $uid ) ) ? '&uid=' . $uid : '';
                                $email_string = ( isset( $semail ) ) ? '&semail=' . $semail : '';

                                // Donation URL
                                $donation_url = $base_url . $fundraiser_string . $media_string . $uid_string . $email_string;
                                $donation_btn = "Donate Now";
                            } else {
                                $donation_url = "#";
                                $donation_btn = "Campaign Ended";
                            }
                            ?>
                          

                                <span class="display_table"><a class="donate_link"
                                                           href="<?php echo $donation_url; ?>">
                                    <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon13.png" alt="">
                                    <?php echo $donation_btn ?>
                                </a></span>


                            <div id="how_to_mail_a_check">
                                <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/email-icon-orange.png"><a style="text-decoration: underline" href="/how-to-mail-a-check?fundraiser_id=<?php echo $f_id; ?>&uid=<?php echo $uid; ?>" id="how_to_mail_a_check_a">Or mail a check</a>
                            </div>

                            <?php if ( !$ended ) { ?>
                                <?php if ( !is_mobile_new() ) { ?>
                                    <a href="javascript:void(0);" onclick="popup_facebookshare()" class="share_link"
                                       style="background: #3c538b;">
                                        <i class="fa fa-facebook" aria-hidden="true"></i> share to facebook
                                    </a>
                                <?php } else { ?>

                                    <a style="background-color: #3c538b;" class="share_link"
                                       href="https://www.facebook.com/dialog/feed?app_id=<?php echo _FACEBOOK_APP_ID ?>&display=popup&caption=<?php echo urlencode( $title ); ?>&link=<?php echo $permalink_facebook; ?>&redirect_uri=<?php echo urlencode( $permalink . 'c/' . $uid ); ?>">
                                        <i class="fa fa-facebook" aria-hidden="true"></i> share to facebook
                                    </a>
                                <?php } ?>
                            <?php } ?>

                          

                            <div class="days_left">
                                <?php echo $dayleft; ?>
                            </div>

                            <?php if ( is_mobile_new() == false ) { ?>
                                <div class="individual_profile" style="padding-top:10px;">
                                <div class="user_name user">
                                    <div class="useravada_wrapper">
                                        <div class="">
                                            <img src="<?php
                                                echo ( $image_url != null ) ?
                                                $image_url : bloginfo( 'template_directory' ) . '/assets/images/default-logo.png';
                                            ?>"
                                            class="logo-img"/>
                                        </div>
                                    </div>
                                    <h3 style="color: #58595B;"><?php echo $title; ?></h3>
                                </div>
                                <div class="total_goal">
                                    <?php if ( get_field( 'show_progressbar', $post_id ) == 1 && !$fundraiser_end->is_in_ssi_extra_period() ) { ?>
                                        <div id="progressBar5" class="big-green">
                                            <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/layer33.png"
                                                    alt="">
                                            <div></div>
                                        </div>
                                        <script>
                                            jQuery(window).load(function () {
                                                progressBar(<?php echo $percentile; ?>, jQuery('#progressBar5'),<?php echo $public_goal ?>, <?php echo $fund_amount ?>);
                                            });
                                            jQuery(window).resize(function () {
                                                progressBar(<?php echo $percentile; ?>, jQuery('#progressBar5'),<?php echo $public_goal ?>, <?php echo $fund_amount ?>);
                                            });
                                        </script>
                                    
                                        <h5><b style="color: black;">$<?php echo number_format( $fund_amount ); ?></b> of
                                            $<?php echo number_format( $public_goal ); ?> GROUP GOAL
                                        </h5>
                                    <?php }  ?>
                                </div>
                            </div>
                            <?php } ?>

                        </div>

                        <?php if ( is_mobile_new() == false ) include ( 'components/fundraiser/landing-page/donation-list-component.php' ); ?>

                    </div>

                    <div class="col-md-7 col-sm-7 col-xs-12 col eql col_left " >
                        <div class="widgets video_sec">
                            <?php
                            $iframe = get_field( 'youtube_url' );
                            if ( !empty( $iframe ) ) {
                                ?>
                                <li>
                                    <div class="res_vid">
                                        <?php
                                        $success = preg_match( '/src="(.+?)"/', $iframe, $matches );
                                        if ( $success ) {
                                            $src        = $matches[1];
                                            $params     = array(
                                                'controls' => 1,
                                                'hd'       => 1,
                                                'autohide' => 1,
                                                'rel'      => 0
                                            );
                                            $new_src    = add_query_arg( $params, $src );
                                            $iframe     = str_replace( $src, $new_src, $iframe );
                                            $attributes = 'frameborder="0"';
                                            $iframe     = str_replace( '></iframe>', ' ' . $attributes . '></iframe>', $iframe );
                                            $iframe     = str_replace( 'width="640" height="360"', 'width="640" height="380"', $iframe );
                                            echo $iframe;
                                        }
                                        ?>
                                    </div>
                                </li>
                                <?php
                            }
                            ?>
                            <div id="please_share_container">
                                <p id="please_share_bar_text">please share</p>
                                <hr id="please_share_bar">
                            </div>
                            <!-- upload video rul or youtube link--------------->
                            <?php if ( !$ended ) { ?>
                                <ul class="video_share">

                                    <li class="fb">

                                        <?php if ( !is_mobile_new() ) { ?>
                                            <a href="javascript:void(0);" onclick="popup_facebookshare()"
                                               style="background-color: #3c538b;">
                                                <i class="fa fa-facebook" aria-hidden="true"></i> facebook
                                            </a>
                                        <?php } else { ?>

                                            <a style="background-color: #3c538b;"
                                               href="https://www.facebook.com/dialog/feed?app_id=<?php echo _FACEBOOK_APP_ID ?>&display=popup&caption=<?php echo urlencode( $title ); ?>&link=<?php echo $permalink_facebook; ?>&redirect_uri=<?php echo urlencode( $permalink . 'c/' . $uid ); ?>">
                                                <i class="fa fa-facebook" aria-hidden="true"></i> facebook
                                            </a>
                                        <?php } ?>
                                    </li>
                                    <li class="twitter">
                                        <?php if ( !is_mobile_new() ) { ?>
                                            <a href="javascript:void(0);" onclick="popup_tweetshare()"
                                               style="background-color: #60a5e5;">
                                                <i class="fa fa-twitter" aria-hidden="true"></i> Twitter
                                            </a>
                                        <?php } else { ?>
                                            <a href="https://twitter.com/share?url=<?php echo $permalink_twitter; ?>"
                                               style="background-color: #60a5e5;" target="_blank">
                                                <i class="fa fa-twitter" aria-hidden="true"></i>Twitter</a>
                                        <?php } ?>
                                    </li>
                                    <li class="text">
                                        <?php if ( !is_mobile_new() ) { ?>
                                            <a href="<?php bloginfo( 'url' ); ?>/invite-by-text-message-sms/?fundraiser_id=<?php echo get_the_ID(); ?>&uid=<?php echo $uid ?>&action=spread&display_type=single"
                                               class="fancyboxInvite" data-fancybox-type="iframe"
                                               style="background-color: #46ce53;">
                                                <i class="fa fa-mobile" aria-hidden="true"></i> Text
                                            </a>
                                            <?php
                                        } else {
                                            $text = $invite_sms->contact_import_button();
                                        }
                                        ?>

                                    </li>
                                    <li class="email">
                                        <a href="<?php bloginfo( 'url' ); ?>/invite-by-email/?fundraiser_id=<?php echo get_the_ID(); ?>&uid=<?php echo $uid ?>&action=spread&display_type=single"
                                           class="fancyboxInvite" data-fancybox-type="iframe"
                                           style="background-color: #52b6d5;">
                                            <i class="fa fa-envelope" aria-hidden="true"></i> Email
                                        </a>
                                    </li>
                                </ul>
                            <?php } ?>
                            <div class="shares">
                                <h6><?php echo $fundraiser_total_share; ?> <b>shares</b></h6>
                            </div>
                        </div>
                        <?php if ( is_mobile_new() && $author_id != $uid && ! in_array_my( $post_id, $sadmin ) && $uid != 0 ) { ?>
                            <div class="individual_profile" style="padding-top:10px;">
                                <div class="user_name user">
                                    <div class="useravada_wrapper">
                                        <div class="">
                                            <img src="<?php
                                                echo ( $image_url != null ) ?
                                                $image_url : bloginfo( 'template_directory' ) . '/assets/images/default-logo.png';
                                            ?>"
                                            class="logo-img"/>
                                        </div>
                                    </div>
                                    <h3 style="color: #58595B;"><?php echo $title; ?></h3>
                                </div>
                                <div class="total_goal">
                                    <?php if ( get_field( 'show_progressbar', $post_id ) == 1 && !$fundraiser_end->is_in_ssi_extra_period() ) { ?>
                                        <div id="progressBar5" class="big-green">
                                            <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/layer33.png"
                                                    alt="">
                                            <div></div>
                                        </div>
                                        <script>
                                            jQuery(window).load(function () {
                                                progressBar(<?php echo $percentile; ?>, jQuery('#progressBar5'),<?php echo $public_goal ?>, <?php echo $fund_amount ?>);
                                            });
                                            jQuery(window).resize(function () {
                                                progressBar(<?php echo $percentile; ?>, jQuery('#progressBar5'),<?php echo $public_goal ?>, <?php echo $fund_amount ?>);
                                            });
                                        </script>
                                    
                                        <h5><b style="color: black;">$<?php echo number_format( $fund_amount ); ?></b> of
                                            $<?php echo number_format( $public_goal ); ?> GROUP GOAL
                                        </h5>
                                    <?php }  ?>
                                </div>
                                <img src="<?php bloginfo( 'template_directory' );?>/assets/images/border5.png">
                            </div>
                        <?php } ?>

                        <div class="widgets about_fundraiser">

                            <h3>About This Fundraiser</h3>
                            <!--  This is Fundraiser messgae-->
                            <p><?php echo nl2br( get_post_meta( get_the_ID(), 'campaign_msg', true ) ); ?></p>

                        </div>

                        <?php if ( is_mobile_new() ) include ( 'components/fundraiser/landing-page/donation-list-component.php' ); ?>

                        <div class="widgets make_donation">

                            <h3>Make A Donation </h3>

                            <ul>
                                <li>
                                    <div class="icon">
                                        <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon12.png"
                                             alt="">
                                    </div>
                                    <div class="amount">
                                        <p>$500 (USD)</p>
                                    </div>
                                    <div class="btn">
                                        <span class="display_table"><a
                                                href="<?php echo $donation_url . '&donation_amount=500' ?>"><?php echo $donation_btn; ?></a></span>
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon12.png"
                                             alt="">
                                    </div>
                                    <div class="amount">
                                        <p>$250 (USD)</p>
                                    </div>
                                    <div class="btn">
                                        <span class="display_table"><a
                                                href="<?php echo $donation_url . '&donation_amount=250' ?>"><?php echo $donation_btn; ?></a></span>
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon12.png"
                                             alt="">
                                    </div>
                                    <div class="amount">
                                        <p>$150 (USD)</p>
                                    </div>
                                    <div class="btn">
                                        <span class="display_table"><a
                                                href="<?php echo $donation_url . '&donation_amount=150' ?>"><?php echo $donation_btn; ?></a></span>
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon12.png"
                                             alt="">
                                    </div>
                                    <div class="amount">
                                        <p>$100 (USD)</p>
                                    </div>
                                    <div class="btn">
                                        <span class="display_table"><a
                                                href="<?php echo $donation_url . '&donation_amount=100' ?>"><?php echo $donation_btn; ?></a></span>
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon12.png"
                                             alt="">
                                    </div>
                                    <div class="amount">
                                        <p>$50 (USD)</p>
                                    </div>
                                    <div class="btn">
                                        <span class="display_table"><a
                                                href="<?php echo $donation_url . '&donation_amount=50' ?>"><?php echo $donation_btn; ?></a></span>
                                    </div>
                                </li>
                            </ul>

                            <h3>Custom Donation amount </h3>

                            <ul class="custom">
                                <li>
                                    <div class="icon">
                                        <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon12.png"
                                             alt="">
                                    </div>
                                    <div class="amount">
                                        <p><span class="custom_curreny">$</span>
                                            <input id="custom_donate_amount" class="amount_ip" autocomplete="off"
                                                   placeholder=""
                                                   onkeypress='return event.charCode >= 48 && event.charCode <= 57'
                                                   type="number"
                                                   name="custom_amount"
                                                   value="<?php
                                                   if ( isset( $_GET['donation_amount'] ) ) {
                                                       echo $_GET['donation_amount'];
                                                   }
                                                   ?>" required="required" maxlength="18" min="10"
                                                   oninvalid="this.setCustomValidity('Please enter at least $10')"
                                                   oninput="setCustomValidity('')" tabindex="1"/></p>


                                    </div>
                                    <div class="btn">
                                        <span class="display_table">
                                            <a href="#" onclick="custom_donation($(this), $('#custom_donate_amount'))"
                                               data-href="<?php echo $donation_url ?>"><?php echo $donation_btn ?></a>
                                        </span>
                                    </div>
                                </li>
                            </ul>


                        </div>

                    </div>
                    
                </div>

            </div>

        </div>
        <!--LANDING PAGE MAIN CONTENT end-->

    </main>
    <!--MAIN end-->

    <div class="modal fade" tabindex="-1" role="dialog" id="confirm_modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                    <h4 class="modal-title">Confirm participant deletion</h4>
                </div>
                <div class="modal-body">
                    <p>Do you want to delete participant: <span id="participant_delete_name" data-id=""></span>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Back</button>
                    <button type="button" class="btn btn-primary has-spinner" data-loading="Deleting..."  id="delete_participant_submit">Delete Participant</button>
                </div>
                <div>
                    <p id="success_response_message" style="text-align: center;padding-top: 25px; color: #02b902; font-weight: bold;"></p>
                    <p id="error_response_message" style="text-align: center;padding-top: 25px;color: red; font-weight: bold;"></p>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <script>

        var USER_ID = false;
        const FUNDRAISER_ID = '<?php echo $f_id; ?>';

        $(document).ready(function () {
            $(".delete_participant").click(function (e) {
                e.preventDefault();

                var participant = $(this).data("participant_id");

                participant = JSON.parse(participant);

                USER_ID = participant.participant_id;

                $("#participant_delete_name").text(participant.participant_name);

                $("#confirm_modal").modal('show');

            });

            $('#confirm_modal').on('hidden.bs.modal', function (e) {

                USER_ID = false;

            });

            $("#delete_participant_submit").click(function (e) {

                $(this).buttonLoader("start");

                var data = {
                    'action': 'fundraiser_delete_participant',
                    'u_id': USER_ID,
                    'f_id': FUNDRAISER_ID,
                };


                $.ajax(
                    "/wp-admin/admin-ajax.php",
                    {
                        type: 'POST',
                        data: data,
                        complete: function (jqXHR, textStatus) {
                            var status = jqXHR.status;
                            var responseJSON = jqXHR.responseJSON;

                            if (status === 200) {
                                $('#success_response_message').text(responseJSON.message);
                                location.reload();
                            } else {
                                $('#error_response_message').text(responseJSON.error);
                                $('#delete_participant_submit').buttonLoader("stop");
                            }
                        }
                    }
                );
            });

        });


    </script>

    <?php
endwhile;?> 
<?php
get_footer();
?>