<div style="margin: 20px;">
    <p>Kopējot reģistru, tiks automātiski kopēti visi reģistra lauki, skati un datu ievades formas.</p>
    <p>Jāņem vērā sekojoši nosacījumi/ierobežojumi:</p>
    <ul>
        <li>Netiks kopēta navigācija, sasaiste ar lomām un darbplūsmas;</li>
        <li>Tiks kopētas visas sasaistes ar klasifikatoriem;</li>
        <li>Ja ir piesaistīts reģistrācijas numuru numerators, arī tas saglabāsies. Tāpēc, ja nepieciešams, pēc ģenerācijas ir jākonfigurē atsevišķs numerators.</li>
    </ul>
</div>
<div id="{{ $form_guid }}">
    <form class="form-horizontal" id='item_edit_form_{{ $form_guid }}' method='POST' data-toggle="validator">
        <div>
            <div class='form-group has-feedback'>
                <label class='col-lg-4 control-label'>Kopējamā reģistra nosaukums <span style="color: red"> *</span></label>
                <div class='col-lg-8'>
                    <select class='form-control' name = 'list_id' required data-foo="bar">
                        <option value="0" selected></option>
                        @foreach($lists as $item)
                            <option value='{{ $item->id }}'>{{ $item->list_title }}</option>
                        @endforeach
                </select>
                <span class="glyphicon form-control-feedback" aria-hidden="true" style="margin-right: 10px;"></span>     
                    <div class="help-block with-errors"></div>
                </div>    
            </div>
        </div>
        <div>
            @include('fields.visible', ['fld_name' => '', 'group_label' => '', 'label_title' => 'Jaunā reģistra nosaukums', 'is_required' => 1, 'hint' => 'Nosaukums tiks parādīts virs tabulārā saraksta. Jāraksta daudzskaitlī.', 'item_htm' => '<input class="form-control" type=text name = "register_title"  maxlength="500" value = "" required /><span class="glyphicon form-control-feedback" aria-hidden="true"></span>'])
        </div>
        <div>
            <div class="col-lg-4">
            </div>
            <div class="col-lg-8">
                <button class="btn btn-primary" type="button" id='{{ $form_guid }}_copy' title="Kopēt reģistru" style="margin-left: -5px;">Kopēt</button>
            </div>
        </div>
        <input type=hidden name='obj_id' value='{{ $obj_id }}'>
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
    
    $("#{{ $form_guid }}_copy").click(function(event) {
        event.stopPropagation();
        $('#item_edit_form_{{ $form_guid }}').validator('validate');
        
        copyRegister();
    });
    
    function copyRegister()
    {   
        var form_el = $("#{{ $form_guid }}");
        
        var obj_id = form_el.find("input[name=obj_id]").val();
        var register_title = form_el.find("input[name=register_title]").val();
        var list_id = form_el.find("select[name=list_id]").val();

        var formData = new FormData();
        formData.append("obj_id", obj_id);
        formData.append("register_title", register_title);
        formData.append("list_id", list_id);
        
        var grid_htm_id = $("#" + get_last_form_id()).find(".tab-pane .table-responsive table").attr("id");
        
        var request = new FormAjaxRequest ("/structure/method/register_copy", "", grid_htm_id, formData);

        request.callback = function(data) {
            
            if (grid_htm_id)
            {
                reload_grid(grid_htm_id);
            }            

            notify_info("Reģistrs veiksmīgi nokopēts!");
        };

        // izpildam AJAX pieprasījumu
        request.doRequest();
    }
    
    setTimeout(function() {
        $("#{{ $form_guid }}").find("select[name=list_id]").focus();
    }, 500);
    
</script>