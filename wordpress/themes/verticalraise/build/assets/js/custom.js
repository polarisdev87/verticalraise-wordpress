$(document).ready(function () {
  $("ul.select_dontaion li input+label").matchHeight();

  $(window).scroll(function () {
    if ($(this).scrollTop() > 100) {
      $("header").addClass("stick");
      $(".sec_header.landing").addClass("stick");
      $(".sec_header.landing1").addClass("stick");
    } else {
      $("header").removeClass("stick");
      $(".sec_header.landing").removeClass("stick");
      $(".sec_header.landing1").removeClass("stick");
    }
  });

  //$(".eql").matchHeight();

  //Comments sidebar match height
  if (ismobile == "0") {
    //    if ( !$(".individual_profile .user_name").length ) {
    var eql_left =
      parseFloat($(".eql.col_left .widgets.video_sec").height()) +
      parseFloat($(".eql.col_left .widgets.about_fundraiser").height()) +
      parseFloat($(".eql.col_left .widgets.make_donation").height());
    var profile_right = parseFloat(
      $(".eql.col_right .widgets.individual_profile").height()
    );
    var padding = 100;
    var matchheight = eql_left - profile_right - padding;

    $(".supporters_comments .supporters_list").css(
      "height",
      matchheight + "px"
    );
    //    }
  }
  //Toggle Menu
  $("#nav-icon2").click(function () {
    $(this).toggleClass("open");
    $(".right_header").fadeToggle();
  });

  $(".right_header>ul>li.menu-item-has-children").append(
    "<span class='toggle_dropdown'></span>"
  );

  $(".right_header>ul>li.menu-item-has-children .toggle_dropdown").click(
    function () {
      $(this)
        .parent(".menu-item-has-children")
        .find(".mega_menu")
        .slideToggle();
      $(this).parent(".menu-item-has-children").toggleClass("show");
      $(this)
        .parent(".menu-item-has-children")
        .siblings(".menu-item-has-children")
        .find(".mega_menu")
        .slideUp();
      $(this)
        .parent(".menu-item-has-children")
        .siblings(".menu-item-has-children")
        .removeClass("show");
    }
  );

  $(".my_supporters .list").mCustomScrollbar();

  if (ismobile == "1") {
    if ($(".supporters_list").length > 0) {
      // -------  show more donators ----------- //
      $(".supporters_list .extraBtn a").on("click", function (e) {
        if ($(this).hasClass("morelist")) {
          var hideElm_count = $(this).parent().parent().children("li.hideClass")
            .length;
          if (hideElm_count != 0) {
            var n = 0;
            var self = $(this);
            $(this)
              .parent()
              .parent()
              .children("li.hideClass")
              .each(function () {
                n++;
                $(this).removeClass("hideClass").addClass("showClass");

                var showElm_count = self
                  .parent()
                  .parent()
                  .children("li.showClass").length;
                $(
                  ".supporters_list .extraBtn .donation-count-view .js-donation-count"
                ).text(showElm_count + 3);

                if (hideElm_count - n == 0) {
                  self.css("display", "none");
                }
                if (n == 6) {
                  return false;
                }
              });
          }
        }
      });
    }
  } else {
    $(".supporters_comments .supporters_list").mCustomScrollbar();
  }

  $(".participant_table .table_body").mCustomScrollbar();
  //    $("input").focus(function () {
  //        $("input").removeClass("focus");
  //        $("textarea").removeClass("focus");
  //        $(this).addClass("focus");
  //    }).blur(function () {
  //        $("input").removeClass("focus");
  //        $("textarea").removeClass("focus");
  //    });
  //
  //    $("textarea").focus(function () {
  //        $("input").removeClass("focus");
  //        $("textarea").removeClass("focus");
  //        $(this).addClass("focus");
  //    }).blur(function () {
  //        $("input").removeClass("focus");
  //        $("textarea").removeClass("focus");
  //    });
  /*
     *
     if ($(".edit_profile .successMsg").length > 0) {
     setTimeout(function () {
     location.href = '/my-account';
     }, 2000)
     }
     */
});

$(document).ready(function () {
  //question form validation
  $("#question").validate({
    rules: {
      fullname: "required",
      email: {
        required: true,
        email: true,
      },
      phone: {
        required: true,
      },
    },
    messages: {
      fullname: "Please enter your name.",
      email: {
        required: "Please enter your email address.",
        email: "Please enter a valid email address",
      },
      phone: {
        required: "Please enter your phone number.",
      },
    },
  });

  $("#contactform").validate({
    rules: {
      firstname: "required",
      lastname: "required",
      email: {
        required: true,
        email: true,
      },
      phone: {
        required: true,
      },
    },
    messages: {
      fullname: "Please enter your firstname.",
      lastname: "Please enter your lastname.",
      email: {
        required: "Please enter your email address.",
        email: "Please enter a valid email address",
      },
      phone: {
        required: "Please enter your phone number.",
      },
    },
  });

  $("#editprofileForm").validate({
    rules: {
      fname: "required",
      lanme: "required",
      email_addr: {
        emailChecker: true,
      },
      pw1: {
        minlength: 5,
      },
      pw2: {
        minlength: 5,
        equalTo: "#editprofileForm #pass1",
      },
    },
    messages: {
      fname: "Please enter your firstname.",
      lname: "Please enter your lastname.",
      pw1: {
        minlength: "Your password must be at lesat 5 charcters long",
      },
      pw2: {
        minlength: "Your password must be at lesat 5 charcters long",
        equalTo: "Please enter the same password as above",
      },
    },
  });

  setTimeout(function () {
    $("#tnx_a").modal("show");
  }, 1000);
});

$(document).ready(function () {
  if ($("#enddate_counter").length) {
    const offsets = [
      480,
      420,
      480,
      420,
      480,
      420,
      480,
      420,
      480,
      420,
      480,
      420,
      480,
      420,
      480,
      420,
      480,
      420,
      480,
      420,
      480,
      420,
      480,
    ];
    const untils = [
      1394359200000,
      1414918800000,
      1425808800000,
      1446368400000,
      1457863200000,
      1478422800000,
      1489312800000,
      1509872400000,
      1520762400000,
      1541322000000,
      1552212000000,
      1572771600000,
      1583661600000,
      1604221200000,
      1615716000000,
      1636275600000,
      1647165600000,
      1667725200000,
      1678615200000,
      1699174800000,
      1710064800000,
      1730624400000,
      null,
    ];
    const now = +new Date();

    var pos = false;
    for (var i = 0; i < untils.length; i++) {
      if (now > untils[i]) {
        continue;
      } else {
        pos = i;
        break;
      }
    }

    if (!pos) {
      throw new Error("Failed to get tz offset");
    }

    offset = offsets[i] * 60 * 1000;

    var ts = $("#enddate_counter").data("enddate") * 1000;
    var date = new Date(ts + offset);

    $("#enddate_counter")
      .countdown(date, function (event) {
        $(this).html(
          event.strftime(
            "Fundraiser ends in: " +
              "<div>" +
              "<span>%I<small>hours</small></span>" +
              "<span>:</span>" +
              "<span>%M<small>minutes</small></span>" +
              "<span>:</span>" +
              "<span>%S<small>seconds</small></span>" +
              "</div>"
          )
        );
      })
      .on("finish.countdown", function (event) {
        $(this).html("Campaign Ended");
      });
  }
});

$(document).ready(function () {
  function resizeName() {
    if (
      $(".participant_landing_page_desktop .name").length &&
      window.matchMedia("(min-width: 768px)").matches
    ) {
      var wd = 0; // limit while iteration
      var element = $(".participant_landing_page_desktop .name");

      element.css("font-size", "75px");

      var height = parseInt(element.css("height"));
      var lineHeight = parseInt(element.css("line-height"));
      var fontSize = parseInt(element.css("font-size"));

      while (height > lineHeight * 2 && wd < 100) {
        fontSize = fontSize - 1;
        element.css("font-size", fontSize + "px");
        height = parseInt(element.css("height"));
        wd++;
      }
    }
  }

  setTimeout(resizeName, 250);
  $(window).resize(resizeName);
});
