var FormiCheck = function () {
    return {
        //main function to initiate the module
        init: function () {         
         
            $('.icheckbox_flat').iCheck({
              checkboxClass: 'icheckbox_flat',
              radioClass: 'iradio_flat',
              increaseArea: '20%' // optional
            });                       
        }
    };
}();

jQuery(document).ready(function() {
    FormiCheck.init();
});