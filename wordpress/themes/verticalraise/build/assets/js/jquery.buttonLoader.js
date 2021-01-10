/*A jQuery plugin which add loading indicators into buttons
* By Minoli Perera
* MIT Licensed.
*/
jQuery(document).ready(function(){

    $('.has-spinner').attr("disabled", false);
    $.fn.buttonLoader = function (action) {
       
        var self = $(this);
        if (action == 'start') {

            if ($(self).attr("disabled") == "disabled") {
                return false;
            }
            $('.has-spinner').attr("disabled", true);
            $(self).attr('data-btn-text', $(self).text());
            var text = $(self).data('loading');
            if($(self).attr('data-load-text') != undefined && $(self).attr('data-load-text') != ""){
                var text = $(self).attr('data-load-text');
            }
            $(self).html('<span class="spinner"><i class="fa fa-spinner fa-spin" title="button-loader"></i></span> '+text);
            $(self).addClass('active');
        }
        if (action == 'stop') {
            
            $(self).html($(self).attr('data-btn-text'));
            $(self).removeClass('active');
            $('.has-spinner').attr("disabled", false);
        }
    }
});
