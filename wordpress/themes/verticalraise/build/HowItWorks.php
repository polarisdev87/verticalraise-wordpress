<?php /* Template Name: How It Works */ ?>
<?php get_header(); ?>
<?php while (have_posts()) : the_post(); ?>
    <div id="how_vid">
        <div class="maincontent" style="width: 70%;">
            <div class="section group">
                <div class="col span_12_of_12">
                    <h1><?php the_title(); ?></h1>
                    <div class="res_vid">
                        <?php the_field('video'); ?>
                        <div class="res_vid_frame"><img src="<?php bloginfo('template_directory'); ?>/assets/images/ipad_frame.png"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="content_how">
        <div class="maincontent noPadding">
            <div class="section group">
                <div class="col span_12_of_12 noMargin">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
    </div>
<?php endwhile; ?>
<?php get_footer(); ?>