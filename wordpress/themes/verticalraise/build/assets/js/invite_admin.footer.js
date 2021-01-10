var EMAILS_ARRAY;
var INVALID_EMAILS;

$(document).ready(function() {
    $('#InviteAdminForm textarea[name=sadmin_email]').focus(function() {
        $('#email_check_status').val('');
        $("#inviteSentBtn").buttonLoader('stop');
        $('#InviteAdminForm .suggestions').hide();
        $('#InviteAdminForm .invalidemails').hide();
    });
    $('#InviteAdminForm textarea[name=sadmin_email]').on('blur', function(event) {
        //email_verification($(this));

        $('#InviteAdminForm .suggestions').hide();
        $('#InviteAdminForm .invalidemails').hide();
        $('#emailfields input').remove();
        $('#InviteAdminForm .suggestions ul').children().remove();
        $('#InviteAdminForm .invalidemails ul').children().remove();
        var textVal = $(this).val();
        EMAILS_ARRAY = textVal.split(/[;, \r\n]+/);
        INVALID_EMAILS = [];
        EMAILS_ARRAY = EMAILS_ARRAY.filter(function(entry) {
            return /\S/.test(entry);
        });

        for (var i = 0; i < EMAILS_ARRAY.length; i++) {
            if (EMAILS_ARRAY[i] != '') {
                $("#emailfields").append('<input type="text" name="input_email[]" value="' + EMAILS_ARRAY[i] + '" class="form-control ip" />');
            }
        }

        $("#emailfields input").each(function(index) {
            email_checker($(this), index);
            //email_verification($(this), index); ///
        });

    });

    $('#InviteAdminForm .suggestions').on('click', 'span', function() {
        var index = $(this).data("arrayindex");
        EMAILS_ARRAY[index] = $(this).text();
        $(this).parent().remove();

        $("#emailfields input:eq(" + index + ")").val($(this).text());
        $('#InviteAdminForm textarea[name=sadmin_email]').val(EMAILS_ARRAY.join());
        $(".invalidemails ul").find('li[invalidindex="' + index + '"]').remove();
        //email_verification($("#emailfields input:eq(" + index + ")"), index);

        if ($('#InviteAdminForm .suggestions ul').children("li").length < 1) {
            $('#InviteAdminForm .suggestions').fadeOut();
        }
        /*if ($('#InviteAdminForm .invalidemails ul').children("li").length < 1) {
            $('#InviteAdminForm .invalidemails').fadeOut();
        }*/

    });
});

function email_checker(emailObj, index) {

    var FirstDomains = ["gmail.com", "yahoo.com", "verizon.net"];
    var SecondDomains = ["com", "net", "org", "co.nz", "co.uk", "co.il", "com.au", "com.tw", "net.au"];
    emailObj.mailcheck({
        //        topLevelDomains: topLevelDomains,
        suggested: function(element, suggestion) {
            var splitDomain = suggestion.domain.split(".");
            switch (splitDomain.length) {
                case 2:
                    splitDomain = splitDomain[1];
                    break;
                case 3:
                    splitDomain = splitDomain[1] + "." + splitDomain[2];
                    break;
                default:
                    splitDomain = splitDomain;
                    break;
            }
            if (FirstDomains.indexOf(suggestion.domain) > -1) {
                EMAILS_ARRAY[index] = suggestion.full;
                $("#emailfields input:eq(" + index + ")").val(suggestion.full);
            } else if (splitDomain != emailObj.val().split(".")[1] && SecondDomains.indexOf(splitDomain) > -1) {
                EMAILS_ARRAY[index] = suggestion.full;
                $("#emailfields input:eq(" + index + ")").val(suggestion.full);
            } else {
                $('#InviteAdminForm .suggestions').fadeIn(200);
                $('#InviteAdminForm .suggestions ul').append("<li><span data-arrayindex='" + index + "'>" + suggestion.full + "</span></li>");
            }
        },
        empty: function(element) {
            // $(".suggestions").empty();
        }
    });
    if (EMAILS_ARRAY.length == index + 1) {
        $('#InviteAdminForm textarea[name=sadmin_email]').val(EMAILS_ARRAY.join());
        $('#email_check_status').val(1);
    }
}

/*function email_verification(emailObj, index) {

    var email = emailObj.val();
    $.get("https://api.thechecker.co/v1/verify?email=" + email + "&api_key=" + THE_CHECKER_API_KEY, function(data, status) {
        if (status != 'success') {
            return false;
        } else {
            var allowed_result = ['deliverable', 'risky', 'unknown'];
            if (allowed_result.indexOf(data.result) > -1) {

            } else {
                INVALID_EMAILS.push(email);
                $('#InviteAdminForm .invalidemails').fadeIn(200);
                $('#InviteAdminForm .invalidemails ul').append("<li invalidindex='" + index + "'><span >" + email + "</span></li>");
            }

            if (EMAILS_ARRAY.length == index + 1 && INVALID_EMAILS.length == 0) {
                $('#email_check_status').val(1);
                //console.log(INVALID_EMAILS);
            }
        }
    });
}*/