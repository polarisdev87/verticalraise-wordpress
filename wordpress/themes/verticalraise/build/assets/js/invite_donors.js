$(document).ready(function () {
    $(".invite_donors").on("click", function () {
        /*
         invite wizad start
         */

        $("#invite_step_start").modal('show');
    });


    $(".submit_btn.btn.nav").on("click", function () {
        var current_id = $(this).data('current');
        var act_id = $(this).data('act');

        $("#" + current_id).modal('hide');

        setTimeout(function () {
            $("#" + act_id).modal('show');
        }, 500);

    });
    
    $("#inviteDonorFinish").on("click", function () {
        $("#invite_step_facebook").modal('hide');
    })


});
