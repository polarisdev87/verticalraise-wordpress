<?php /* Template Name: Contact */ ?>
<?php get_header(); ?>

    <!--MAIN start-->
    <main>

        <!--LET US KNOW start-->
        <div class="let_us_know">

            <div class="wrap">

                <div class="let_us_know_title title">

                    <div class="container">

                        <h2>Let us know how we can help</h2>

                        <p>You can give us a call at 888-853-0355, send us an email at<br> <a
                                    href="mailto:Support@verticalraise.com">Support@verticalraise.com</a> or fill out
                            the form below.</p>

                    </div>

                </div>

                <div class="help_form">

                    <div class="container">
                    <?php echo do_shortcode( '[contact-form-7 id="53512" title="Contact us Page"]' ); ?>
                    </div>

                </div>

            </div>

        </div>
        <!--LET US KNOW end-->

    </main>
    <!--MAIN end-->


<?php get_footer(); ?>