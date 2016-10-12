<div style="margin: 20px;">
    <p>Tiks ģenerēta programmatūras projektējuma apraksta datne PDF formātā.</p>
</div>
<div id="{{ $form_guid }}">
    <form class="form-horizontal" id='item_edit_form_{{ $form_guid }}' method='POST'>
        
        <div class="col-lg-12 text-center" id="div_btn_line">
            <button class="btn btn-primary" type="button" id='{{ $form_guid }}_gener' title="Ģenrēt PPA" style="margin-left: -5px;">Ģenerēt PPA</button>
        </div>        
    </form>    
</div>
<div class="col-lg-12 text-center" style="display: none;" id="file_gen_progress">
    <img src="assets/global/img/input-spinner.gif" alt="Notiek ģenerēšana" /> Tiek ģenerēta PPA datne... Lūdzu, uzgaidiet... Process var notikt vairākas minūtes.
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
            notify_info("PPA datne veiksmīgi noģenerēta!");
            $("#file_gen_progress").html("<b>PPA datne veiksmīgi noģenerēta!</b><br><br><a href='/assets/global/doc/ppa.pdf' target='_blank'>Lejuplādēt PPA</a>")
        };

        form_el.hide();
        $("#file_gen_progress").show();
        
        // izpildam AJAX pieprasījumu
        request.doRequest();
    }
    
</script>