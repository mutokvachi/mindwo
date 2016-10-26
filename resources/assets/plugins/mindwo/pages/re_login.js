var reLogin = window.reLogin = {
    auth_popup: $("#popup_authorization"),
    authorize: function () {
        var formData = new FormData();
        var pass_form = $("#reLoginForm");
        var user_name = pass_form.find("input[name='user_name']");
        var pasw = pass_form.find("input[name='password']");

        formData.append("user_name", user_name.val());
        formData.append("password", pasw.val());

        var ajax_url = 'relogin';
        var request = new FormAjaxRequest(ajax_url, "", "", formData);

        request.callback = function (result) {
            var successMsg = reLogin.auth_popup.attr('data-success');
            notify_info(successMsg);
            $("#popup_authorization").modal("hide");
            reLogin.updateToken(result.token);
        };

        request.err_callback = function (msg) {
            user_name.val("");
            pasw.val("");
        };

        // izpildam AJAX pieprasījumu
        request.doRequest();
    },
    
    updateToken: function(token) {
        $("meta[name='csrf-token']").attr('content', token);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.each($("[name='_token']"), function(){
            $(this).val(token);
        });
    }
};

(function () {
    $('#reLoginForm').validator({
        feedback: {
            success: 'glyphicon-ok',
            error: 'glyphicon-alert'
        }
    });

    $('#reLoginForm').validator().on('submit', function (e) {
        if (e.isDefaultPrevented())
        {
            $("#relogin_user_name").focus();
            return false;
        }
        else
        {
            // autorizācijas dati ir korekti ievadīti
            reLogin.authorize();
            return false;
        }
    });

    var reLoginModal = reLogin.auth_popup;
    reLoginModal.on('shown.bs.modal', function () {
        reLoginModal.find("input[name='user_name']").val("").focus();
        reLoginModal.find("input[name='password']").val("");
    });

    // Override $.ajax
    // Store a reference to the original remove method.
    var originalPostMethod = jQuery.ajax;

    // Define overriding method.
    jQuery.ajax = function (data) {
        
        // Execute the original method.
        var callMethod = originalPostMethod.apply(this, arguments);

        callMethod.error(function (result) {            
            // Check for 401 (Unautorized) status
            if (result.status === 401) {
                //console.log(result);
                reLoginModal.modal("show");
            }
        });
    };
})();