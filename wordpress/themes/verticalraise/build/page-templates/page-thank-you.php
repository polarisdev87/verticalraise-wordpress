<?php 

/* Template Name: Thank You Template */ 

get_header(); 

$home_url = get_bloginfo('url');

?>
<div id="title">
	<div class="maincontent">
	    <div class="section group">
	        <div class="col span_12_of_12"> 
                <h1><span><?php the_title(); ?></span></h1>                
	        </div>
	    </div>
	</div>
</div>
<div id="content">
    <div class="maincontent noPadding">
        <div class="section group">
            <div class="col span_12_of_12">
                <?php
                
					$action = decripted($_GET['action']);
                
					if ( $action == 'registration' ) {
						//echo "<p class=\"successMsg\">Please check your mail to complete your registration ptocess.<br />if email does not appear in your inbox please check your spam folder</p>";
                        echo "<p class=\"successMsg\"><strong>Congratulations</strong><br /><br />Your account has been created. <strong><a style=\"text-decoration: underline; color: blue;\" href=\"{$home_url}/my-account\">Click here to go your MY ACCOUNT PAGE</a></strong></p>";
					}
                
					if ( $action == 'forgotpassword' ) {
						echo "<p class='successMsg'>Please check your registered email and click on the reset password link.</p>";
					}
                
					if ( $action == 'resetpassword' ) {
                ?>
					<p class="successMsg">Your password updated successfully. Please click here to <a class="alink" href="<?php echo $home_url ?>/login">Login</a>.</p>
				<?php } ?>
            </div>
        </div>
    </div>
</div>
<?php get_footer();