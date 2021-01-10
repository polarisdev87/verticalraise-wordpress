

$(document).ready(function () {
    $('#editFundraiserForm #start_date1').datetimepicker({
        timepicker: false,
        format: 'm/d/Y'
    });
    $('#editFundraiserForm #end_date1').datetimepicker({
        timepicker: false,
        format: 'm/d/Y'
    });

    jQuery("input.non_specific_char").each(function (index) {

        jQuery(this).val(function (index, value) {
            return value.replace(/[^A-Z0-9a-z ,.]+/g, '');
        });

        jQuery(this).keyup(function (event) {
            var key = event.which;

            jQuery(this).val(function (index, value) {
                return value.replace(/[^A-Z0-9a-z ,.]+/g, '');
            });

        })

    })
    $(".fill_compaign_msg").on("click", function () {
        $('textarea[name="campaign_msg"]').val("Fundraising is vital to the success of our program. Each participant has a monetary goal, please consider donating and sharing via email, text, and social media so that they may reach it. Your donation is greatly appreciated, and you will receive a tax-deductible receipt. Donations will be used toward the repair & purchase of equipment, training camps, competitions, travel costs, & uniforms. Thank you so much for your support!");
    });


    $(".modal .close").on("click", function () {
        $("#editFundraiserForm").resetForm();
    })


    //------------------edit fundraiser-------------------//


    $("#editFund_goal").inputmask("numeric", {
        radixPoint: ".",
        groupSeparator: ",",
        digits: 2,
        autoGroup: true,
        prefix: '$',
        rightAlign: false,
        oncleared: function () {
            self.value('');
        }
    })
    $(".edit_fundraiser").on("click", function () {
        setTimeout(function () {
            $('#edit_fundraiser').modal('show');
        }, 300);
    });

    $('#editFundraiserForm select[name="hear_about_us"]').on('change', function () {
        if (this.value == 'Fundraising Rep') {
            $("#editFundraiserForm .coach_info").show();
            editfund_enable('coach_info');

            $("select[name=payment_option] .direct").show();

        } else {

            $("select[name=payment_option] .direct").hide();
            editfund_disable('direct_diposit');
            $("#editFundraiserForm .direct_diposit").hide();

            $("select[name=payment_option] .checkpay").prop("selected", "selected");
            $("select[name=payment_option]").trigger("change");
            $('#editFundraiserForm .coach_info').hide();

            editfund_disable('coach_info');
        }
    });


    $('#editFundraiserForm select[name="payment_option"]').on('change', function () {
        if (this.value == '1') {
            $("#editFundraiserForm .direct_diposit").show();
            $("#editFundraiserForm .check_by_mail").hide();
            $("#createFundraiserForm .address_field").show();
            editfund_enable('direct_diposit');
            editfund_disable('check_by_mail');

        } else if (this.value == '0') {
            $("#editFundraiserForm .check_by_mail").show();
            $("#editFundraiserForm .direct_diposit").hide();
            $("#createFundraiserForm .address_field").show();
            editfund_enable('check_by_mail');
            editfund_disable('direct_diposit');
        } else {
            $("#editFundraiserForm .check_by_mail").hide();
            $("#editFundraiserForm .direct_diposit").hide();
            $("#createFundraiserForm .address_field").hide();
            editfund_disable('check_by_mail');
            editfund_disable('direct_diposit');
        }
    });


    $("#editFundraiserForm input[name=direct_account]").on("change focus", function () {
        $("#bank-error-message").text('');
        $("#bank-error-message").hide();
    });

    //if checked direct deposit for payment option

    $(".direct_diposit a").click(function () {
        if ($(this).hasClass("active")) {
            $(this).removeClass("active");
            $(this).text("Edit Bank Account");
            $(".current-stripe-account").show();
            $(".edit-stripe-account").hide();
            editfund_disable('edit-stripe-account');
            $("input[name=edit_flag]").val(0);
        } else {
            $(this).addClass("active");
            $(this).text("Cancel Edit");
            $(".current-stripe-account").hide();
            $(".edit-stripe-account").show();
            editfund_enable('edit-stripe-account');
            $("input[name=edit_flag]").val(1);
        }
    })


    $("#editFundraiserForm #edit_fundraiser").on("click", function () {

        var btn = $(this);
        if ($('#editFundraiserForm input[name="showCheck"]').is(":checked")) {
            $("#editFundraiserForm #showPc_table1").val(1);
        } else {
            $("#editFundraiserForm #showPc_table1").val(0);
        }

        if ($('#editFundraiserForm select[name="payment_option"]').val() == "1") {
            editfund_disable('check_by_mail');
        }
        if ($('#editFundraiserForm select[name="payment_option"]').val() == "0") {
            editfund_disable('direct_diposit');
        }

        if ($('#editFundraiserForm select[name="hear_about_us"]').val() != "Fundraising Rep") {
            editfund_disable('coach_info');
        }


        if (!$("#editFundraiserForm").valid()) {
            $("#editFundraiserForm").valid();
        } else {
            // var form = $("#editFundraiserForm")[0];
            // var data = new FormData(form);
            $(btn).buttonLoader('start');

            if ($('#editFundraiserForm select[name="payment_option"]').val() == '1') {
                var stripe_connect_flag = parseInt($("input[name=edit_flag]").val());
                var stripe_connect_status = parseInt($("input[name=stripe_connect]").val());

                if (stripe_connect_status) {
                    if (stripe_connect_flag) {
                        submitWithStripeConnect($(btn));
                    } else {
                        submit_fundraiserForm($(btn));
                    }
                } else {
                    submitWithStripeConnect($(btn));
                }

            } else {
                submit_fundraiserForm($(btn));
            }
        }
    });

    function submitWithStripeConnect(btn) {
        stripe.createToken('bank_account', {
            country: 'US',
            currency: 'usd',
            routing_number: $("input[name=routing]").val(),
            account_number: $("input[name=direct_account]").val(),
            account_holder_name: $("input[name=bank_account_name]").val(),
            account_holder_type: 'individual',
        }).then(function (result) {
//            console.log(result);return false;
            // Handle result.error or result.token
            if (result.token) {
                var token = result.token.id;
                $("input[name=b_token]").val(token);
                submit_fundraiserForm(btn);

            } else {
                var error = result.error;
                $("input[name=account_id]").val('')
                $("#bank-error-message").text(error.message);
                $("#bank-error-message").show();
                btn.buttonLoader('stop');
                return false;
            }
        });
    }

    function submit_fundraiserForm(btn) {
        $.post(
                LoginAjaxUrl + "/edit-fundraiser",
                $("#editFundraiserForm").serializeArray(),
                function (result) {
                    var json = JSON.parse(result);

                    if (json.status) {
                        btn.buttonLoader('stop');
                        $('#edit_fundraiser').modal('hide');
                        $('#success_model').modal('show');
                        $('#success_model .modal-title').text("Your fundraiser has been updated.");
                        $('#success_model button.confirm').data("href", json.data);
                    } else {
                        $("#bank-error-message").text(json.error);
                        btn.buttonLoader('stop');
                    }
                }
        );
    }

    $("#success_model button.confirm").on('click', function () {
        location.href = $(this).data("href");
    })

    $("#editFundraiserForm").validate({
        rules: {
            direct_account: {
                required: true,
            },
            confirm_account: {
                required: true,
                equalTo: "#editFundraiserForm .account"
            }
        },
        messages: {
            direct_account: {
                required: "Please enter valid account.",
            },
            confirm_account: {
                required: "Please enter valid confirm account.",
                equalTo: "it must same as account."
            }
        }
    })



    function editfund_disable(elm) {
        $("#editFundraiserForm ." + elm + " :input").each(function () {
            $(this).prop("disabled", true)
        });
    }

    function editfund_enable(elm) {
        $("#editFundraiserForm ." + elm + " :input").each(function () {
            $(this).prop("disabled", false)
        });

    }

    /*
     upload logo
     object: $("#fundlogoFile")
     */

//    $("#editFundraiserForm .logo_update").on("click", function () {
//        $("#fundlogoFile").trigger("click");
//
//    });

    /*
     upload youtube video
     object: $("#fundvideoFile")
     */
    $("#editFundraiserForm .video_change").on("click", function () {
        // console.log("DDDD", _token);
        $("#fundvideoFile").trigger("click");

    });

    function handleFileSelect(evt) {
        var orientation;
        var image, canvas;
        var files = evt.target.files;

        getOrientation(document.getElementById("fundlogoFile").files[0], function (image_orientation) {
            orientation = image_orientation;
            // Loop through the FileList and render image files as thumbnails.
            for (var i = 0, f; f = files[i]; i++) {
                // Only process image files.
                if (!f.type.match('image.*')) {
                    continue;
                }

                image = new Image();
                image.src = createObjectURL(files[i]);
                image.onload = function (e) {
                    var mybase64resized = resizeCrop(e.target, 400, 400, orientation).toDataURL(files[i].type, 90);
                    $("#editFundraiserForm .logo_update").css('background-image', 'url(' + mybase64resized + ')');
                    $("#editFundraiserForm #logoImage").val(mybase64resized);
                    $("#editFundraiserForm #logoImageName").val(files[i].name);
                }
                return false;
            }
        });

    }
    function handlevideoSelect() {

        if (!_token) {
            $("#___signin_0 button").trigger("click");
        } else {
            if ($('#fundvideoFile').val()) {
                var uploadVideo = new UploadVideo();
                uploadVideo.ready(_token);
            }
        }
    }

//    document.getElementById('fundlogoFile').addEventListener('change', handleFileSelect, false);

    $('#fundvideoFile').on('change', function () {
        handlevideoSelect()
    });


    // =========================== Invite admiin ===============================//

    $(".invite_admin").on("click", function () {
        setTimeout(function () {
            $("#invite_admin_modal .res_msg").children().remove();
            $("#invite_admin_modal textarea").val('');
            $('#invite_admin_modal #InviteAdminForm .suggestions').hide();
            $('#invite_admin_modal #InviteAdminForm .invalidemails').hide();

            $('#invite_admin_modal').modal('show');
        }, 300);
    });

    $("#invite_admin_modal #send_admin_request").on("click", function () {

        var btn = $(this);
        if (!$("#InviteAdminForm").valid()) {
            $("#InviteAdminForm").valid();
        } else {

            var form = $("#InviteAdminForm")[0];
            var data = new FormData(form);

            $(btn).buttonLoader('start');
            setTimeout(function () {
                if ($('#email_check_status').val() == '' /* || $('#InviteAdminForm .suggestions ul li').length != 0 || $('#InviteAdminForm .invalidemails ul li').length != 0*/) {
                    $(btn).buttonLoader('stop');
                    return false;
                }

                $.ajax({
                    type: "POST",
                    url: LoginAjaxUrl + "/invite-sadmin",
                    enctype: "multipart/form-data",
                    data: data,
                    processData: false,
                    contentType: false,
                    cache: false,
                    dataType: 'json',
                    success: function (result) {

                        if (result.length > 0) {
                            var msgHTML = '';
                            for (var i = 0; i < result.length; i++) {
                                msgHTML += result[i];
                            }
                            $("#invite_admin_modal .res_msg").append(msgHTML);
                        } else {
                            // $(json.data).insertBefore("#login_model .modal-footer")
                        }

                        $(btn).buttonLoader('stop');
                    },
                    error: function (e) {
                        console.log("ERROR", e);
                    }
                });

            }, 2000)



        }
    });

    $("#editprofileForm input").focus(function () {
        $("input").removeClass("focus");
        if ($(this).val() == '') {
            add_placeholder($(this));
        }
    }).keydown(function () {
        $(this).css('color', '#000')
        reset_attr($(this));
        if ($(this).val() == $(this).attr('placeholder')) {
            $(this).val('');
            $(this).css('color', '#727272')
        }
    }).keyup(function () {
        if ($(this).val() == '') {
            add_placeholder($(this));
        }
    }).blur(function () {
        if ($(this).val() == $(this).attr('placeholder'))
            $(this).val('');
        $(this).removeClass("focus");
    });
});

$(window).load(function () {
    if ($('#editFundraiserForm select[name="hear_about_us"]').val() != "Fundraising Rep") {
        $('#editFundraiserForm select[name="hear_about_us"]').trigger("change");
    }
});