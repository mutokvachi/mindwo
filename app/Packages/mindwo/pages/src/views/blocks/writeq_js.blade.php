<script type='text/javascript'>
    function is_email (email) 
    {
        var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }
    
    $('#email_btn_{{ $block_guid }}').click(function(e) {
        $('#popup_email_{{ $block_guid }}').modal('hide');
        
        var quest = $("#writeq_{{ $block_guid }}").find("[name=askQuestion]").val();
        save_question(quest, $('#popup_email_{{ $block_guid }}').find("input[name=q_email]").val());
    });
                        
    $("#writeq_{{ $block_guid }}").find("button").click(function(){
        var quest = $("#writeq_{{ $block_guid }}").find("[name=askQuestion]").val();
        
        if (quest.length == 0)
        {
            notify_err(Lang.get('writeq.provide_question'));
            $("#writeq_{{ $block_guid }}").find("[name=askQuestion]").focus();
            return;
        }
        
        if ($("#writeq_{{ $block_guid }}").find("input[type=checkbox]").is(':checked'))
        {
            $('#popup_email_{{ $block_guid }}').on('shown.bs.modal', function () {			
                $('#popup_email_{{ $block_guid }}').find("input[name=q_email]").focus();                          	
            });
            
            $('#popup_email_{{ $block_guid }}').validator({
                custom : {
                    foo: function($el) 
                    {                        
                        if (!is_email($el.val()))
                        {
                            return false;
                        }
                        return true;
                    }
                },
                errors: {
                    foo: Lang.get('writeq.wrong_email'),
                    auto: Lang.get('writeq.value_not_set')
                },
                feedback: {
                    success: 'glyphicon-ok',
                    error: 'glyphicon-alert'
                }
            });
            $('#popup_email_{{ $block_guid }}').modal('show');
            
            return;
        }        
        
       
        save_question(quest, '');
    });
    
    function save_question(question, email)
    {
         $('#popup_window .modal-dialog').removeClass('modal-lg').addClass('modal-md');
        
        $('#popup_window').on('hidden.bs.modal', function (e) {			
            $('#popup_window .modal-dialog').removeClass('modal-md').addClass('modal-lg');                             	
        });
        
        $('#popup_window .modal-title').html(Lang.get('writeq.form_title'));
        $("#popup_body").html('<div style="padding: 10px;">' + getProgressInfo() + '</div>');
        $('#popup_window').modal('show');
        
        var formData = "param=" + encodeURIComponent("OBJ=WRITEQ|SOURCE={{ $source_id}}") + "&question=" + encodeURIComponent(question) + "&email=" + email;
        
        var ajax_url = 'block_ajax';

        //$("#load_more_link").text("IELĀDĒ...");

        var request = new FormAjaxRequestIE9 (ajax_url, "", "", formData);            
        request.progress_info = "";                       

        request.callback = function(data) {
            
            $('#popup_window .modal-title').html(Lang.get('writeq.sent_ok'));
            $("#popup_body").html('<div style="padding: 10px;">' + Lang.get('writeq.sent_msg') + '</div>');
            $("#writeq_{{ $block_guid }}").find("[name=askQuestion]").val('');           

        };
        
        request.err_callback = function(err_txt) {
            $('#popup_window .modal-title').html(Lang.get('writeq.err_title'));
            $("#popup_body").html('<div style="padding: 10px;"><font color="red">' + err_txt + '</font></div>');
        };

        // izpildam AJAX pieprasījumu
        request.doRequest();
    }
    
</script>