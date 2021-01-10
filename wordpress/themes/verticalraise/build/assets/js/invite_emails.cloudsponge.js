jQuery(document).ready(function() {
    jQuery('input[name="your_name"]').focusout(function() {
        var your_name = jQuery(this).val();
        jQuery.ajax({
            url: "/user-session",
            data: {
                "your_name": your_name
            },
            type: "POST",
            success: function(result) {}
        });
    });
});
// extra widget options go here:
cloudsponge.init({
    browserContactCacheMin: 15,
    sources: ["gmail", "yahoo", "windowslive", "aol", "icloud", "plaxo", "mail_ru", "uol", "bol", "terra", "rediff", "mail126", "mail163", "mail_yeah_net", "gmx", "web_de", "qip_ru", "sapo", "mailcom", "yandex_ru", "addressbook", "office365", "qq_mail", "poczta_o2", "naver"],
    /*skipSourceMenu: true,*/
    afterLaunch: function() {},
    afterSubmitContacts: function(contacts, source, owner) {
        jQuery.ajax({
            url: "/user-session",
            data: {
                "get_your_name": 1
            },
            type: "POST",
            success: function(result) {
                jQuery('input[name="your_name"]').val(result);
            }
        });
        var textarea = jQuery('textarea[name="emails"]');
        contacts.forEach(function(item) {
            if (textarea.val() == '') {
                textarea.val(textarea.val() + item.primaryEmail());
            } else {
                textarea.val(textarea.val() + ', ' + item.primaryEmail());
            }
        });
        textarea.trigger('focus');
    },
});
jQuery(document).ready(function() {
    jQuery('textarea[name="emails"]').focus();
});