<div style="margin: 20px;">
    <p>Tiks dzēstas datnes no servera kataloga <i>public/img</i>.</p>
    <p>Tiks neatgriezeniski dzēstas tās datnes, kurām nav neviena atbilstoša ieraksta datu bāzē.</p>
    <p>Ja izvēlēts veidots kopiju, tad dati tiks saglabāti servera katalogā <i>storage/logs/{yyyy_mm_dd_HH_mm_ss}</i></p>
</div>
<div id="{{ $form_guid }}" style="margin: 20px;">
    <form class="form-horizontal" id='item_edit_form_{{ $form_guid }}' method='POST'>
        <div class='form-group'>
                <label class='col-lg-6 control-label'>Izveidot dzēsto datņu kopiju:</label>
                <div class='col-lg-6' style="margin-top: 7px;">
                    <input type="radio" checked="'checked'" name="is_backup" value="1"> Jā
                    <input type="radio" name="is_backup" value="0"> Nē
                </div>    
            </div>
        <div class="col-lg-12 text-center" id="div_btn_line">
            <button class="btn btn-primary" type="button" id='{{ $form_guid }}_delete' title="Dzēst datnes" style="margin-left: -5px;">Dzēst datnes</button>
        </div>        
    </form>    
</div>
<div class="col-lg-12 text-center" style="display: none;" id="file_del_progress">
    <img src="assets/global/img/input-spinner.gif" alt="Notiek dzēšana" /> Tiek dzēstas datnes... Lūdzu, uzgaidiet... Process var notikt vairākas minūtes.
</div>
<script>
                        
    $("#{{ $form_guid }}_delete").click(function(event) {
        event.stopPropagation();
        deleteFiles();
    });
    
    function deleteFiles()
    {   
        var form_el = $("#{{ $form_guid }}");        
        
        var formData = new FormData();
        
        form_el.find(':checkbox:checked, :radio:checked').each(function (key, obj) {
            formData.append(obj.name, obj.value);
        });
        
        var request = new FormAjaxRequest ("/structure/method/delete_unneded_files", "", "", formData);

        request.callback = function(data) {
            notify_info("Datnes veiksmīgi dzēstas!");
            $("#file_del_progress").html("<b>Datnes veiksmīgi dzēstas!</b>")
        };

        form_el.hide();
        $("#file_del_progress").show();
        
        // izpildam AJAX pieprasījumu
        request.doRequest();
    }
    
</script>