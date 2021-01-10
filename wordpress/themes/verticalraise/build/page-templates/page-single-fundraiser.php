<?php
/* Template Name: Single Fundraiser */

use \classes\app\download_report\Download_Report;
use \classes\app\download_report\Download_Report_Subgroups;
// Comments
use \classes\models\tables\Donation_Comments;
use \classes\app\fundraiser\Fundraiser_Ended;
use \classes\app\fundraiser\Fundraiser_Statistics;
use \classes\app\fundraiser\Fundraiser_Media;  //Fundraiser Media Class Object
use \classes\models\tables\Subgroups;
use \classes\app\download_report\Subgroup_Participant_Data;

/*
 * FUNDRAISER AUTHOR/SECONDARY ADMIN DASHBOARD
 */
    
load_class( 'participants.class.php' );
load_class( 'goals.class.php' );
load_class( 'participant_records.class.php' );
load_class( 'sharing.class.php' );

$sharing       = new Sharing();                                 // Sharing class object
$fundraiser_ID = $sharing->fundraiser_ID;

if ( !$fundraiser_ID || empty( $fundraiser_ID ) ) {
    header( 'Location: ' . get_bloginfo( 'url' ) . '/my-account' );
    exit();
}

$participants               = new Participants();
$goal                       = new Goals;
$subgroups_participant_data = new Subgroup_Participant_Data();
$subgroups_table_info       = $subgroups_participant_data->get_subgroup_aggregation_for( $fundraiser_ID );

if ( is_user_logged_in() ) {
    global $user_ID;

    $f_id                   = (int) $fundraiser_ID;               // Fundraiser id
    $uid                    = '';                                 // Blank for VRaise Admins
    //total share infor
    $participants_count     = $participants->get_filtered_participant_ids_by_fid_count( $f_id );
    $fundraiser_total_share = ( $participants_count > 0 ) ? $participants->total_shares_count_by_fid( $f_id ) : 0;

    $post_id = (int) $_GET['fundraiser_id'];

    $sadmin    = json_decode( get_user_meta( $user_ID, 'campaign_sadmin', true ) );
    $author_id = get_post_field( 'post_author', $f_id );

    if ( $author_id == $user_ID || in_array_my( $f_id, $sadmin ) ) {
        $uid = $user_ID;
    } else {
        header( 'Location: ' . get_bloginfo( 'url' ) . '/my-account' );
        exit();
    }


    if ( isset( $wp_query->query_vars['media'] ) ) {
        $media = urldecode( $wp_query->query_vars['media'] );
    } else {
        $media = 'c';
    }
//
    // Fundraiser info
    $title = get_the_title( $post_id );

    //get team logo image url;
    $fundraise_mediaObj = new Fundraiser_Media();
    $image_url          = $fundraise_mediaObj->get_fundraiser_logo( $f_id );

    $status          = get_post_status( $post_id );
    $site_name       = get_bloginfo( "name" );
    $post            = get_post( $post_id );
    $author_id       = $post->post_author;
    $author_id       = get_userdata( $author_id );
    $fundraiser_name = $author_id->first_name . " " . $author_id->last_name;

    //Fundraiser End
    $fundraiser_end    = new Fundraiser_Ended( $post_id );
    $ended             = $fundraiser_end->check_end();
    $dayleft           = $fundraiser_end->get_fundraiser_enddate();
    // URLs
    $base_url          = get_site_url();
    $fundraiser_string = '/donation/?fundraiser_id=' . $post_id;
    $permalink         = get_permalink( $post_id );

    if ( $ended == false ) {
        // Donate URL Params
        $media_string = ( isset( $media ) ) ? '&media=' . $media : '';
        $uid_string   = ( isset( $uid ) ) ? '&uid=' . $uid : '';

        // Donation URL
        $donation_url = $base_url . $fundraiser_string . $media_string . $uid_string;
        $donation_btn = "Donate Now";
    } else {
        $donation_url = "#";
        $donation_btn = "Campaign Ended";
    }
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
    $percentile       = ( $percentile > 100 ) ? $percentile       = 100 : $percentile;
    $supporters_total = $goal->get_num_supporters( $post_id );
    $supporters       = $goal->get_donators( $post_id );

    $donation_comments = new Donation_Comments();
    $comments          = $donation_comments->get_by_fundraiser_id( $post_id );


    //get fundraiser statistics
    $fundraiser_statistics = new Fundraiser_Statistics( $post_id );
    $participation_score   = $fundraiser_statistics->participation_score_formatted();
    $email_quality_score   = $fundraiser_statistics->email_quality_score_formatted();
    $participants_score    = $fundraiser_statistics->participant_score_formatted();

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

    get_header();
    $args             = array(
        'post_type'   => 'fundraiser',
        'post_status' => array( 'pending', 'publish', 'rejected' ),
        'p'           => $fundraiser_ID
    );
    $fundraiser_query = new WP_Query( $args );
    while ( $fundraiser_query->have_posts() ) :
        $fundraiser_query->the_post();
        ?>
        <script>
            var pecent = '<?php echo $percentile ?>';
            $(window).load(function () {
                comment_moreless(150, $(".comment_text"));
            })
        </script>

        <!--MAIN start-->
        <main>
            <!--LANDING PAGE BANNER start-->
            <div class="landing_page_banner dashboard_banner">
                <!--CONTAINER start-->
                <div class="container">
                    <?php
                    $status = get_post_status();

                    if ( $status == 'pending' ) {
                        header( 'Location: ' . get_bloginfo( 'url' ) . '/pending-approval/?fundraiser_id=' . $f_id );
                    }
                    $status = get_post_status();
                    if ( $status == 'rejected' ) {
                        ?>
                        <div class="maincontent noPadding">
                            <div class="section group">
                                <div class="col span_12_of_12 noMargin">
                                    <p class="warningMsg">This Fundraiser is rejected. <span style="float: right;">
                                            <strong></strong></span></p>
                                </div>
                            </div>
                        </div>
                        <?php
                    }

                    $status = get_post_status();
                    if ( $status == 'publish' ) {
                        ?>

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

                        <div class="row">

                            <div class=" col-md-5 col-sm-5 col-xs-12 col col_left">

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

                                <?php
                                if ( get_field( 'show_doller_amount', $post_id ) == 1 ) {
                                    ?>

                                    <div class="price_bar">
                                        <div class="goal">
                                            <h5><?php echo $currency; ?> <?php echo number_format( $public_goal ); ?>
                                                <b>Fundraiser
                                                    Goal</b></h5>
                                        </div>

                                        <?php if ( get_field( 'show_progressbar', $post_id ) == 1 ) { ?>
                                            <div id="progressBar" class="default">
                                                <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/layer22.png"
                                                     alt="">
                                                <div style="width:0px;"></div>
                                            </div>
                                            <script>
                                                //                                    $(document).ready(function () {

                                                $(window).load(function () {
                                                    if ( pecent != 0 )
                                                        progressBar(<?php echo $percentile; ?>, $('#progressBar'),<?php echo $public_goal ?>, <?php echo $fund_amount ?>);

                                                });
                                                $(window).resize(function () {
                                                    if ( pecent != 0 )
                                                        progressBar(<?php echo $percentile; ?>, $('#progressBar'),<?php echo $public_goal ?>, <?php echo $fund_amount ?>);

                                                });
                                            </script>
                                            <?php
                                        }
                                        ?>

                                        <div class="total_gain desk">
                                            <h5><span>$0</span><b>Total Raised</b></h5>
                                        </div>
                                    </div>

                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 mob_bar">
                                <div class="price_bar">
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
                                                    progressBar1(<?php echo $percentile; ?>, $('#progressBar1'),<?php echo $public_goal ?>, <?php echo $fund_amount ?>);
                                            });
                                        </script>
                                        <?php
                                    }
                                    ?>

                                    <div class="total_gain mob">
                                        <h5><span>$0</span><b>Total Raised</b></h5>
                                    </div>
                                </div>
                            </div>

                        </div>
                    <?php } ?>

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
                                    <th class="hide_mob" >facebook</th>
                                    <th class="hide_mob" >sms</th>
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
                                        <td class="hide_mob" ><?php echo $subgroup_stats->facebook; ?></td>
                                        <td class="hide_mob" ><?php echo $subgroup_stats->smsp; ?></td>
                                        <td><?php echo $subgroup_stats->supporters; ?></td>
                                        <td data-total="<?php echo $subgroup_stats->net_amount;?>">$<?php echo $subgroup_stats->net_amount; ?></td>
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
                        $f_id    = (int) $_GET['fundraiser_id'];
                        $meta_cp = json_decode( get_post_meta( $f_id, 'campaign_participations', true ) );

                        $fundraiser_participant_ids = get_fundraiser_participant_ids( $f_id );
                        if ( count( $fundraiser_participant_ids ) > 0 ) {
                            foreach ( $fundraiser_participant_ids as $fp_id ) {
                                $fp_ids[] = $fp_id[0];
                            }

                            $campaign_participations = ( $meta_cp && count( $fp_ids ) > 0 ) ? array_unique( array_merge_recursive( $meta_cp, $fp_ids ) ) : '';
                        } else {
                            $campaign_participations = '';
                        }
                        // More queries
                        $myrows1 = $wpdb->get_results( "SELECT * FROM participant_fundraiser_details WHERE fundraiser = " . $_GET['fundraiser_id'] . " AND (total >= " . _PARTICIPATION_GOAL . " OR email >=20) ORDER BY total DESC, email DESC, participant_name ASC", OBJECT );
                        $myrows2 = $wpdb->get_results( "SELECT * FROM participant_fundraiser_details WHERE fundraiser = " . $_GET['fundraiser_id'] . " AND (email >= 10 AND email < 20) AND total < " . _PARTICIPATION_GOAL . " ORDER BY total DESC, email DESC, participant_name ASC", OBJECT );
                        $myrows3 = $wpdb->get_results( "SELECT * FROM participant_fundraiser_details WHERE fundraiser = " . $_GET['fundraiser_id'] . " AND email <10 AND total < " . _PARTICIPATION_GOAL . " ORDER BY total DESC, email DESC, participant_name ASC", OBJECT );

                        if ( !empty( $campaign_participations ) ) {
                            ?>
                            <h3>Registered Participants (<?php echo count($campaign_participations);?>)</h3>

                            <div class="table_head">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Profile Picture</th>
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
                                    <tbody> <?php

                                        $dir = get_template_directory_uri();
                                        $no_profile_pic_uploaded = "<img style=\"max-width: 30px;\" src=\"$dir/assets/images/error.png\">" ;
                                        $profile_pic_uploaded = "<img style=\"max-width: 30px;\" src=\"$dir/assets/images/success.png\">";

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
                                                        <td><?php echo ( strpos ( get_avatar($participant->participant_id, 96) , 'user-avatar-96x96.png') )? $no_profile_pic_uploaded : $profile_pic_uploaded;  ?></td>
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
                                                            <td><?php echo ( strpos ( get_avatar($participant->participant_id, 96) , 'user-avatar-96x96.png') )? $no_profile_pic_uploaded : $profile_pic_uploaded;  ?></td>
                                                            <td><?php echo $participant->parents; ?></td>
                                                            <td><?php echo $participant->email; ?></td>
                                                            <td><?php echo $participant->facebook; ?></td>
                                                            <td>$<?php echo $participant->sms; ?></td>
                                                            <td><?php echo $participant->supporters; ?></td>
                                                            <td
                                                                data-total="<?php echo $participant->total; ?>">
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
                                                        <td><?php echo ( strpos ( get_avatar($participant->participant_id, 96) , 'user-avatar-96x96.png') )? $no_profile_pic_uploaded : $profile_pic_uploaded;  ?></td>
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
                            echo '<h3>No Participants Found</h3>';
                        }
                        ?>

                    </div>
                    <!--PARTICIPANTS TABLE end-->

                </div>
                <!--CONTAINER end-->

            </div>
            <!--LANDING PAGE BANNER end-->

            <!--LANDING PAGE MAIN CONTENT start-->
            <div class="landing_page_main_content">

                <div class="container">

                    <div class="row">

                        <div class="col-md-5 col-sm-5 col-xs-12 col eql col_right">
                            <div class="widgets individual_profile">
                                <?php
                                global $user_ID;
                                $permalink_copy = $permalink . 'c/' . $user_ID;
                                ?>
                                <span class="display_table">
                                    <a class="donate_link landing_link" href="<?php echo $permalink_copy; ?>"
                                       target="_blank">go to your landing page</a>
                                </span>

                                <div class="days_left">
                                    <?php echo $dayleft; ?>
                                </div>
                            </div>

                            <?php // get_sidebar ( 'fundraiser' );
                            ?>

                            <?php
                            if ( is_mobile_new() == false ) {
                                include ( get_template_directory() . "/comments-sidebar.php" );
                            }
                            ?>

                        </div>

                        <div class="col-md-7 col-sm-7 col-xs-12 col eql col_left">

                            <div class="widgets video_sec">

                                <!--- upload video url or Youtube link -->

                                <?php
                                $iframe = get_field( 'youtube_url' );
                                if ( !empty( $iframe ) ) {
                                    ?>
                                    <li>
                                        <div class="res_vid" style="padding-bottom: 20px;">
                                            <?php
                                            $success = preg_match( '/src="(.+?)"/', $iframe, $matches );
                                            if ( $success ) {
                                                $src     = $matches[1];
                                                $params  = array(
                                                    'controls' => 1,
                                                    'hd'       => 1,
                                                    'autohide' => 1,
                                                    'rel'      => 0
                                                );
                                                $new_src = add_query_arg( $params, $src );
                                                $iframe  = str_replace( $src, $new_src, $iframe );

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

                                <div class="shares">
                                    <h6><?php echo $fundraiser_total_share; ?> <b>shares</b></h6>
                                </div>
                            </div>

                            <div class="widgets about_fundraiser">

                                <h3>About This Fundraiser</h3>
                                <!--  This is Fundraiser messgae-->
                                <p><?php echo nl2br( get_post_meta( get_the_ID(), 'campaign_msg', true ) ); ?></p>

                            </div>
                            <?php
                            if ( is_mobile_new() ) {
                                include ( get_template_directory() . "/comments-sidebar.php" );
                            }
                            ?>
                            <div class="widgets make_donation">

                                <h3>MAke A Donation </h3>

                                <ul>
                                    <li>
                                        <div class="icon">
                                            <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon12.png"
                                                 alt="">
                                        </div>
                                        <div class="amount">
                                            <p>$1000 (USD)</p>
                                        </div>
                                        <div class="btn">
                                            <a href="<?php echo $donation_url . '&donation_amount=1000' ?>"><?php echo $donation_btn; ?></a>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="icon">
                                            <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon12.png"
                                                 alt="">
                                        </div>
                                        <div class="amount">
                                            <p>$500 (USD)</p>
                                        </div>
                                        <div class="btn">
                                            <a href="<?php echo $donation_url . '&donation_amount=500' ?>"><?php echo $donation_btn; ?></a>
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
                                            <a href="<?php echo $donation_url . '&donation_amount=250' ?>"><?php echo $donation_btn; ?></a>
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
                                            <a href="<?php echo $donation_url . '&donation_amount=150' ?>"><?php echo $donation_btn; ?></a>
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
                                            <a href="<?php echo $donation_url . '&donation_amount=100' ?>"><?php echo $donation_btn; ?></a>
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
                                            <a href="<?php echo $donation_url . '&donation_amount=50' ?>"><?php echo $donation_btn; ?></a>
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
                                                <a href="#"
                                                   onclick="custom_donation($(this), $('#custom_donate_amount'))"
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
        <!--MAIN end-->
        <?php
    endwhile;?>

    <?php get_footer();
} else {
    // Redirect the user to the fundraiser in case the dashboard link was shared with them
    if ($fundraiser_ID) {
        header( 'Location: ' . get_permalink($fundraiser_ID) );
        exit();
    } else {
        header( 'Location: ' . get_bloginfo( 'url' ) );
        exit();
    }
}