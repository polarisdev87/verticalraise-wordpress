<?php
/* Template Name: Edit Profile */

use \classes\pages\Edit_Profile_Page;

if ( is_user_logged_in() ) {

    global $user_ID;
    $edit_profile = new Edit_Profile_Page($user_ID);
    get_header();
    ?>

    <!--MAIN start-->
    <main>

        <!--EDIT PROFILE start-->
        <div class="edit_profile">
            <div class="profile_wrap">
                <div class="container">
                    <div class="mob_title">
                        <h4>Edit my profile</h4>
                    </div>

                    <div class="row">

                        <div class="col-md-4 col-sm-4 col-xs-12 col col_left">
                            <div class="user_photo">
                                <?php echo get_avatar($user_ID, 312); ?>
                                <a class="link photo_link fancybox_upload_pro_pic" id="avada_change" href="#">Add/Replace photo</a>
                            </div>
                        </div>

                        <div class="col-md-8 col-sm-8 col-xs-12 col col_right">
                            <h4>Edit my profile</h4>

                            <?php
                            $edit_profile->handle_form_submit($_POST);
                            $user_info    = get_userdata($user_ID);
                            ?>

                            <form class="registration" action="" method="post" id="editprofileForm" enctype="multipart/form-data">
                                <div>
                                    <input type="text" value="<?php echo $user_info->first_name; ?>" name="fname"
                                           placeholder="First Name*" class="form-control ip" required="required"/>
                                </div>
                                <div>
                                    <input type="text" value="<?php echo $user_info->last_name; ?>" name="lname"
                                           placeholder="Last Name*" class="form-control ip" required=""/>
                                </div>
                                <div class="email_field">
                                    <input type="email" id="e2" name="email_addr" value="<?php echo $user_info->user_email; ?>"
                                           placeholder="Email Address*" class="form-control ip" 
                                           required="" data-validEmail="valid" autocomplete="off"/>
                                    <div class="tc-result" title="Validated by TheChecker.co" >
                                        <img class="tc-result-icon" src="<?php bloginfo('template_directory'); ?>/assets/images/error.png">
                                    </div>
                                    <p id="suggestion" ></p>
                                </div>
                                <div>
                                    <input type="password" name="pw1" id="pass1" placeholder="Password*" class="form-control ip" />
                                </div>
                                <div>
                                    <input type="password" name="pw2" id="pass2" placeholder="Verify Password*" class="form-control ip" />
                                </div>
                                <input type="submit" name="register" value="Update" class="submit_btn">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--EDIT PROFILE end-->
    </main>
    <!--MAIN end-->
    <script type="text/javascript">

        $(document).ready(function () {          
            $('#editprofileForm input[name=email_addr]').on('blur', function () {
                email_verification($(this));
            })
            $('#editprofileForm input[name=email_addr]').on('keyup', function () {
                email_checker($(this));
            });
            $('#editprofileForm #suggestion').on('click', 'span', function () {
                // On click, fill in the field with the suggestion and remove the hint
                $('#editprofileForm input[name=email_addr]').val($(this).text());
                $('#editprofileForm #suggestion').fadeOut(200, function () {
                    $(this).empty();
                    email_verification($('#editprofileForm input[name=email_addr]'));
                });
            });
        });


        function email_verification(emailObj) {
    //                $('#signupForm #invalid').empty();
            var email = emailObj.val();
            if (email.length > 0) {
                $("#editprofileForm .tc-result").show();
            } else {
                $("#editprofileForm .tc-result").hide();
            }
            $.get("https://api.thechecker.co/v1/verify?email=" + email + "&api_key=<?php echo _THE_CHECKER_API_KEY ?>", function (data, status) {
                if (status != 'success') {
                    return false;
                } else {

                    var allowed_result = ['deliverable', 'risky', 'unknown'];
                    if (allowed_result.indexOf(data.result) > -1) {
                        $("#editprofileForm .tc-result-icon").attr("src", TEMP_DIRECTORY + "/assets/images/success.png");
    //                            $('#signupForm #invalid').empty();
                        emailObj.data("validEmail", "valid");
                    } else {
                        $("#editprofileForm .tc-result-icon").attr("src", TEMP_DIRECTORY + "/assets/images/error.png");
    //                            $('#signupForm #invalid').text("This email is not valid.");
                        emailObj.data("validEmail", "invalid");
                    }
                    $("#editprofileForm").validate().element("#e2");
                }
            });
        }

        function email_checker(emailObj) {
            $('#editprofileForm #invalid').empty();
            var topLevelDomains = ["com", "net", "org"];
            emailObj.mailcheck({
                topLevelDomains: topLevelDomains,
                suggested: function (element, suggestion) {
                    $('#editprofileForm #suggestion').fadeIn(200);
                    $('#editprofileForm #suggestion').html("Did you mean <span >" + suggestion.full + "</span> ?");
                },
                empty: function (element) {
                    $("#editprofileForm #suggestion").empty();
                }
            });
        }
    </script>   
    <?php
    get_footer();
} else {
    header('Location: ' . get_bloginfo('url'));
} 