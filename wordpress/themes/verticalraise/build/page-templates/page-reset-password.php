<?php /* Template Name: Reset Password Template */

if(!is_user_logged_in()) {
    get_header();

    ?>
    <main>

        <!--MEMBER FORM SECTION start-->
        <div class="reset_password">

            <div class="reset_title">

                <div class="container">
                    <div class="row">
                        <h2>Reset Password</h2>
                    </div>
                </div>
            </div>


            <div class="reset_pass_form">

                <div class="container">
                    <div class="row">

                        <form id="resetpasswordForm" class="reset" action="" method="POST" role="form">
                            <div class="form-group">
                                <input type="password" class="form-control ip" id="pwd1" name="pwd1"
                                       placeholder="Password" required="" />
                            </div>

                            <div class="form-group">
                                <input type="password" class="form-control ip" name="pwd2"
                                       placeholder="Confim Password" required="" />
                            </div>
                            <input type="hidden" name="key"
                                   value="<?php echo(isset($_GET['action']) && !empty($_GET['action']) ? $_GET['action'] : '') ?>" />
                            <input type="hidden" name="reset_password" />


                            <button id="reset_btn" data-loading="Submitting..." class="submit_btn has-spinner">Reset
                            </button>

                        </form>
                    </div>

                </div>
            </div>

        </div>
        <!--MEMBER FORM SECTION end-->

    </main>
    <!--MAIN end-->
    <?php

    get_footer();
}else {
    header('Location: ' . get_bloginfo('url'));
}
?>