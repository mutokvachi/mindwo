<div style="margin: 20px;">
    <p>Kopējot skatu, tiks automātiski kopēti visi skata lauki un skatā iekļautās kolonnas.</p>
    <p>Jāņem vērā sekojoši nosacījumi/ierobežojumi:</p>
    <ul>
        <li>Netiks kopēts lauks URL, jo tam katram skatam ir jābūt unikālam;</li>
    </ul>
</div>
<div id="{{ $form_guid }}" style="margin: 20px;">
    <form class="form-horizontal" id='item_edit_form_{{ $form_guid }}' method='POST' data-toggle="validator">        
        <div>
            <div class='form-group has-feedback'>
                <label class='col-lg-4 control-label'>Kopējamais skats <span style="color: red"> *</span></label>
                <div class='col-lg-8'>
                    <select class='form-control' name = 'view_id' required data-foo="bar">
                        <option value="0" selected></option>
                        @foreach($views as $item)
                            <option value='{{ $item->id }}'>{{ $item->title }}</option>
                        @endforeach
                </select>
                <span class="glyphicon form-control-feedback" aria-hidden="true" style="margin-right: 10px;"></span>     
                    <div class="help-block with-errors"></div>
                </div>    
            </div>
        </div>
        <div>
            @include('fields.visible', ['frm_uniq_id'=>'copy_view', 'fld_name' => '', 'group_label' => '', 'label_title' => 'Jaunā skata nosaukums', 'is_required' => 1, 'hint' => '', 'item_htm' => '<input class="form-control" type=text name = "view_title"  maxlength="500" value = "" required /><span class="glyphicon form-control-feedback" aria-hidden="true"></span>'])
        </div>
        <div>
            <div class="col-lg-4">
            </div>
            <div class="col-lg-8">
                <button class="btn btn-primary" type="button" id='{{ $form_guid }}_copy' title="Kopēt skatu" style="margin-left: -5px;">Kopēt</button>
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
    
    $("#{{ $form_guid }}_copy").click(function(event) {
        event.stopPropagation();
        $('#item_edit_form_{{ $form_guid }}').validator('validate');
        
        copyView();
    });
    
    function copyView()
    {   
        var form_el = $("#{{ $form_guid }}");
        
        var view_id = form_el.find("select[name=view_id]").val();
        var view_title = form_el.find("input[name=view_title]").val();

        var formData = new FormData();
        formData.append("view_id", view_id);
        formData.append("view_title", view_title);
        
        var grid_htm_id = $("#" + get_last_form_id()).find("div[dx_list_id={{ $views_list_id }}]").attr("dx_grid_id");
        
        var request = new FormAjaxRequest ("/structure/method/view_copy", "", grid_htm_id, formData);

        request.callback = function(data) {
            
            if (grid_htm_id)
            {
                reload_grid(grid_htm_id);
            }            

            notify_info("Skats veiksmīgi nokopēts!");
        };

        // izpildam AJAX pieprasījumu
        request.doRequest();
    }
    
    setTimeout(function() {
        $("#{{ $form_guid }}").find("select[name=view_id]").focus();
    }, 500);
    
</script>