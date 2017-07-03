<div style="margin: 20px;">
    <p>{{ trans('ppa.form.intro') }}</p>
</div>
<div id="{{ $form_guid }}">
    <form class="form-horizontal" id='item_edit_form_{{ $form_guid }}' method='POST'>
        
        <div class="col-lg-12 text-center" id="div_btn_line">
            <button class="btn btn-primary" type="button" id='{{ $form_guid }}_gener' style="margin-left: -5px;">{{ trans('ppa.form.btn_generate') }}</button>
        </div>        
    </form>    
</div>
<div class="col-lg-12 text-center" style="display: none;" id="file_gen_progress">
    <img src="assets/global/img/input-spinner.gif" alt="{{ trans('ppa.form.progress') }}" /> {{ trans('ppa.form.progress_info') }}
</div>
<script>
                        
    $("#{{ $form_guid }}_gener").click(function(event) {
        event.stopPropagation();
        generatePPA();
    });
    
    function generatePPA()
    {   
        var form_el = $("#{{ $form_guid }}");        
        
        var formData = new FormData();
        var request = new FormAjaxRequest ("/structure/method/ppa_pdf", "", "", formData);
        request.progress_info = "";
        
        request.callback = function(data) {
            notify_info(Lang.get('ppa.form.done'));
            $("#file_gen_progress").html("<b>" + Lang.get('ppa.form.done') + "</b><br><br><a href='/assets/global/doc/ppa.pdf' target='_blank'>" + Lang.get('ppa.form.link_download') + "</a>")
        };

        form_el.hide();
        $("#file_gen_progress").show();
        
        // izpildam AJAX pieprasījumu
        request.doRequest();
    }
    
</script>