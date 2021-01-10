$(document).ready(function () {
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('header').addClass("stick");
            $(".sec_header.landing").addClass("stick");
            $(".sec_header.landing1").addClass("stick");
        } else {
            $('header').removeClass("stick");
            $(".sec_header.landing").removeClass("stick");
            $(".sec_header.landing1").removeClass("stick");
        }
    });

    //Toggle Menu
    $("#nav-icon2").click(function () {
        $(this).toggleClass("open");
        $(".right_header").fadeToggle();
    });

    $(".right_header>ul>li.menu-item-has-children").append("<span class='toggle_dropdown'></span>");

    $(".right_header>ul>li.menu-item-has-children .toggle_dropdown").click(function () {
        $(this).parent(".menu-item-has-children").find(".mega_menu").slideToggle();
        $(this).parent(".menu-item-has-children").toggleClass("show");
        $(this).parent(".menu-item-has-children").siblings('.menu-item-has-children').find('.mega_menu').slideUp();
        $(this).parent(".menu-item-has-children").siblings('.menu-item-has-children').removeClass("show");
    });

});

$(window).load(function () {
    //question form validation
    $("#question").validate({
        rules: {
            fullname: "required",
            email: {
                required: true,
                email: true
            },
            phone: {
                required: true
            }
        },
        messages: {
            fullname: "Please enter your name.",
            email: {
                required: "Please enter your email address.",
                email: "Please enter a valid email address"
            },
            phone: {
                required: "Please enter your phone number."
            }
        }
    });
});


//function add_placeholder(inputObj) {
//    var placeholder = inputObj.attr('placeholder');
//    convert_attr(inputObj)
//
//    inputObj.val(placeholder);
//    inputObj.removeClass("focus").addClass("focus");
//    inputObj[0].setSelectionRange(0, 0);
//    inputObj.css('color','#727272')
//}
//
//
//function reset_attr(inputObj) {
//    if (inputObj.attr("type") == "password1") {
//        inputObj.attr("type", "password")
//    }
//    if (inputObj.attr("type") == "email1") {
//        inputObj.attr("type", "email")
//    }
//}
//
//function convert_attr(inputObj) {
//    if (inputObj.attr("type") == "password") {
//        inputObj.attr("type", "password1")
//    }
//    if (inputObj.attr("type") == "email") {
//        inputObj.attr("type", "email1")
//    }
//}