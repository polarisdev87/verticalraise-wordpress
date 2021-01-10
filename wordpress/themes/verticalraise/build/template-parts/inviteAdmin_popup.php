<!--Invite Admin POPUP start-->
<?php
while ( have_posts() ) :
    the_post();
    $f_id = get_the_ID();
endwhile;
if ( isset($_GET['fundraiser_id']) ) {
    $f_id = $_GET['fundraiser_id'];
} else {
    $f_id = $f_id;
}
?>
<script src="<?php bloginfo('template_directory'); ?>/assets/js/invite_admin.footer.js?ts=<?php echo time(); ?>"></script>
<div class="modal fade login_model" data-backdrop="static" id="invite_admin_modal">
    <div class="modal-dialog">

        <div class="modal-content">
            <!-- header -->
            <div class="modal-header model_title">
                <button type="button" class="close show_in_mob1" data-dismiss="modal" aria-label="Close"></button>
                <img src="<?php bloginfo('template_directory'); ?>/assets/images/icon1.png" alt="">
                <h3 class="modal-title">Add Admins</h3>
                <p>This will send an email with the admin join code and instructions on how to join the fundraiser as an admin.</p>
            </div>
            <!-- body -->
            <div class="modal-header">
                <form id="InviteAdminForm" class="" action="" method="POST" role="form">
                    <div class="form-group">
                        <textarea name="sadmin_email" class="form-control"
                                  placeholder="Enter email address separated by commas." required="required">
                        </textarea>
                        <div id="emailfields" style="display: none"> </div>
                        <div class="suggestions" ><label>Did you mean? (Click to update)</label><ul></ul></div>
                        <div class="invalidemails" ><label>Invalid Emails</label><ul></ul></div>

                        <input type="text" id="email_check_status" value="" required="" style="display:none"/>

                    </div>
                    <input type="hidden" name="inviteadmin"/>
                    <input type="hidden" name="fundraiser_id" value="<?php echo $f_id ?>"/>
                </form>
                <div class="res_msg"></div>
            </div>
            <!-- footer -->
            <div class="modal-footer">
                <button class="btn btn-primary btn-block has-spinner" data-loading="Sending..." id="send_admin_request">
                    SEND REQUEST
                </button>
            </div>
        </div>

    </div>
</div>

<!--Invite Admin end-->