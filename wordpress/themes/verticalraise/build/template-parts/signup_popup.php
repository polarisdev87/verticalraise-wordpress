<!--Signup Popup Start-->
<div class="modal fade login_model signup_model" id="signup_model">
    <div class="modal-dialog">

        <div class="modal-content">
            <!-- header -->
            <div class="modal-header model_title">
                <button type="button" class="close show_in_mob1" data-dismiss="modal" aria-label="Close"></button>
                <img src="<?php bloginfo('template_directory'); ?>/assets/images/icon1.png" alt="">
                <h3 class="modal-title">Join Vertical Raise </h3>
            </div>
            <!-- body -->
            <div class="modal-header">
                <form id="signupForm" class="registration" action="" method="post" enctype="multipart/form-data" role="form" >
                    <div class="form-group">
                        <div><input type="text" name="fname" class="form-control" placeholder="First Name*" required=""/></div>
                        <div><input type="text" name="lname" class="form-control" placeholder="Last Name*"  required=""/></div>
                        <div class="email_field">
                            <input type="email" name="reg_email" id="e1" data-validEmail="" 
                                   class="form-control" placeholder="Email Address*" 
                                   required="" autocomplete="off"/>
                            <div class="tc-result" title="Validated by TheChecker.co" >
                                <img class="tc-result-icon" src="<?php bloginfo('template_directory'); ?>/assets/images/error.png">
                            </div>
                            <p id="suggestion" ></p>
                            <div id="invalid"></div>
                        
                        </div>
                        <div><input type="password" name="password1" id="pw1" class="form-control" placeholder="Password*" required="" /></div>
                        <div><input type="password" name="password2" id="pw2" class="form-control" placeholder="Verify Password*" required="" /></div>
                    </div>
                    <input type="hidden" name="register" />                
                </form>
            </div>

            <div class="other_options">
                <p>By clicking continue, you are agreeing to the <a href="<?php echo get_the_permalink(157); ?> "  target="_blank">Terms and Conditions</a> of the site and the <a href="/privacy-policy/" target="_blank">Privacy Policy</a>.</p>
            </div>

            <!-- footer -->
            <div class="modal-footer">
                <button class="btn btn-primary btn-block has-spinner" data-loading="SignUp..." id="signup">CONTINUE →</button>
                <p>*Signup to create or join a Vertical Raise fundraiser and receive periodic fundraiser updates.</p>
            </div>

        </div>
        <div class="no_member_yet">
            <p>Already a member? <a href="#">Sign in</a></p>
        </div>
    </div>
</div>
<!--Signup Popup End-->