<div style="margin: 20px;">
    <p>Ierakstu izmaiņu auditācijas funkcionalitātes iestatīšana.</p>
    <p>Auditācija netiks iestatīta sekojošām tabulām:<br /><br /><b>{{ $ignore_tables }}</b></p>
    <p>Auditējamām tabulām SVS tiks iestatīta pazīme veikt auditāciju un nepieciešamības gadījumā tiks izveidoti sekojosi lauki:<br /><br /><b>created_user_id, created_time, modified_user_id un modified_time</b>
</div>
<div id="{{ $form_guid }}">
    <form class="form-horizontal" id='item_edit_form_{{ $form_guid }}' method='POST'> 
            <div class="col-lg-12 text-center">
                <button class="btn btn-primary" type="button" id='{{ $form_guid }}_audit' title="Iestatīt auditāciju" style="margin-left: -5px;">Iestatīt auditāciju</button>
            </div>
    </form>
</div>
<div class="col-lg-12 text-center" style="display: none;" id="file_del_progress">
    <img src="assets/global/img/input-spinner.gif" alt="Notiek dzēšana" /> Tiek iestatīta auditācija... Lūdzu, uzgaidiet...
</div>
<script>
    $("#{{ $form_guid }}_audit").click(function(event) {
        event.stopPropagation();
        
        generateAudit();
    });
    
    function generateAudit()
    {        
        var form_el = $("#{{ $form_guid }}");
        
        var formData = new FormData();
        var request = new FormAjaxRequest ("/structure/method/generate_audit", "", "", formData);

        request.callback = function(data) {            
            notify_info("Auditācija veiksmīgi iestatīta!");
            $("#file_del_progress").html("<b>Auditācija veiksmīgi iestatīta!</b>")
        };

        form_el.hide();
        $("#file_del_progress").show();
        
        // izpildam AJAX pieprasījumu
        request.doRequest();
    }    
</script>