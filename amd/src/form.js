define(['jquery'], function($) {
    return {
        init: function() {
            var pwd = $('input[name="password"]');
            var reqpwd = $('input[name="requirepassword"][type!="hidden"]');
            $(document).ready(function() {
                if (!reqpwd.is(':checked')) {
                    pwd.val('');
                }
            });
            reqpwd.change(function() {
                if (pwd.attr('disabled') == 'disabled') {
                    pwd.val('');
                } else {
                    // Set value to be a new random 6 digit number
                    pwd.val(Math.floor(Math.random() * (999999 - 100000) + 100000).toString());
                }
            });
        }
    };
});
