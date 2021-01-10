<div class="acc_nav_wrap">
<div class="acc_nav">
    <?php wp_nav_menu(array('theme_location' => 'myacc')); ?>
    <h3>Your Fundraisers</h3>
    <?php
        global $user_ID;        
        $campaign_participations = json_decode(get_user_meta($user_ID, 'campaign_participations', true));
        $campaign_sadmin = json_decode(get_user_meta($user_ID, 'campaign_sadmin', true));
        if(!empty($campaign_participations) || !empty($campaign_sadmin)) {
            $fundraiser_query1 = new stdClass();
            $fundraiser_query2 = new stdClass();
            $fundraiser_query3 = new stdClass();
            $fundraiser_query1->posts = array();
            $fundraiser_query2->posts = array();
            $fundraiser_query3->posts = array();

            $args1 = array(
                'post_type' => 'fundraiser',
                'post_status' => array('pending', 'publish'),
                'posts_per_page' => -1,
                'author' => $user_ID
            );
            $fundraiser_query1 = new WP_Query($args1);
            if (!empty($campaign_participations)) {
                $args2 = array(
                    'post_type' => 'fundraiser',
                    'post_status' => array('publish', 'pending'),
                    'posts_per_page' => -1,
                    'post__in' => $campaign_participations
                );
                $fundraiser_query2 = new WP_Query($args2);
            }
            if (!empty($campaign_sadmin)) {
                $args3 = array(
                    'post_type' => 'fundraiser',
                    'post_status' => array('publish', 'pending'),
                    'posts_per_page' => -1,
                    'post__in' => $campaign_sadmin
                );
                $fundraiser_query3 = new WP_Query($args3);
            }

            $fundraiser_query = new WP_Query();

            if (empty($campaign_participations)) {
                $fundraiser_query->posts = array_merge($fundraiser_query1->posts, $fundraiser_query3->posts);
            } elseif (empty($campaign_sadmin)) {
                $fundraiser_query->posts = array_merge($fundraiser_query1->posts, $fundraiser_query2->posts);
            } elseif (!empty($campaign_participations) && !empty($campaign_sadmin)) {
                $fundraiser_query->posts = array_merge($fundraiser_query1->posts, $fundraiser_query2->posts, $fundraiser_query3->posts);
            } else {
                $fundraiser_query->posts = array_merge($fundraiser_query1->posts);
            }

            $fundraiser_query->post_count = count($fundraiser_query->posts);
        } else {
            $args = array(
                'post_type' => 'fundraiser',
                'post_status' => array('pending', 'publish'),
                'posts_per_page' => -1,
                'author' => $user_ID
            );
            $fundraiser_query = new WP_Query($args);
        }                        
                        
        if ( $fundraiser_query->have_posts() ) :
        $old = array();
        $current = array();
        $upcoming = array();
        while ( $fundraiser_query->have_posts() ) : $fundraiser_query->the_post();
            $format_in = 'Ymd';
            $start_date = get_post_meta(get_the_ID(), 'start_date', true);
            $start_date = DateTime::createFromFormat($format_in, $start_date);
            $end_date = get_post_meta(get_the_ID(), 'end_date', true);
            $end_date = DateTime::createFromFormat($format_in, $end_date);
            $current_date = new DateTime();
            if($current_date >= $end_date) {
                array_push($old, get_the_ID());
            }
            if($current_date >= $start_date && $current_date < $end_date) {
                array_push($current, get_the_ID());
            }
            if($current_date < $start_date) {
                array_push($upcoming, get_the_ID());
            }
        endwhile;
    ?>
    <?php $campaign_participations = array(); $campaign_participations = json_decode(get_user_meta($user_ID, 'campaign_sadmin', true)); ?>
        <ul>
            <li><a href="javascript: void(0);"><i class="fa fa-fast-forward"></i>Upcoming</a>
                <?php if(!empty($upcoming)) { ?>
                    <ul>
                        <?php foreach($upcoming as $u) { ?>
                            <?php $author_id = get_post_field ('post_author', $u); ?>
                            <?php
                                $flag_u = 0;
                                if(!empty($campaign_participations)) {
                                    if(in_array($u, $campaign_participations)) {
                                        $flag_u = 1;
                                    }
                                }
                             ?>
                             <?php if($author_id == $user_ID || $flag_u == 1) { ?>
                                <li><a href="<?php bloginfo('url'); ?>/single-fundraiser/?fundraiser_id=<?php echo $u; ?>"><?php echo get_the_title($u); ?></a></li>
                            <?php } else { ?>
                                <li><a href="<?php bloginfo('url'); ?>/participant-fundraiser/?fundraiser_id=<?php echo $u; ?>"><?php echo get_the_title($u); ?></a></li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>
            <li><a href="javascript: void(0);"><i class="fa fa-check-circle"></i>Current</a>
                <?php if(!empty($current)) { ?>
                    <ul>
                        <?php foreach($current as $c) { ?>
                             <?php $author_id = get_post_field ('post_author', $c); ?>
                             <?php
                                $flag_c = 0;
                                if(!empty($campaign_participations)) {
                                    if(in_array($c, $campaign_participations)) {
                                        $flag_c = 1;
                                    }
                                }
                             ?>
                             <?php if($author_id == $user_ID || $flag_c == 1) { ?>
                                <li><a href="<?php bloginfo('url'); ?>/single-fundraiser/?fundraiser_id=<?php echo $c; ?>"><?php echo get_the_title($c); ?></a></li>
                            <?php } else { ?>
                                <li><a href="<?php bloginfo('url'); ?>/participant-fundraiser/?fundraiser_id=<?php echo $c; ?>"><?php echo get_the_title($c); ?></a></li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>
            <li><a href="javascript: void(0);"><i class="fa fa-fast-backward"></i>Past</a>
                <?php if(!empty($old)) { ?>
                    <ul>
                        <?php foreach($old as $o) { ?>
                             <?php $author_id = get_post_field ('post_author', $o); ?>
                             <?php
                                $flag_o = 0;
                                if(!empty($campaign_participations)) {
                                    if(in_array($o, $campaign_participations)) {
                                        $flag_o = 1;
                                    }
                                }
                             ?>
                             <?php if($author_id == $user_ID || $flag_o == 1) { ?>
                                <li><a href="<?php bloginfo('url'); ?>/single-fundraiser/?fundraiser_id=<?php echo $o; ?>"><?php echo get_the_title($o); ?></a></li>
                            <?php } else { ?>
                                <li><a href="<?php bloginfo('url'); ?>/participant-fundraiser/?fundraiser_id=<?php echo $o; ?>"><?php echo get_the_title($o); ?></a></li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>
        </ul>
    <?php
        endif;
        wp_reset_postdata();
    ?>
</div>
</div>
<script>
    jQuery(document).ready(function() {
        //jQuery('.acc_nav ul li ul').show();
        jQuery('.matchheight').matchHeight();
        //jQuery('.acc_nav ul li ul').hide();
    });
</script>