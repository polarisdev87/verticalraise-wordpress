$(document).ready(function () {
  Date.prototype.addDays = function (days) {
    var date = new Date(this.valueOf());
    date.setDate(date.getDate() + days);
    return date;
  };

  $("#createFundraiserForm #start_date").datetimepicker({
    timepicker: false,
    format: "m/d/Y",
    onSelectDate: function (ct, $i) {
      $("#createFundraiserForm #end_date").val(
        ct.addDays(7 * 3).dateFormat("m/d/Y")
      );
    },
  });
  $("#createFundraiserForm #end_date").datetimepicker({
    timepicker: false,
    format: "m/d/Y",
  });

  jQuery("input.non_specific_char").each(function (index) {
    jQuery(this).val(function (index, value) {
      return value.replace(/[^A-Z0-9a-z ,.]+/g, "");
    });

    jQuery(this).keyup(function (event) {
      var key = event.which;

      jQuery(this).val(function (index, value) {
        return value.replace(/[^A-Z0-9a-z ,.]+/g, "");
      });
    });
  });

  $(".create_fundraiser").on("click", function () {
    setTimeout(function () {
      $("#create_fundraiser").modal("show");
    }, 300);
    $("[name=our_fee]").on("change", function () {
      var option = $(this).val();
      if (option == 1) {
        // 1 100% founds
        $("#create_fundraiser_100_deposit").modal("show");
      }
    });
  });

  $("#create_fundraiser_100_deposit_cancel").click(function (e) {
    $("#create_fundraiser_100_deposit").modal("hide");
    $("[name=our_fee]").val(2); // reset to  vr fee out other option
  });

  $("#create_fundraiser_100_deposit").on("hidden.bs.modal", function (e) {
    $("body").addClass("modal-open"); // fix scroll issue after dismiss modal inside modal
  });

  $("#create_fundraiser_100_deposit_accept").click(function (e) {
    $("#create_fundraiser_100_deposit").modal("hide");
    window.open(
      "https://drive.google.com/open?id=1pIzCbIKdp4WcvxxQQFev4fn1Zqnn5Hul",
      "_blank"
    ); //open in new tab
  });

  $(".fill_compaign_msg").on("click", function () {
    $('textarea[name="campaign_msg"]').val(
      "Fundraising is vital to the success of our program. Each participant has a monetary goal, please consider donating and sharing via email, text, and social media so that they may reach it. Your donation is greatly appreciated, and you will receive a tax-deductible receipt. Donations will be used toward the repair & purchase of equipment, training camps, competitions, travel costs, & uniforms. Thank you so much for your support!"
    );
  });

  $("#create_fundraiser.modal .close").on("click", function () {
    $("#createFundraiserForm").resetForm();
  });

  $("#fundraising_goal").inputmask("numeric", {
    radixPoint: ".",
    groupSeparator: ",",
    digits: 2,
    autoGroup: true,
    prefix: "$",
    rightAlign: false,
    oncleared: function () {
      self.value("");
    },
  });

  $.validator.addMethod(
    "fundraiserGoal",
    function (value, element) {
      var amount = value.replace("$", "").split(",").join("");
      amount = parseInt(amount);
      return this.optional(element) || amount > 0;
    },
    "Please enter a positive number"
  );

  $.validator.addMethod(
    "estimatedTeamSize",
    function (value, element) {
      var amount = value;
      amount = parseInt(amount);
      return this.optional(element) || amount > 0;
    },
    "Please enter a positive number"
  );

  //------------------create fundraiser-------------------//
  $("#createFundraiserForm").validate({
    rules: {
      direct_account: {
        required: true,
      },
      confirm_account: {
        required: true,
        equalTo: "#createFundraiserForm .account",
      },
      coach_code: {
        required: true,
        range: [75, 80],
      },
      tax_id: {
        required: true,
        minlength: 9,
        maxlength: 9,
      },
      tax_id2: {
        required: false,
        minlength: 9,
        maxlength: 9,
      },
      zipcode: {
        minlength: 5,
        maxlength: 5,
      },
      est_team_size: {
        estimatedTeamSize: true,
      },
      fundraising_goal: {
        fundraiserGoal: true,
      },
      fundraiser_name: {
        minlength: 5,
      },
    },
    messages: {
      direct_account: {
        required: "Please enter a valid account",
      },
      coach_code: {
        required: "Please enter a rep code",
        range: "Please enter a value between 75 and 80",
      },
      confirm_account: {
        required: "Please enter an account number",
        equalTo: "Doe not match the entered account number",
      },
      tax_id: {
        required: "Please enter a valid tax ID",
        minlength: "Please enter 9 digits for the tax ID",
        maxlength: "Please enter 9 digits for the tax ID",
      },
      zipcode: {
        minlength: "Zip code must be 5 digits",
        maxlength: "Zip code must be 5 digits",
      },
      fundraising_goal: {
        fundraiserGoal: "Please Enter a value greater than zero",
      },
    },
  });

  $("#createFundraiserForm [name='tax_id']").inputmask("99-9999999", {
    placeholder: " ",
    autoUnmask: true,
  });
  $("#editFundraiserForm [name='tax_id']").inputmask("99-9999999", {
    placeholder: " ",
    autoUnmask: true,
  });
  $("#createFundraiserForm [name='tax_id2']").inputmask("99-9999999", {
    placeholder: " ",
    autoUnmask: true,
  });
  $("#editFundraiserForm [name='tax_id2']").inputmask("99-9999999", {
    placeholder: " ",
    autoUnmask: true,
  });

  $("#createFundraiserForm input[name=direct_account]").on(
    "change focus",
    function () {
      $("#bank-error-message").text("");
      $("#bank-error-message").hide();
    }
  );
  $("#createFundraiserForm #create_fundraiser").on("click", function () {
    var btn = $(this);

    if ($('#createFundraiserForm input[name="showCheck"]').is(":checked")) {
      $("#createFundraiserForm #showPc_table").val(1);
    } else {
      $("#createFundraiserForm #showPc_table").val(0);
    }

    if (!$("#createFundraiserForm").valid()) {
      $("#createFundraiserForm").valid();
      console.log("Validate form");
      var $container = $(".create_fundraiser_modal");
      var $scrollTo = $(".error:visible").first();
      $container.animate({
        scrollTop:
          $scrollTo.offset().top -
          $container.offset().top +
          $container.scrollTop() -
          20,
      });
    } else {
      $(btn).buttonLoader("start");

      if (
        $('#createFundraiserForm select[name="payment_option"]').val() == "1"
      ) {
        stripe_connect($(btn));
      } else {
        submit_fundraiserForm($(btn));
      }
    }
  });

  function stripe_connect(btn) {
    stripe
      .createToken("bank_account", {
        country: "US",
        currency: "usd",
        routing_number: $("input[name=routing]").val(),
        account_number: $("input[name=direct_account]").val(),
        account_holder_name: $("input[name=bank_account_name]").val(),
        account_holder_type: "individual",
      })
      .then(function (result) {
        // Handle result.error or result.token

        if (result.token) {
          var token = result.token.id;
          $("input[name=b_token]").val(token);
          submit_fundraiserForm(btn);
        } else {
          var error = result.error;
          $("input[nmae=account_id]").val("");
          $("#bank-error-message").text(error.message);
          $("#bank-error-message").show();
          btn.buttonLoader("stop");
          return false;
        }
      });
  }
  function submit_fundraiserForm(btn) {
    $.ajax({
      beforeSend: function () {
        $("#fundraiser_create_error").hide();
      },
      type: "POST",
      url: "/create-fundraise",
      data: $("#createFundraiserForm").serialize(),

      success: function (json) {
        console.log(json);
        console.log("success");
        btn.buttonLoader("stop");
        $("#create_fundraiser").modal("hide");
        $("#success_model").modal("show");
        $("#success_model .modal-title").text(
          "Your fundraiser has been created."
        );
        $("#success_model button.confirm").data("href", json.data);
      },
      error: function (e) {
        if (typeof e.responseJSON.message !== "undefined") {
          $("#fundraiser_create_error_message").text(e.responseJSON.message);
        }
        console.log(e);
        console.log("error");
        $("#fundraiser_create_error").show();
        btn.buttonLoader("stop");
      },
    });
  }

  //    createfund_disable('check_by_mail');
  createfund_disable("direct_diposit");
  createfund_disable("coach_info");
  createfund_enable("check_by_mail");

  $("#success_model button.confirm").on("click", function () {
    location.href = $(this).data("href");
  });
  $('#createFundraiserForm select[name="hear_about_us"]').on(
    "change",
    function () {
      $("select[name=payment_option] option:first-child").prop(
        "selected",
        "selected"
      );
      createfund_disable("direct_diposit");
      createfund_disable("check_by_mail");
      $("#createFundraiserForm .direct_diposit").hide();
      $("#createFundraiserForm .check_by_mail").hide();

      if (this.value == "Fundraising Rep") {
        $("#createFundraiserForm .coach_info").show();
        $("select[name=payment_option]").append(
          '<option value="1" class="direct">Direct Deposit</option>'
        );
        $("select[name=payment_option] .direct").show();
        createfund_enable("coach_info");
      } else {
        $("#createFundraiserForm .coach_info").hide();
        $("select[name=payment_option] .direct").remove();
        createfund_disable("coach_info");
      }
    }
  );

  $('#createFundraiserForm select[name="payment_option"]').on(
    "change",
    function () {
      if (this.value == "1") {
        $("#createFundraiserForm .address_field").show();
        $("#createFundraiserForm .direct_diposit").show();
        $("#createFundraiserForm .check_by_mail").hide();
        createfund_enable("direct_diposit");
        createfund_disable("check_by_mail");
      } else if (this.value == "0") {
        $("#createFundraiserForm .address_field").show();
        $("#createFundraiserForm .check_by_mail").show();
        $("#createFundraiserForm .direct_diposit").hide();
        createfund_enable("check_by_mail");
        createfund_disable("direct_diposit");
      } else {
        $("#createFundraiserForm .address_field").hide();
        $("#createFundraiserForm .check_by_mail").hide();
        $("#createFundraiserForm .direct_diposit").hide();
        createfund_disable("check_by_mail");
        createfund_disable("direct_diposit");
      }
    }
  );

  function createfund_disable(elm) {
    $("#createFundraiserForm ." + elm + " :input").each(function () {
      $(this).prop("disabled", true);
    });
  }

  function createfund_enable(elm) {
    $("#createFundraiserForm ." + elm + " :input").each(function () {
      $(this).prop("disabled", false);
    });
  }

  //------------------upload picture popup--------------------//

  $("#avada_change").on("click", function () {
    setTimeout(function () {
      $("#upload_pro_pic").modal("show");
    }, 300);
  });

  $("#upload_pro_pic .fancybox").on("click", function () {
    $("#upload_pro_pic").modal("hide");
  });
});
