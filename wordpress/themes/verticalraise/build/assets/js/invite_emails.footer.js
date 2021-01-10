


$(document).ready(function() {
    $("#invite_step form").submit(function(event) {

        // console.log("ASDFASDFASDf");
        // event.preventDefault();
        if ($('input[name="your_name"]').val() == '' || $('input[name="your_name"]').val() == 'Your Name') {
            //alert('"Your Name" is required in the from box');
            event.preventDefault();
        } else {
            var btn = $("#inviteSentBtn");
            $(btn).buttonLoader('start');
        }
    });
});

jQuery(document).ready(function() {
    jQuery('span.deleteicon').click(function() {
        jQuery(this).next('input').val('').focus();
    });
    addmoreInit();
});

function addmoreInit() {
    jQuery('span.addicon').click(function() {
        //alert('add');
        jQuery(this).hide();
        jQuery(this).next('.crossicon').show();
        jQuery(this).parent('li').after('<li><span class="addicon"></span><span class="crossicon" style="display: none;"></span><input type="email" name="inviteemail[]" value=""></li>');
        addmoreInit();
    });
    jQuery('span.crossicon').click(function() {
        //alert('remove');
        jQuery(this).parent('li').remove();
    });
}
var checkEmpty;

function singleentry() {
    jQuery('.import_mail_sec').hide();
    jQuery('.normal_entry').hide();
    jQuery('.single_entry').show();
    jQuery('input[name="invite_submit"]').hide();
    jQuery('.text-email').attr('required');
    jQuery('.text-area-emails').removeAttr('required');
    checkEmpty = setInterval(function() {
        var isValid;
        var i = 0;
        jQuery('input[name="inviteemail[]"]').each(function() {
            var element = jQuery(this);
            if (element.val() != "" && i == 0) {
                isValid = false;
            }
            i++;
        });
        if (isValid == false && i > 1) {
            jQuery('input[name="invite_submit"]').show();
        } else {
            jQuery('input[name="invite_submit"]').hide();
        }
    }, 2000);
}

function multipleentry() {
    jQuery('.import_mail_sec').show();
    jQuery('input[name="invite_submit"]').show();
    jQuery('.normal_entry').show();
    jQuery('.single_entry').hide();
    clearInterval(checkEmpty);
    jQuery('.text-email').removeAttr('required');
    jQuery('.text-area-emails').attr('required');
}

jQuery('.invite_next a, .invite_prev a').click(function(e) {
    var i = 0;
    jQuery('input[name^="inviteemail"]').each(function() {
        var valid;
        valid = jQuery(this).val();
        if (valid != '') {
            i++;
        }
    });
    if (jQuery('textarea[name="emails"]').val() != '') {
        i++;
    }
    if (i > 0) {
        e.preventDefault();
        alert('You must click the "SEND" button to send the invites out before you can go to the next page.');
    }
});

setTimeout(function () {
   window.top.location.href= 'https://www.verticalraise.com'; // the redirect goes here

},10800000); // Redirect after 3 hours of tab being opened