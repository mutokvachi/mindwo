<div style="margin: 20px;">
    <p>Dzēšot reģistra lauku, tiks automātiski dzēsti arī skata un formas lauki.</p>
</div>
<div id="{{ $form_guid }}" style="margin: 20px;">
    <form class="form-horizontal" id='item_edit_form_{{ $form_guid }}' method='POST' data-toggle="validator">
        <div>
            <div class='form-group has-feedback'>
                <label class='col-lg-4 control-label'>Lauka nosaukums <span style="color: red"> *</span></label>
                <div class='col-lg-8'>
                    <select class='form-control' name = 'field_id' required data-foo="bar">
                        <option value="0" selected></option>
                        @foreach($fields as $item)
                            <option value='{{ $item->id }}'>{{ $item->title_list }}</option>
                        @endforeach
                </select>
                <span class="glyphicon form-control-feedback" aria-hidden="true" style="margin-right: 10px;"></span>     
                    <div class="help-block with-errors"></div>
                </div>    
            </div>
        </div>
        <div>
            <div class="col-lg-4">
            </div>
            <div class="col-lg-8">
                <button class="btn btn-primary" type="button" id='{{ $form_guid }}_delete' title="Dzēst skatu" style="margin-left: -5px;">Dzēst</button>
            </div>
        </div>
    </form>
</div>
<script>
    
    $('#item_edit_form_{{ $form_guid }}').validator({
        custom : {
            foo: function($el) 
            { 
                if (!($el.val()>0) && $el.attr('required'))
                {
                    return false;
                }
                return true;
            }
        },
        errors: {
            foo: 'Nav norādīta vērtība!'
        },
        feedback: {
            success: 'glyphicon-ok',
            error: 'glyphicon-alert'
        }
    });
                        
    $("#{{ $form_guid }}_delete").click(function(event) {
        event.stopPropagation();
        $('#item_edit_form_{{ $form_guid }}').validator('validate');
        
        deleteView();
    });
    
    function deleteView()
    {   
        var form_el = $("#{{ $form_guid }}");
        var field_id = form_el.find("select[name=field_id]").val();
       
        var formData = new FormData();
        formData.append("field_id", field_id);
        
        var grid_htm_id = $("#" + get_last_form_id()).find("div[dx_list_id={{ $fields_list_id }}]").attr("dx_grid_id");
        
        var request = new FormAjaxRequest ("/structure/method/field_delete", "", grid_htm_id, formData);

        request.callback = function(data) {
            
            if (grid_htm_id)
            {
                reload_grid(grid_htm_id);
            }            
            form_el.find("select[name=field_id] option[value='" + field_id + "']").remove();
            notify_info("Lauks veiksmīgi dzēsts!");
        };

        // izpildam AJAX pieprasījumu
        request.doRequest();
    }
    
    setTimeout(function() {
        $("#{{ $form_guid }}").find("select[name=field_id]").focus();
    }, 500);
    
</script>