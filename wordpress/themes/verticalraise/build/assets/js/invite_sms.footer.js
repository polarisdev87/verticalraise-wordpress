jQuery(document).ready(function () {
    $("#invite_step form").submit(function(event) {
        if(jQuery('input[name="your_name"]').val() == '' || jQuery('input[name="your_name"]').val() == 'Your Name'){
            alert('"Your Name" is required in the from box');
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
        jQuery(this).parent('li').after('<li><span class="addicon"></span><span class="crossicon" style="display: none;"></span><input type="text" name="invitesms[]" value=""></li>');
        addmoreInit();
    });
    jQuery('span.crossicon').click(function() {
        //alert('remove');
        jQuery(this).parent('li').remove();
    });
}
var checkEmpty;
function singleentry() {
    jQuery('.normal_entry').hide();
    jQuery('.single_entry').show();
    jQuery('input[name="invite_submit1"]').hide();
    checkEmpty = setInterval(function() {
        var isValid;
        var i = 0;
        jQuery('input[name="invitesms[]"]').each(function() {
            var element = jQuery(this);
            if (element.val() != "" && i == 0) {
                isValid = false;
            }
            i++;
        });
        if(isValid == false && i > 1) {
            jQuery('input[name="invite_submit1"]').show();
        } else {
            jQuery('input[name="invite_submit1"]').hide();
        }
    }, 2000);
}
function multipleentry() {
    jQuery('input[name="invite_submit"]').show();
    jQuery('.normal_entry').show();
    jQuery('.single_entry').hide();
    clearInterval(checkEmpty);
}

setTimeout(function () {
   window.top.location.href= 'https://verticalraise.com'; // the redirect goes here

},10800000); // Redirect after 3 hours of tab being opened