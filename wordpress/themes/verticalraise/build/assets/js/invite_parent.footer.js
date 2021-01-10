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
jQuery(document).ready(function() {
    addMask();
})
function addMask() {
    jQuery.mask.definitions['~'] = "[+-]";
    jQuery(".phone").mask("(999) 999-9999");
}
function addmoreInit() {
    jQuery('span.addicon').click(function() {
        if(jQuery('input[name="invitesms[]"]').val() == '' || jQuery('input[name="invitesms[]"]').val().length < 10 ){
            alert('Please enter a 10 digit telephone number including area code first.');
            return false;
        }
        //alert('add');
        jQuery(this).hide();
        jQuery(this).next('.crossicon').show();
        jQuery(this).parent('div').after(
            '<div class="parent_nums">' +
            '<span class="addicon"><i class="fa fa-plus"></i></span>' +
            '<span class="crossicon" style="display: none;"><i class="fa fa-remove"></i></span>' +
            '<input type="text"  name="invitesms[]" value=""  class="phone form-control ip" placeholder="(###) ###-####">' +
            '</div>');
        addMask();
        addmoreInit();
    });
    jQuery('span.crossicon').click(function($) {
        //alert('remove');
        jQuery(this).parent('div').remove();
    });
}
setInterval(function() {

    var isValid = true;
    var i = 0;
    $('input[name="invitesms[]"]').each(function() {
        var element = $(this);
        if (element.val().length >=10 && i == 0) {
            isValid = false;
        }
        i++;
    });
    if(isValid == false && i > 0) {
        $('input[name=invite_submit]').show();
    } else {
        $('input[name=invite_submit]').hide();
    }
},1000);