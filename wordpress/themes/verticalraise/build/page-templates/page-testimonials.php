<?php /* Template Name: Testimonial */ ?>
<?php get_header(); ?>
    <div class="testimonial_page">
        <div class="maincontent">
            <div class="section group">
                <div class="col span_12_of_12">
                    <?php while (have_posts()) : the_post();
                        $title = get_the_title();
                        echo '<h1>'.$title.'</h1>';
                    endwhile; ?>
                    <?php
                    $args = array(
                        'post_type' => 'testimonial',
                        'posts_per_page' => -1,
                        'post_status' => 'publish'
                    );
                    $the_query = new WP_Query( $args );
                    echo '<div class="testimonial_wrapper">';
                    echo '<div class="testimonial_slider">';
                    if ( $the_query->have_posts() ) :
                        while ( $the_query->have_posts() ) : $the_query->the_post();
                    ?>
                        <div class="testimonial_single">
                            <div class="testimonial_quote_bg">
                                <img src="<?php bloginfo('template_directory'); ?>/assets/images/testi_quote.png">
                                <div class="testimonial_quote">
                                    <div class="quote_content">
                                        <?php the_content(); ?>
                                        <!--<p><?php /*echo $trimmed = wp_trim_words( get_the_excerpt(), $num_words = 20, $more = ' ...' ); */?></p>-->
                                    </div>
                                    <div class="quote_title">
                                        <p>--<?php the_title(); ?></p>
                                    </div>
                                </div>
                                <div class="testimonial_logo">
                                    <?php if(has_post_thumbnail()) { ?>
                                        <?php the_post_thumbnail('testimonial-logo-thumb'); ?>
                                    <?php } else { ?>
                                        <img src="<?php bloginfo('template_directory'); ?>/assets/images/testi_logo_bg.png">
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php
                        endwhile;
                    endif;
                    echo '</div>';
                    echo '</div>';
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div id="content" style="background: #ffffff;">
        <div class="maincontent" style="width: 80%;">
            <div class="section group">
                <div class="col span_12_of_12">
                    <script type="text/javascript"> var sa_review_count = 20; var sa_date_format = 'F j, Y'; function saLoadScript(src) { var js = window.document.createElement("script"); js.src = src; js.type = "text/javascript"; document.getElementsByTagName("head")[0].appendChild(js); } saLoadScript('//www.shopperapproved.com/merchant/22101.js'); </script><div id="review_header"></div><div id="merchant_page"></div><div id="review_image"><a href="http://www.shopperapproved.com/reviews/wefund4u.com/" target="_blank" rel="nofollow"></a></div>
                </div>
            </div>
        </div>
    </div>
<?php get_footer(); ?>