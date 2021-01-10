<?php
/* Template Name: My Account */

/**
 * Load classes.
 */
use classes\app\cookie\Check_Cookie;

load_class( 'secondary_admins.class.php' );

load_class( 'goals.class.php' );
$goal = new Goals;
/**
 * Instantiate classes.
 */
$sadmins = new Secondary_Admins();

if ( is_user_logged_in() ) {

    get_header();

    global $user_ID;
    $user_info   = get_userdata( $user_ID );
    $loginStatus = get_user_meta( $user_ID, 'first_login', true );
    if ( $loginStatus == 1 ) {
        update_user_meta( $user_ID, 'first_login', 0 );
    }
    ?>
    <!--MAIN start-->
    <main>
        <!--MEMBER FORM SECTION start-->
        <div class="member_form_sec">
            <!-- -->
            <div class="">
                <div class="member_name">
                    <?php
                    // Look up fundraisers by 'join_code'
                    ?>
                    <!--  -->
                    <div class="container">
                        <div class="row">
                            <div class="col col_left user">
                                <a href="#" id="avada_change" class="fancybox_upload_pro_pic">

                                    <?php
                                    if ( is_mobile_new() ) {
                                        echo get_avatar( $user_ID, 72 );
                                    } else {
                                        echo get_avatar( $user_ID, 150 );
                                    }
                                    ?>
                                    <b>Add/Change</b>
                                </a>                      
                            </div>
                            <div class="col col_right">
                                <h3>
                                    <b><?php echo $user_info->first_name; ?> <?php echo $user_info->last_name; ?><a
                                            href="<?php bloginfo( 'url' ); ?>/edit-profile"></a>
                                    </b>
                                </h3>
                                <h5><a href="#"><?php echo $user_info->user_email; ?></a></h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fundraiser_form">

                    <div class="container">
                        <div class="row">
                            <div class="col col_right">

                                <?php
                                global $user_ID;

                                $campaign_participations = json_decode( get_user_meta( $user_ID, 'campaign_participations', true ) );
                                $campaign_sadmin         = json_decode( get_user_meta( $user_ID, 'campaign_sadmin', true ) );

                                if ( !empty( $campaign_participations ) || !empty( $campaign_sadmin ) ) {

                                    $fundraiser_query1 = new stdClass();
                                    $fundraiser_query2 = new stdClass();
                                    $fundraiser_query3 = new stdClass();
                                    $fundraiser_query1->posts = array();
                                    $fundraiser_query2->posts = array();
                                    $fundraiser_query3->posts = array();

                                    $args1 = array(
                                        'post_type'      => 'fundraiser',
                                        'post_status'    => array( 'pending', 'publish' ),
                                        'posts_per_page' => -1,
                                        'author'         => $user_ID,
                                        'meta_key'       => 'start_date',
                                        'orderby'        => 'meta_value_num',
                                        'order'          => 'ASC'
                                    );
                                    $fundraiser_query1 = new WP_Query( $args1 );
                                    if ( !empty( $campaign_participations ) ) {
                                        $args2 = array(
                                            'post_type'      => 'fundraiser',
                                            'post_status'    => array( 'publish', 'pending' ),
                                            'posts_per_page' => -1,
                                            'post__in'       => $campaign_participations,
                                            'meta_key'       => 'start_date',
                                            'orderby'        => 'meta_value_num',
                                            'order'          => 'ASC'
                                        );
                                        $fundraiser_query2 = new WP_Query( $args2 );
                                    }
                                    if ( !empty( $campaign_sadmin ) ) {
                                        $args3 = array(
                                            'post_type'      => 'fundraiser',
                                            'post_status'    => array( 'publish', 'pending' ),
                                            'posts_per_page' => -1,
                                            'post__in'       => $campaign_sadmin,
                                            'meta_key'       => 'start_date',
                                            'orderby'        => 'meta_value_num',
                                            'order'          => 'ASC'
                                        );
                                        $fundraiser_query3 = new WP_Query( $args3 );
                                    }

                                    $fundraiser_query = new WP_Query();

                                    if ( empty( $campaign_participations ) ) {
                                        $fundraiser_query->posts = array_merge( $fundraiser_query1->posts, $fundraiser_query3->posts );
                                    } elseif ( empty( $campaign_sadmin ) ) {
                                        $fundraiser_query->posts = array_merge( $fundraiser_query1->posts, $fundraiser_query2->posts );
                                    } elseif ( !empty( $campaign_participations ) && !empty( $campaign_sadmin ) ) {
                                        $fundraiser_query->posts = array_merge( $fundraiser_query1->posts, $fundraiser_query2->posts, $fundraiser_query3->posts );
                                    } else {
                                        $fundraiser_query->posts = array_merge( $fundraiser_query1->posts );
                                    }

                                    $fundraiser_query->post_count = count( $fundraiser_query->posts );
                                } else {
                                    $args = array(
                                        'post_type'      => 'fundraiser',
                                        'post_status'    => array( 'pending', 'publish' ),
                                        'posts_per_page' => -1,
                                        'author'         => $user_ID,
                                        'meta_key'       => 'start_date',
                                        'orderby'        => 'meta_value_num',
                                        'order'          => 'ASC'
                                    );
                                    $fundraiser_query = new WP_Query( $args );
                                }

                                if ( $fundraiser_query->have_posts() ) :

                                    // Our 3 fundraiser types
                                    $old = array();
                                    $current = array();
                                    $upcoming = array();

                                    while ( $fundraiser_query->have_posts() ) : $fundraiser_query->the_post();

                                        // `Start` & `End` dates
                                        $format_in    = 'Ymd';
                                        $start_date   = strtotime( get_post_meta( get_the_ID(), 'start_date', true ), current_time( 'timestamp', 0 ) );
                                        $end_date     = strtotime( get_post_meta( get_the_ID(), 'end_date', true ), current_time( 'timestamp', 0 ) );
                                        $current_date = current_time( 'timestamp', 0 );

                                        if ( $current_date >= $end_date ) {
                                            $old[] = array( "f_id" => get_the_ID(), "end_date" => $end_date );
                                        }
                                        if ( ( $current_date >= $start_date ) && ( $current_date < $end_date ) ) {
                                            $current[] = array( "f_id" => get_the_ID(), "end_date" => $end_date );
                                        }
                                        if ( $current_date < $start_date ) {
                                            $upcoming[] = array( "f_id" => get_the_ID(), "end_date" => $end_date, "start_date" => $start_date  );
                                        }
                                    endwhile;
                                endif;

                                if ( !empty( $old ) ) {
                                    usort($old, function($a, $b) {
                                        return $a['end_date'] - $b['end_date'];
                                    });
                                }
                                if ( !empty( $current ) ) {
                                    usort($current, function($a, $b) {
                                        return $a['end_date'] - $b['end_date'];
                                    });
                                }
                                if ( !empty( $upcoming ) ) {
                                    usort($upcoming, function($a, $b) {
                                        return $a['start_date'] - $b['start_date'];
                                    });
                                }

                                $campaign_participations = array();
                                $campaign_participations = json_decode( get_user_meta( $user_ID, 'campaign_sadmin', true ) );
                                ?>
                                <div class="fundraiser_type">
                                    <h4>Current Fundraiser(s)</h4>
                                    <?php
//                                        print_r($current);
                                    if ( !empty( $current ) ) {
                                        ?>
                                        <ul>
                                            <?php
                                            foreach ( $current as $c ) {

                                                $author_id = get_post_field( 'post_author', $c["f_id"] );

                                                $flag_c = 0;
                                                if ( !empty( $campaign_participations ) ) {
                                                    if ( in_array( $c["f_id"], $campaign_participations ) ) {
                                                        $flag_c = 1;
                                                    }
                                                }

                                                $title       = get_the_title( $c["f_id"] );

                                                $end_date    = date( "n/j/y", $c["end_date"] );
                                                $goal_amount = $goal->get_goal( $c["f_id"] );
                                                $fund_amount = $goal->get_amount( $c["f_id"] );

                                                if ( $author_id == $user_ID || $flag_c == 1 ) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php bloginfo( 'url' ); ?>/single-fundraiser/?fundraiser_id=<?php echo $c["f_id"]; ?>">
                                                            <p><?php echo "{$title} (ending {$end_date})"; ?></p>
                                                            <h6>$<?php echo number_format( $fund_amount ); ?>
                                                                of $<?php echo number_format( $goal_amount ); ?></h6>
                                                        </a>

                                                    </li>
                                                <?php } else { ?>
                                                    <li>
                                                        <a href="<?php bloginfo( 'url' ); ?>/participant-fundraiser/?fundraiser_id=<?php echo $c["f_id"]; ?>">
                                                            <p><?php echo "{$title} (ending {$end_date})"; ?></p>
                                                            <h6>$<?php echo number_format( $fund_amount ); ?>
                                                                of $<?php echo number_format( $goal_amount ); ?></h6>
                                                        </a>
                                                    </li>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </ul>
                                    <?php } ?>
                                </div>
                                <div class="fundraiser_type">
                                    <h4>Upcoming Fundraiser(s)</h4>
                                    <?php if ( !empty( $upcoming ) ) { ?>
                                        <ul>
                                            <?php

                                            foreach ( $upcoming as $u ) {
                                                $author_id = get_post_field( 'post_author', $u["f_id"] );

                                                $flag_u = 0;
                                                if ( !empty( $campaign_participations ) ) {
                                                    if ( in_array( $u["f_id"], $campaign_participations ) ) {
                                                        $flag_u = 1;
                                                    }
                                                }

                                                $title       = get_the_title( $u["f_id"] );
                                                $start_date    = date( "n/j/y", $u["start_date"] );
                                                $goal_amount = $goal->get_goal( $u["f_id"] );
                                                $fund_amount = $goal->get_amount( $u["f_id"] );
                                                ?>
                                                <?php if ( $author_id == $user_ID || $flag_u == 1 ) { ?>
                                                    <li>
                                                        <a href="<?php bloginfo( 'url' ); ?>/single-fundraiser/?fundraiser_id=<?php echo $u["f_id"]; ?>">
                                                            <p><?php echo "{$title} (starts {$start_date})"; ?></p>
                                                            <h6>$<?php echo number_format( $fund_amount ); ?>
                                                                of $<?php echo number_format( $goal_amount ); ?></h6>
                                                        </a>
                                                    </li>
                                                <?php } else { ?>
                                                    <li>
                                                        <a href="<?php bloginfo( 'url' ); ?>/participant-fundraiser/?fundraiser_id=<?php echo $u["f_id"]; ?>">
                                                            <p><?php echo "{$title} (starts {$start_date})" ?></p>
                                                            <h6>$<?php echo number_format( $fund_amount ); ?>
                                                                of $<?php echo number_format( $goal_amount ); ?></h6>
                                                        </a>
                                                    </li>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </ul>
                                    <?php } ?>
                                </div>
                                <div class="fundraiser_type">
                                    <h4>
                                        <a href="#" class="past_fund_pop" >
                                            Past Fundraiser(s)
                                        </a>                                        
                                    </h4>
                                </div>
                            </div>
                            <div class="col_left col">
                                <form id="joinForm" action="" method="POST">
                                    <h3>Join a fundraiser</h3>
                                    <input type="tel" name="join_code" maxlength="8" id="joinCode" placeholder="Enter Join Code Here"
                                           class="ip" required="" />
                                    <input type='hidden' name='submit_joincode' />
                                    <input type="button" value="Join now" name="join" class="submit_btn" />
                                </form>
                                <span class="display_table">
                                    <a href="#" class="create_link create_fundraiser">create a
                                        fundraiser</a>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>

                <!--PAST FUNDRAISER POPUP start-->
                <div class="modal fade past_fund_model" id="past_fund_model">
                    <div class="modal-dialog">

                        <div class="modal-content">
                            <!-- header -->
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>

                            <!-- body -->
                            <div class="modal-body">
                                <div class="container">
                                    <div class="modal-header model_title">
                                        <h3 class="modal-title">Past Fundraiser(s)</h3>
                                    </div>
                                    <div class="search-past">
                                        <i class="fa fa-search"></i>
                                        <input type="text" class="ip valid past_fund_search" name="search_key" />
                                    </div>
                                    <div class="row">
                                        <?php
                                        if ( !empty( $old ) ) {
                                            usort( $old, function($a, $b) {
                                                return $a['end_date'] < $b['end_date'];
                                            } );
                                            foreach ( $old as $o ) {

                                                $author_id = get_post_field( 'post_author', $o["f_id"] );

                                                $title     = get_the_title( $o["f_id"] );
                                                $end_date  = date( "n/j/y", $o["end_date"] );

                                                $flag_o = 0;
                                                if ( !empty( $campaign_participations ) ) {
                                                    if ( in_array( $o["f_id"], $campaign_participations ) ) {
                                                        $flag_o = 1;
                                                    }
                                                }
                                                $goal_amount = $goal->get_goal( $o["f_id"] );
                                                $fund_amount = $goal->get_amount( $o["f_id"] );
                                                if ( $author_id == $user_ID || $flag_o == 1 ) {
                                                    ?>
                                                    <div class="col-md-6 col-sm-6 col-xs-12 col past-fundraiser-list">
                                                        <a href="<?php bloginfo( 'url' ); ?>/single-fundraiser/?fundraiser_id=<?php echo $o["f_id"]; ?>">
                                                            <p><?php echo "{$title} (ended {$end_date})"; ?></p>
                                                        </a>
                                                        <h6>$<?php echo number_format( $fund_amount ); ?>
                                                            of $<?php echo number_format( $goal_amount ); ?></h6>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="col-md-6 col-sm-6 col-xs-12 col past-fundraiser-list">
                                                        <a href="<?php bloginfo( 'url' ); ?>/participant-fundraiser/?fundraiser_id=<?php echo $o["f_id"]; ?>">
                                                            <p><?php echo "{$title} (ended {$end_date})"; ?></p>
                                                        </a>
                                                        <h6>$<?php echo number_format( $fund_amount ); ?>
                                                            of $<?php echo number_format( $goal_amount ); ?></h6>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--PAST FUNDRAISER POPUP end-->
                </div>

                <!--joining popup to wait submit-->
                <div class="modal fade joining_load_modal" data-backdrop="static" id="joining_loading" style="display: none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <!-- body -->
                            <div class="modal-body">
                                <div class="container">
                                    <div class="loader" >
                                        <div class="loader_icon" >
                                            <img class="loading-image"
                                                 src="<?php bloginfo( 'template_directory' ); ?>/assets/images/ajax-loader.gif"
                                                 alt="loading..">
                                        </div>
                                        <h5>Joining you to the fundraiser. Please be patient.</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                   
                </div>

            </div>
        </div>
    </main>

    <script>
        (function ($) {
    // Expressions
        })(jQuery);
        $(document).ready(function () {
            $("#joinForm").validate({
                rules: {
                    join_code: "required",
                },
                messages: {
                    join_code: "Please enter Valid Join code."
                }
            });

            $("#joinForm input[type=button]").on('click', function (e) {
                e.preventDefault();
                if ( $("#joinForm").valid() == false ) {
                    $("#joinForm").valid();
                    return false;
                } else {
                    var btn = this;
                    $(btn).button('loading');
                    $("#joining_loading").modal('show');
                    setTimeout(function () {
                        //get ajax request to register join code
                        //ajax request
                        $.post(
                                LoginAjaxUrl + "/ajax-joincode",
                                $("#joinForm").serializeArray(),
                                function (result) {
                                    $(btn).button('reset');
                                    $("#joining_loading").modal('hide');
                                    $(" p.warningMsg,  p.errorMsg").remove()
                                    var json = result;

                                    if ( json.success ) {
                                        location.href = json.data
                                    } else {
                                        $(json.data).insertBefore(".member_name")
                                    }
                                },
                                'json'
                                );
                    }, 1000)
                }
            });
            $(".past_fund_pop").on("click", function (e) {
                e.preventDefault();
                $("#past_fund_model input.past_fund_search").val('');
                $("#past_fund_model").modal('show');
            });
        });

        $(window).load(function () {
            $("#past_fund_model input.past_fund_search").keyup(function () {
                var keyword = $(this).val()

                $("#past_fund_model .past-fundraiser-list").each(function () {
                    if ( keyword == '' ) {
                        $(this).removeClass('deactive_search').addClass('active_search');
                    } else {
                        $(this).removeClass('active_search').addClass('deactive_search');
                        var title = $(this).find("a").text();
                        if ( title.toLowerCase().includes(keyword.toLowerCase()) ) {
                            $(this).removeClass('deactive_search').addClass('active_search');
                        }
                    }
                })
            })
        })
    </script>


    <?php if ( isset( $_GET['popup'] ) && $_GET['popup'] == 1 ) { ?>
        <script type="text/javascript">
            $(document).ready(function () {
                $('#upload_pro_pic').modal('show');
            });
        </script>
    <?php } else { ?>
                                         
    <?php } ?>
    <!--MAIN end-->
    <?php
    $checkcookie = new Check_Cookie();
    $checkcookie->display();

    get_footer();
} else {
    header( 'Location: ' . get_bloginfo( 'url' ) );
    exit();
}