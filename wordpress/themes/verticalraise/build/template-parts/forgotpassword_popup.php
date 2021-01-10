<!--LOGIN POPUP start-->
<div class="modal fade login_model" id="forgotpassword_model">
    <div class="modal-dialog">

        <div class="modal-content">
            <!-- header -->
            <div class="modal-header model_title">
                <button type="button" class="close show_in_mob1" data-dismiss="modal" aria-label="Close"></button>
                <img src="<?php bloginfo('template_directory'); ?>/assets/images/icon1.png" alt="">
                <h3 class="modal-title">Forgot Password</h3>
            </div>
            <!-- body -->
            <div class="modal-header">
                <form id="forgotpasswordForm" class="forgot" action="" method="POST" role="form">
                    <div class="form-group">
                        <input type="email" class="form-control" name="emailid" placeholder="Email Address*" />
                    </div>
                    <input type="hidden" name="forgot" />
                </form>
            </div>
            <!-- footer -->
            <div class="modal-footer">
                <button class="btn btn-primary btn-block has-spinner" data-loading="Submitting..." id="forgotpass">Submit</button>
            </div>
        </div>
        <div class="no_member_yet">
            <p><a href="#">Sign in</a></p>
        </div>
    </div>
</div>
<!--LOGIN POPUP end-->