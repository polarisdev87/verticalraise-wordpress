<!--FOOTER start-->
<?php
global $template;

$page_name = basename($template);
?>
<footer>
    <!--CONTAINER start-->
    <div class="container">
        <?php
        if ( is_mobile() && $page_name == "page-donation.php" ) {
            
        } else {
            ?>
            <div class="footer_logo">
                <a href="#">
                    <?php if ( is_mobile_new() ) { ?>
                        <img src="<?php bloginfo('template_directory'); ?>/assets/images/mobile-footer-logo.png" alt="">
                    <?php } else { ?>
                        <img src="<?php bloginfo('template_directory'); ?>/assets/images/footer-logo.png" alt="">
                    <?php } ?>
                </a>
            </div>
            <div class="soc_icons">
                <ul>
                    <li>
                        <a class="google_link soc_link" href="<?php echo _SOCIAL_MEDIA_GOOGLE_PLUS_URL ?>" target="_blank" >
                        </a>
                    </li>
                    <li>
                        <a class="instagram_link soc_link" href="<?php echo _SOCIAL_MEDIA_INSTAGRAM_URL ?>" target="_blank">
                        </a>
                    </li>
                    <li>
                        <a class="facebook_link soc_link" href="<?php echo _SOCIAL_MEDIA_FACEBOOK_PAGE_URL ?>" target="_blank">
                        </a>
                    </li>
                    <li>
                        <a class="linkedin_link soc_link" href="<?php echo _SOCIAL_MEDIA_LINKEDIN_URL ?>" target="_blank">
                        </a>
                    </li>
                    <li>
                        <a class="twitter_link soc_link" href="<?php echo _SOCIAL_MEDIA_TWITTER_URL ?>" target="_blank">
                        </a>
                    </li>
                </ul>
            </div>
            <div class="page_links">
                <?php if ( !is_user_logged_in() ) { ?>
                    <ul>
                        <li>
                            <a href="<?php bloginfo('url'); ?>/">Home</a>
                        </li>
                        <li>
                            <a class="loginLink" href="#">Login</a>
                        </li>
                        <li>
                            <a class="signupLink" href="#">Sign-up</a>
                        </li>
                        <li>
                            <a href="<?php bloginfo('url'); ?>/contact-us/">Contact Us</a>
                        </li>
                        <li>
                            <a href="<?php bloginfo('url'); ?>/careers/">Careers</a>
                        </li>
                    </ul>
                <?php } else { ?>
                    <ul>
                        <li>
                            <a href="<?php bloginfo('url'); ?>/">Home</a>
                        </li>
                        <li>
                            <a href="<?php bloginfo('url'); ?>/contact-us/">Contact Us</a>
                        </li>
                        <li class="menu-item-has-children">
                            <a href="<?php bloginfo('url'); ?>/my-account/">My Account</a>

                        </li>
                        <li>
                            <a href="<?php echo wp_logout_url(get_bloginfo('url')); ?>">Log Out</a>
                        </li>
                    </ul>
                <?php } ?>
            </div>

        <?php } ?>
        <div class="sec_footer">
            <p>Copyright &copy; <?php echo date('Y') ?> vertical raise. all rights reserved.</p>
            <ul>
                <li>
                    <a href="<?php echo get_the_permalink(157); ?>"  target="_blank">Terms</a>
                </li>
                <li>
                    <a href="<?php echo get_the_permalink(379); ?>"  target="_blank">Privacy policy</a>
                </li>
            </ul>
        </div>
    </div>
    <!--CONTAINER end-->
</footer>
<!--FOOTER end-->

</div>
<!--WRAPPER end-->

<!-- Google Analytics event tracking -->
<script>
document.addEventListener( 'wpcf7mailsent', function( event ) {
ga('send', 'event', 'contact-form', 'submit');
});
</script>


<?php wp_footer(); ?>
</body>
</html>
