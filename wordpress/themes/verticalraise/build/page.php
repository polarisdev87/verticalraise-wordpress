<?php get_header(); ?>


    <main>

        <!--LANDING PAGE MAIN CONTENT start-->
        <div class="general-page custom-section pate-title">

            <div class="container">

                <div class="row">
                    <div class="col">
                        <div class="postContentDefault">
                            <?php while ( have_posts() ) : the_post(); ?>
                                <h1><?php the_title(); ?></h1>

                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="general-page custom-section pate-content">
            <div class="container">

                <div class="row">
                    <div class="col">
                        <div class="">
                            <?php while ( have_posts() ) : the_post(); ?>

                                <div><?php the_content(); ?></div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>


<?php get_footer(); ?>