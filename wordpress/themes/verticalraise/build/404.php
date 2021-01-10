<?php /* Template Name: 404 */ ?>
<?php get_header( ); ?>
<main>

        <!--THANK YOU PAGE start-->
        <div class="error-404">

            <div class="container">

                <div class="modal-header">
                    <h3> 404 error
                    </h3>
                    <p>The page you were looking cannot be found.</p>
                </div>
                <div class="modal-body">
                    <div class="thankyou fundraiser_img">
                      
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="<?php echo get_bloginfo('url'); ?>" class="go_back" data-dismiss="modal">
                        Go to the homepage
                    </a>
                </div>
               
            </div>
        </div>
    </main>
<?php get_footer(); ?>