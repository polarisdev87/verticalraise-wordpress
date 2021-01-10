<?php
/* Template Name: Fundraiser - Participant Landing Page */

/**
 * Dashboard: Participant Dashboard
 */
use \classes\app\fundraiser\Fundraiser_Ended;     // Fundraiser Ended Class Object
use \classes\models\tables\Donation_Comments;     // Comments
use \classes\app\fundraiser\Fundraiser_Media;      //Fundraiser Media Class Object
use \classes\models\tables\Subgroup_Users;
use \classes\app\download_report\Subgroup_Participant_Data;

/**
 * Set the fundraiser id.
 */
load_class( 'sharing.class.php' );
$sharing       = new Sharing();                   // Sharing class object
$fundraiser_id = $sharing->fundraiser_ID;

if ( !$fundraiser_id || empty( $fundraiser_id ) ) {
    header( 'Location: ' . get_bloginfo( 'url' ) . '/my-account' );
    exit();
}

/**
 * Load classes.
 */
load_class( 'payment_records.class.php' );
load_class( 'participants.class.php' );
load_class( 'goals.class.php' );
load_class( 'participant_records.class.php' );
/**
 * Instantiate classes.
 */
$goal                       = new Goals;
$payments                   = new Payment_Records();
$participants               = new Participants();
$donation_comments          = new Donation_Comments();
$subgroup_users_table       = new Subgroup_Users();
$subgroups_participant_data = new Subgroup_Participant_Data();
$subgroups_table_info       = $subgroups_participant_data->get_subgroup_aggregation_for( $fundraiser_id );

/**
 * Check the participations for the logged in user.
 */
if ( is_user_logged_in() ) {

    global $user_ID;

    // Is current user attached to this fundraiser?
    $campaign_participations = get_user_meta( $user_ID, 'campaign_participations', true );
    $participations_array    = json_decode( $campaign_participations );

    // Update $uid
    if ( !empty( $participations_array ) ) {
        if ( in_array( $fundraiser_id, $participations_array ) ) {
            $uid = $user_ID;
        } else {
            header( 'Location: ' . get_bloginfo( 'url' ) . '/my-account' );
            exit();
        }
    } else {
        header( 'Location: ' . get_bloginfo( 'url' ) . '/my-account' );
        exit();
    }


    $post_id  = (int) $fundraiser_id;
    $comments = $donation_comments->get_by_fundraiser_id( $post_id );

    // Fundraiser info
    $title = get_the_title( $post_id );

    $fundraise_mediaObj = new Fundraiser_Media();
    $image_url          = $fundraise_mediaObj->get_fundraiser_logo( $post_id );

    $status = get_post_status( $post_id );
    if ( $status != "publish" ) {
        header( 'Location: ' . get_site_url() . '/my-account' );
        exit();
    }
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
    $percentile       = ( $percentile > 100 ) ? $percentile       = 100 : $percentile;
    $supporters_total = $goal->get_num_supporters( $post_id );
    $supporters       = $goal->get_donators( $post_id );

// get Parcitipant info
    $user_info = get_userdata( $user_ID );


    // Base line values
    $p_supporters_total = $payments->get_number_supporters_by_user_id( $user_ID, $fundraiser_id );
    $p_supporters       = $payments->get_all_payments_by_fundraiser_uid( $user_ID, (int) $fundraiser_id );

    $p_amount = $payments->get_total_by_user_id( $user_ID, $fundraiser_id );


// URLs
    $base_url          = get_site_url();
    $fundraiser_string = '/donation/?fundraiser_id=' . $post_id;
    $permalink         = get_permalink( $post_id );

// Corporate Sponsors
    $corporate_sponsors = get_field( 'corporate_sponsors', $post_id );
// Total share counts

    $fundraiser_total_share   = $participants->total_shares_count_by_fid( $post_id );
    $participants_total_share = $participants->total_shares_count_by_uid( $post_id, $user_ID );

	$subgroup_name = $subgroup_users_table->getUserSubgroupName( $user_ID, $post_id );

//-----------------//-------------------//

    if ( isset( $_GET['print_inst'] ) && $_GET['print_inst'] == 'true' ) {
        load_class( 'print_instructions.class.php' );
        $instructions = new Print_Instructions( $fundraiser_id, $uid );
        $instructions->init();
        exit();
    }

    get_header();
    ?>

    <!--    <a class="custom_button fancybox_upload_pro_pic" id="fancybox_upload_pro_pic" style="display: none;"-->
    <!--       href="#upload_pro_pic">Add / replace Photo</a>-->
    <?php
    $currency_selection = get_post_meta( $fundraiser_id, 'currency_selection', true );

    if ( $currency_selection == 'CAD' ) {
        $currency = '$';
    } else {
        $currency = '$';
    }

    $args = array(
        'post_type'   => 'fundraiser',
        'post_status' => array( 'pending', 'publish', 'rejected' ),
        'p'           => $fundraiser_id
    );

    $fundraiser_query = new WP_Query( $args );

    while ( $fundraiser_query->have_posts() ) {
        $fundraiser_query->the_post();
        ?>
        <script>
            var pecent = '<?php echo $percentile ?>';
            $(document).ready(function () {
                comment_moreless(150, $(".comment_text"));
            })
        </script>


        <?php
        if ( isset( $_GET['invitepopup'] ) && $_GET['invitepopup'] == '1' ) {
            if ( !is_mobile_new() ) {
                ?>
                <script>
                    $(document).ready(function () {
                        setTimeout(function () {
                            $(".fancyboxInvite.participant_invite").not( ".no-event" ).trigger("click");
                        });
                    });
                </script>
            <?php } else { ?>
                <script>
                    $(document).ready(function () {
                        setTimeout(function () {
                            var url = $(".fancyboxInvite.participant_invite").not( ".no-event" ).attr("href");
                            if ( url ) {
                                window.location.href = $(".fancyboxInvite.participant_invite").not( ".no-event" ).attr("href");
                            }
                        });
                    });
                </script>
                <?php
            }
        }
        ?>


        <!--MAIN start-->
        <main>

            <!--LANDING PAGE BANNER start-->
            <div class="landing_page_banner particiants_dashboard_banner">

                <!--CONTAINER start-->
                <div class="container">
                    <div class="fundraiser_title_dash_top">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <h2><?php the_title(); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="row1 row">

                        <div class="col-md-8 col-sm-8 col-xs-12 col_left">

                            <div class="row">

                                <div class="col-md-4 col-sm-4 col-xs-12 particiant_name">
                                    <h3 class="hide_mob"><?php echo $user_info->first_name . " " . $user_info->last_name; ?></h3>
                                    <div class="wrap">
                                        <div class="user">
                                            <a href="#" id="avada_change" class="fancybox_upload_pro_pic">
                                                <?php
                                                if ( is_mobile_new() ) {
                                                    echo get_avatar( $user_ID, 130 );
                                                } else {
                                                    echo get_avatar( $user_ID, 150 );
                                                }
                                                ?>
                                                <b>Add/Change</b>
                                            </a>
                                        </div>
                                        <div class="mob_view_name">
                                            <h3 class="name"><?php echo $user_info->first_name . " " . $user_info->last_name; ?></h3>
                                            <h3 class="goal">Total Raised:
                                                <b>$<?php echo number_format( $p_amount ); ?>
                                                    <em>of $<?php echo _PARTICIPATION_GOAL ?></em>
                                                </b>
                                            </h3>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-8 col-sm-8 col-xs-12 participant_perfomance">
                                    <h3 class="hide_mob">Total Raised:
                                        <b>$<?php echo number_format( $p_amount ); ?>
                                            <em>of $<?php echo _PARTICIPATION_GOAL ?></em>
                                        </b>
                                    </h3>

                                    <?php
                                    $part_percentile = ( $p_amount / _PARTICIPATION_GOAL ) * 100;
                                    $part_percentile = ( $part_percentile <= 100 ) ? $part_percentile : 100;

                                    if ( get_field( 'show_progressbar', $post_id ) == 1 ) {
                                        ?>
                                        <div id="progressBar5" class="default">
                                            <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/layer22.png"
                                                 alt="">
                                            <div></div>
                                        </div>
                                        <script>
                                            jQuery(window).load(function () {
                                                if ( pecent != 0 )
                                                    progressBar(<?php echo $part_percentile; ?>, jQuery('#progressBar5'),<?php echo _PARTICIPATION_GOAL ?>, <?php echo $p_amount ?>);
                                            });
                                            jQuery(window).resize(function () {
                                                if ( pecent != 0 )
                                                    progressBar(<?php echo $part_percentile; ?>, jQuery('#progressBar5'),<?php echo _PARTICIPATION_GOAL ?>, <?php echo $p_amount ?>);
                                            });
                                        </script>
                                        <?php
                                    }
                                    ?>

                                    <div class="blank_row" ></div>
	                                <?php if ( ! empty( $subgroup_name ) ) { ?>
                                        <h3>Group: <b><?php echo $subgroup_name; ?></b></h3>
	                                <?php } ?>
                                    <?php $level = $participants->participant_level( $fundraiser_id, $user_ID ); ?>
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

                            </div>

                        </div>

                        <div class="col-md-4 col-sm-4 col-xs-12 col_right my_supporters">
                            <h3>my Supporters: <?php echo number_format( $p_supporters_total ); ?></h3>
                            <?php //if ( !is_mobile_new () ) {     ?>
                            <div class="list">
                                <ul>
                                    <?php
                                    if ( $p_supporters_total != 0 ) {

                                        foreach ( $p_supporters as $supporter ) {

                                            // Remove days ago per Paul
                                            /*
                                            $donate_date = explode( " ", $supporter['time'] )[0];
                                            $your_date   = strtotime( $donate_date, current_time( 'timestamp', 0 ) );
                                            $date_diff   = current_time( 'timestamp', 0 ) - $your_date;
                                            
                                            $days_ago    = round( $date_diff / ( 60 * 60 * 24 ) );

                                            switch ( $days_ago ) {
                                                case 0:
                                                    $days_ago = "Today";
                                                    break;
                                                case 1:
                                                    $days_ago = "Yesterday";
                                                    break;
                                                default:
                                                    $days_ago = $days_ago . " days ago";
                                                    break;
                                            }*/
                                            ?>
                                            <li>
                                                <b class="amt"><?php echo $currency; ?><?php echo $supporter['amount']; ?></b>
                                                <span> - </span>
                                                <span class="name">
                                                    <?php if ( $supporter['anonymous'] != 1 ) { ?>
                                                        <?php echo $supporter['name']; ?>
                                                    <?php } else { ?>
                                                        Anonymous
                                                    <?php } ?>
                                                </span>
                                                <?php if ( $supporter['comment'] ) { ?>
                                                    <p class="supporters_comments"><i>&ldquo;<?php echo $supporter['comment']; ?>&rdquo;</i></p>
                                                <?php } ?>
                                                <?php /*<p class="days"><?php echo $days_ago ?></p>*/ ?>
                                            </li>

                                            <?php
                                        }
                                    }
                                    ?>


                                </ul>
                            </div>
                            <?php //}     ?>
                        </div>

                    </div>
                    <?php
                    $table_show_check = get_post_meta( $fundraiser_id, 'showPc_table', true );
                    if ( $table_show_check ) {
                        ?>

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

                            $f_id = (int) $fundraiser_id;

                            // Get the participants from the Fundraiser ID
                            $campaign_participations = $participants->get_filtered_participant_ids_by_fid( $f_id );

                            $myrows1 = $wpdb->get_results( "SELECT * FROM participant_fundraiser_details WHERE fundraiser = '{$fundraiser_id}' AND (total >= " . _PARTICIPATION_GOAL . " OR email >=20) ORDER BY total DESC, email DESC, participant_name ASC", OBJECT );

                            $myrows2 = $wpdb->get_results( "SELECT * FROM participant_fundraiser_details WHERE fundraiser = '{$fundraiser_id}' AND (email >= 10 AND email < 20) AND total < " . _PARTICIPATION_GOAL . " ORDER BY total DESC, email DESC, participant_name ASC", OBJECT );

                            $myrows3 = $wpdb->get_results( "SELECT * FROM participant_fundraiser_details WHERE fundraiser = '{$fundraiser_id}' AND email <10 AND total < " . _PARTICIPATION_GOAL . "
ORDER BY total DESC, email DESC, participant_name ASC", OBJECT );

                            if ( !empty( $campaign_participations ) ) {
                                ?>
                                <h3>Registered Participants</h3>

                                <div class="table_head">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
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
                                                            <td class="user_id-<?php echo $participant->participant_id; ?>"><?php echo $participant->participant_name; ?></td>
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
                                                            <td class="user_id-<?php echo $participant->participant_id; ?>"><?php echo $participant->participant_name; ?></td>
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
                                                            <td class="user_id-<?php echo $participant->participant_id; ?>"><?php echo $participant->participant_name; ?></td>
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
                                echo '<h2>No Participants Found</h2>';
                            }
                            ?>

                        </div>
                        <!--PARTICIPANTS TABLE end-->
                    <?php } ?>
                </div>
                <!--CONTAINER end-->

            </div>
            <!--LANDING PAGE BANNER end-->

            <!--LANDING PAGE MAIN CONTENT start-->
            <div class="landing_page_main_content dashboard_page">

                <div class="container">

                    <div class="row">

                        <div class="col-md-5 col-sm-5 col-xs-12 col eql col_right">

                            <div class="widgets individual_profile">
                                <div class="user_name">
                                    <div class="fundraiser_logo">
                                        <img src="<?php
                    echo ( $image_url != null ) ?
                            $image_url : bloginfo( 'template_directory' ) . '/assets/images/default-logo.png';
                    ?>"
                                             class="logo-img"/>
                                    </div>
                                    <h4><?php echo $title; ?></h4>
                                </div>
                                <?php
                                if ( get_field( 'show_doller_amount', $post_id ) == 1 ) {
                                    ?>

                                    <div class="total_goal">
                                        <?php if ( get_field( 'show_progressbar', $post_id ) == 1 ) { ?>
                                            <div id="progressBar3" class="big-green">
                                                <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/layer33.png"
                                                     alt="">
                                                <div></div>
                                            </div>
                                            <script>
                                                $(window).load(function () {
                                                    if ( pecent != 0 )
                                                        progressBar(<?php echo $percentile; ?>, $('#progressBar3'),<?php echo $public_goal ?>, <?php echo $fund_amount ?>);
                                                });
                                                $(window).resize(function () {
                                                    if ( pecent != 0 )
                                                        progressBar(<?php echo $percentile; ?>, $('#progressBar3'),<?php echo $public_goal ?>, <?php echo $fund_amount ?>);
                                                });
                                            </script>
                                            <?php
                                        }
                                        ?>
                                        <h5>
                                            <b>$<?php echo number_format( $fund_amount ); ?></b> of
                                            $<?php echo number_format( $public_goal ); ?> goal</h5>
                                    </div>
                                    <?php
                                }
                                ?>
                                <div class="days_left">
                                    <?php echo $dayleft; ?>
                                </div>
                            </div>
                            <?php
                            if ( is_mobile_new() == false ) {
                                include ( get_template_directory() . "/comments-sidebar.php" );
                            }
                            ?>

                        </div>

                        <div class="col-md-7 col-sm-7 col-xs-12 col eql col_left">

                            <div class="widgets video_sec">

                                <?php
                                $iframe = get_field( 'youtube_url' );
                                if ( !empty( $iframe ) ) {
                                    ?>
                                    <li>
                                        <div class="res_vid" style="padding-bottom: 20px">
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

                                <!--- upload video url or Youtube link -->
                                <div class="shares">
                                    <h6><?php echo $fundraiser_total_share; ?>
                                        <b>shares</b>
                                    </h6>
                                </div>
                            </div>

                            <div class="widgets about_fundraiser">

                                <h3>About This Fundraiser</h3>

                                <p><?php echo nl2br( get_post_meta( get_the_ID(), 'campaign_msg', true ) ); ?></p>

                            </div>
                            <?php
                            if ( is_mobile_new() ) {
                                include ( get_template_directory() . "/comments-sidebar.php" );
                            }
                            ?>
                            <div class="widgets make_donation">

                                <h3>Make A Donation </h3>

                                <?php
                                // Media
                                if ( isset( $wp_query->query_vars['media'] ) ) {
                                    $media = urldecode( $wp_query->query_vars['media'] );
                                } else {
                                    $media = 'c';
                                }

                                // Uid
                                if ( isset( $wp_query->query_vars['uid'] ) ) {
                                    $uid = urldecode( $wp_query->query_vars['uid'] );
                                } else {
                                    if ( is_user_logged_in() ) {
                                        $uid = $user_ID;
                                    }
                                }

                                // If the fundraiser has ended
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
                                ?>
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
                                            <span class="display_table"><a
                                                    href="<?php echo $donation_url . '&donation_amount=1000' ?>"><?php echo $donation_btn; ?></a></span>
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
        <!--MAIN end-->
        <script>
        $(document).ready(function () {
            $(".fancyboxInvite").not( ".no-event" ).click();
        });
        </script> 
        <?php
    }
    get_footer();
} else {
    // Redirect the user to the fundraiser in case the dashboard link was shared with them
    if ($fundraiser_id) {
        header( 'Location: ' . get_permalink($fundraiser_id) );
        exit();
    } else {
        header( 'Location: ' . get_bloginfo( 'url' ) );
        exit();
    }
}