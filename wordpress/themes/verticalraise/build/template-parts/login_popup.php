<!--LOGIN POPUP start-->
<div class="modal fade login_model" id="login_model">
    <div class="modal-dialog">

        <div class="modal-content">
            <!-- header -->
            <div class="modal-header model_title">
                <button type="button" class="close show_in_mob1" data-dismiss="modal" aria-label="Close"></button>
                <img src="<?php bloginfo('template_directory'); ?>/assets/images/icon1.png" alt="">
                <h3 class="modal-title">login to Vertical Raise</h3>
            </div>
            <!-- body -->
            <div class="modal-header">
                <form id="joinusForm" class="login" action="" method="POST" role="form">
                    <div class="form-group">
                        <input type="email" class="form-control" name="login_email" placeholder="Username/Email Address*" />
                        <input type="password" class="form-control" name="password" placeholder="Enter Password*" />
                    </div>
                    <input type="hidden" name="logina" />
                    <input type="hidden" name="rememberme" id="rememberme" value="1">
                </form>
            </div>
            <!-- footer -->
            <div class="modal-footer">
                <button class="btn btn-primary btn-block has-spinner" data-loading="Login..." id="joinus">CONTINUE →</button>
            </div>

            <div class="other_options">

                <div class="checkboxDIv">
                    <input type="checkbox" checked class="icheckbox_flat" name="rememberCheck"
                       tabindex="2" />
                    <label for="rememberme" class="">
                        Remember Me?
                    </label>
                    <a href="#" class="forget_pw">Forgot Password</a>
                </div>
            </div>

        </div>
        <div class="no_member_yet">
            <p>not a member yet? <a href="#">join now</a></p>
        </div>
    </div>
</div>

<!--LOGIN POPUP end-->