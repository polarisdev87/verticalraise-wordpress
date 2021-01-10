
jQuery(document).ready(function () {
//    $.validator.addMethod("emailChecker", function (value, element) {
//        if ($(element).data("validEmail") != "valid") {
//            return false;
//        } else {
//            return true;
//        }
//    }, "This email is not valid.");
    
    $("#signupForm").validate({
        rules: {
            fname: "required",
            lname: "required",
            reg_email: {
                required: true,
                email: true
//                emailChecker: true
            },
            password1: {
                required: true,
                minlength: 5
            },
            password2: {
                required: true,
                minlength: 5,
                equalTo: "#signupForm #pw1"
            }
        },
        messages: {
            fname: "Please enter your firstname.",
            lname: "Please enter your lastname",
            reg_email: {
                required: "Please enter your email address.",
                email: "Please enter a valid email address"
            },
            password1: {
                required: "Please provide a password.",
                minlength: "Your password must be at least 5 characters long"
            },
            password2: {
                required: "Please provide confirm password.",
                minlength: "Your password must be at least 5 characters long",
                equalTo: "Please enter the same password as above"
            }
        }

    });
    $("#joinusForm").validate({
        rules: {
            login_email: {
                required: true,
                email: true
            },
            password: {
                required: true,
                minlength: 5
            },
        },
        messages: {
            login_email: {
                required: "Please enter your email address.",
                email: "Please enter a valid email address"
            },
            password: {
                required: "Please provide a password.",
                minlength: "Your password must be at least 5 characters long"
            },
        }
    });
    $("#forgotpasswordForm").validate({
        rules: {
            emailid: {
                required: true,
                email: true
            }
        },
        messages: {
            emailid: {
                required: "Please enter your email address.",
                email: "Please enter a valid email address"
            }
        }
    });

    $("#resetpasswordForm").validate({
        rules: {
            pwd1: {
                required: true,
                minlength: 5
            },
            pwd2: {
                required: true,
                minlength: 5,
                equalTo: "#resetpasswordForm #pwd1"
            }
        },
        messages: {
            pwd1: {
                required: "Please provide a password.",
                minlength: "Your password must be at least 5 characters long"
            },
            pwd2: {
                required: "Please provide confirm password.",
                minlength: "Your password must be at least 5 characters long",
                equalTo: "Please enter the same password as above"
            }
        }
    });


    //login modal
    jQuery(".loginLink").on("click", function () {
        setTimeout(function () {
            $('#login_model').modal('show');
        }, 300);
    });

    $("#login_model .no_member_yet a").click(function () {
        $('#login_model').modal('hide');
        setTimeout(function () {
            $('#signup_model').modal('show');
        }, 300);
    });


    //click continue button to login
    $("#login_model #joinusForm").find(':input').each(function () {
        $(this).keypress(function (e) {
            if (e.keyCode == 13) {
                $("#login_model #joinus").trigger("click");
            }
        })
    })
    $("#login_model #joinus").on("click", function () {

        var btn = $(this);

        if ($('input[name="rememberCheck"]').is(":checked")) {
            $("#login_model #rememberme").val(1);
        } else {
            $("#login_model #rememberme").val(0);
        }
        if (!$("#joinusForm").valid()) {
            $("#joinusForm").valid();
        } else {
            if ($(".errorMsg").length > 0) {
                $(".errorMsg").remove();
            }
            $(btn).button('loading');

            var is_online = false;

            if (navigator.onLine) {
                is_online = true;
            }
            console.log(is_online);
            $.post(
                    LoginAjaxUrl + "/login",
                    $("#joinusForm").serializeArray(),
                    function (result) {

                        var json = result;

                        if (json.success) {

                            location.href = json.data;
                        } else {
                            $(btn).button('reset');
                            $(json.data).insertBefore("#login_model .modal-footer")
                        }

                    },
                    'json'
                    );
        }
    });

    //signup modal
    jQuery(".signupLink").on("click", function () {
        setTimeout(function () {
            $('#signup_model').modal('show');
        }, 300);
    });

    $("#signup_model .no_member_yet a").click(function () {
        $('#signup_model').modal('hide');
        setTimeout(function () {
            $('#login_model').modal('show');
        }, 300);
    });

    $("#signup_model #signupForm").find(':input').each(function () {
        $(this).keypress(function (e) {
            if (e.keyCode == 13) {
                $("#signup_model #signup").trigger("click");
            }
        })
    })
    //click continue button to signup
    $("#signup_model #signup").on("click", function (e) {
        e.preventDefault();
        var btn = $(this);

        if (!$("#signupForm").valid()) {
            $("#signupForm").valid();
        } else {

            if ($(".errorMsg").length > 0) {
                $(".errorMsg").remove();
            }
            $(btn).button('loading');
            console.log("This is desktop view.");
            $.post(
                    LoginAjaxUrl + "/sign-up",
                    $("#signupForm").serializeArray(),
                    function (result) {

                        var json = result;
                        if (!json.status) {
                            $(json.data).insertBefore("#signup_model .modal-footer");
                            $(btn).button('reset');
                        } else {
                            location.href = json.data;
                        }
                    },
                    'json'
                    );
        }
    });

    //forgot password modal
    //signup modal
    jQuery(".forget_pw").on("click", function () {
        $('#login_model').modal('hide');
        setTimeout(function () {
            $('#forgotpassword_model').modal('show');
        }, 300);
    });

    $("#forgotpassword_model .no_member_yet a").click(function () {
        $('#forgotpassword_model').modal('hide');
        setTimeout(function () {
            $('#login_model').modal('show');
        }, 300);
    });

    //click continue button to signup
    $("#forgotpassword_model #forgotpass").on("click", function () {
        var btn = $(this);

        if (!$("#forgotpasswordForm").valid()) {
            $("#forgotpasswordForm").valid();
        } else {
            if ($(".errorMsg").length > 0) {
                $(".errorMsg").remove();
            }

            $(btn).button('loading');
            $.post(
                    LoginAjaxUrl + "/forgot-password",
                    $("#forgotpasswordForm").serializeArray(),
                    function (result) {

                        var json = result;
                        if (!json.status) {
                            $(json.data).insertBefore("#forgotpassword_model .modal-footer");
                            $(btn).button('reset');
                        } else {
                            // location.href = json.data;
                            $(json.data).insertBefore("#forgotpassword_model .modal-footer");
                            $(btn).button('reset');
                        }
                    },
                    'json'
                    );
        }
    });


    //click continue button to signup
    $("#resetpasswordForm #reset_btn").on("click", function (event) {

        event.preventDefault();
        var btn = $(this);

        if (!$("#resetpasswordForm").valid()) {
            $("#resetpasswordForm").valid();
        } else {
            if ($(".errorMsg").length > 0) {
                $(".errorMsg").remove();
            }

            $(btn).button('loading');
            $.post(
                    LoginAjaxUrl + "/reset-password-ajax",
                    $("#resetpasswordForm").serializeArray(),
                    function (result) {

                        $("#resetpasswordForm").resetForm();
                        var json = JSON.parse(result);
                        if (!json.status) {
                            $(json.data).insertBefore("#resetpasswordForm #reset_btn");
                            $(btn).button('reset');
                        } else {
                            // location.href = json.data;
                            $(json.data).insertBefore("#resetpasswordForm #reset_btn");
                            $(btn).button('reset');

                            setTimeout(function () {
                                $('#login_model').modal('show');
                            }, 1000);

                        }
                    }
            );
        }

    });

//    placeholder in signup, login, forgot password form
//#joinusForm input,#signupForm input,#forgotpasswordForm input,#resetpasswordForm input,
//    $(" #editprofileForm input").focus(function () {
//        $("input").removeClass("focus");
//        if ($(this).val() == '') {
//            add_placeholder($(this));
//        }
//    }).keydown(function () {
//        $(this).css('color','#000')
//        reset_attr($(this));        
//        if ($(this).val() == $(this).attr('placeholder')) {
//            $(this).val('');
//            $(this).css('color','#727272')
//        }        
//    }).keyup(function(){
//       if($(this).val() == '') {
//           add_placeholder($(this));            
//        } 
//    }).blur(function () {
//        if ($(this).val() == $(this).attr('placeholder'))
//            $(this).val('');
//        $(this).removeClass("focus");
//    });
})
