<div style="margin: 20px;">
    <p>Ģenerējot skatu, tajā tiks automātiski ievietoti visi reģistra lauki.</p>    
</div>
<div id="{{ $form_guid }}" style="margin: 20px;">
    <form class="form-horizontal" id='item_edit_form_{{ $form_guid }}' method='POST' data-toggle="validator">
        <div>
            @include('fields.visible', ['frm_uniq_id'=>'gener_reg', 'fld_name' => '', 'group_label' => '', 'label_title' => 'Skata nosaukums', 'is_required' => 1, 'hint' => '', 'item_htm' => '<input class="form-control" type=text name = "view_title"  maxlength="500" value = "" required /><span class="glyphicon form-control-feedback" aria-hidden="true"></span>'])
        </div>
        <div>            
            <button class="btn btn-primary" type="button" id='{{ $form_guid }}_generate' title="Ģenerēt skatu" style="margin-left: -5px;">Ģenerēt</button>            
        </div>
        <input type=hidden name='list_id' value='{{ $list_id }}'>
    </form>
</div>
<script>
    
    $("#{{ $form_guid }}_generate").click(function(event) {
        event.stopPropagation();
        $('#item_edit_form_{{ $form_guid }}').validator('validate');
        
        generateView();
    });
    
    function generateView()
    {   
        var form_el = $("#{{ $form_guid }}");
        
        var list_id = form_el.find("input[name=list_id]").val();
        var view_title = form_el.find("input[name=view_title]").val();
        
        if (list_id === 0)
        {
            notify_err("Lai ģenerētu skatu, vispirms veiciet reģistra datu saglabāšanu!");
            return;
        }

        var formData = new FormData();
        formData.append("list_id", list_id);
        formData.append("view_title", view_title);
        
        var grid_htm_id = $("#" + get_last_form_id()).find(".tab-pane .table-responsive table").attr("id");
        
        var request = new FormAjaxRequest ("/structure/method/view_generate", "", grid_htm_id, formData);

        request.callback = function(data) {
            
            if (grid_htm_id)
            {
                reload_grid(grid_htm_id);
            }            

            notify_info("Skats veiksmīgi noģenerēts!");
        };

        // izpildam AJAX pieprasījumu
        request.doRequest();
    }
    
    setTimeout(function() {
        $("#{{ $form_guid }}").find("input[name=view_title]").focus();
    }, 500);
    
</script>