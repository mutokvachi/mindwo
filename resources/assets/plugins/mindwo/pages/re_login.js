var reLogin = window.reLogin = {
    ajax_obj: null,
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

        var clearFields = function() {
            user_name.val("");
            pasw.val("");
        };
        
        request.callback = function (result) {
            notify_info(Lang.get('relogin_form.relogin_ok'));
            $("#popup_authorization").modal("hide");
            $('#popup_authorization').off('shown.bs.modal', reLogin.focusName);
            reLogin.updateToken(result.token);
            
            clearFields();
            
            reLogin.reExecute();
        };

        request.err_callback = function () {
            clearFields();
        };

        // izpildam AJAX pieprasījumu
        request.doRequest();
    },
    
    openForm: function() {
        hide_page_splash(1);
        
        $('#popup_authorization').on('shown.bs.modal', reLogin.focusName);
        
        $('#popup_authorization').modal({
                keyboard: false,
                backdrop: 'static'
        });
    },
    
    focusName: function() {
        $("#reLoginForm").find("input[name='user_name']").focus();
    },
    
    reExecute: function() {
        show_page_splash(1);
        
        $.ajax({
            type: reLogin.ajax_obj.type,
            url: reLogin.ajax_obj.url,
            data: reLogin.ajax_obj.data,
            processData: reLogin.ajax_obj.processData,
            contentType: reLogin.ajax_obj.contentType,
            dataType: reLogin.ajax_obj.dataType,
            async: reLogin.ajax_obj.async,
            success: function(event, xhr, settings){                
                reLogin.ajax_obj.success(event, xhr, settings);
            },
            beforeSend: reLogin.ajax_obj.beforeSend,
            complete: function(event, xhr, settings){
                hide_page_splash(1);
                reLogin.ajax_obj.complete(event, xhr, settings);            
            },
            error: function(event, xhr, settings, err) {
                reLogin.ajax_obj.error(event, xhr, settings, err);
            }
        });
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
    
})();