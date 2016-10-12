<div style="margin: 20px;">
    <p>Ģenerējot reģistru, tiks automātiski izveidoti visi reģistra lauki, skats un datu ievades forma.</p>
    <p>Jāņem vērā sekojoši nosacījumi/ierobežojumi:</p>
    <ul>
        <li>Ģenerētajā skatā un formā tiks iekļauti visi attiecīgās tabulas lauki;</li>
        <li>Varchar tipa lauki tiek veidoti ar tipu "Teksts". Datņu, reģistrācijas numuru, programmatūras kodu u.c. gadījumos jāveic papildus konfigurācijas.</li>
    </ul>
</div>
<div id="{{ $form_guid }}">
    <form class="form-horizontal" id='item_edit_form_{{ $form_guid }}' method='POST' data-toggle="validator">
        <div>
            @include('fields.visible', ['fld_name' => '', 'group_label' => '', 'label_title' => 'Reģistra nosaukums', 'is_required' => 1, 'hint' => 'Nosaukums tiks parādīts virs tabulārā saraksta. Jāraksta daudzskaitlī.', 'item_htm' => '<input class="form-control" type=text name = "register_title"  maxlength="500" value = "" required /><span class="glyphicon form-control-feedback" aria-hidden="true"></span>'])
        </div>
        <div>
            @include('fields.visible', ['fld_name' => '', 'group_label' => '', 'label_title' => 'Formas nosaukums', 'is_required' => 1, 'hint' => 'Formas nosaukums jāraksta vienskaitlī.', 'item_htm' => '<input class="form-control" type=text name = "form_title"  maxlength="100" value = "" required /><span class="glyphicon form-control-feedback" aria-hidden="true"></span>'])
        </div>
        <div>
            <div class="col-lg-4">
            </div>
            <div class="col-lg-8">
                <button class="btn btn-primary" type="button" id='{{ $form_guid }}_generate' title="Ģenerēt reģistru" style="margin-left: -5px;">Ģenerēt</button>
            </div>
        </div>
        <input type=hidden name='obj_id' value='{{ $obj_id }}'>
    </form>
</div>
<script>
    $("#{{ $form_guid }}_generate").click(function(event) {
        event.stopPropagation();
        $('#item_edit_form_{{ $form_guid }}').validator('validate');
        
        generateRegister();
    });
    
    function generateRegister()
    {   
        var form_el = $("#{{ $form_guid }}");
        
        var obj_id = form_el.find("input[name=obj_id]").val();
        var register_title = form_el.find("input[name=register_title]").val();
        var form_title = form_el.find("input[name=form_title]").val();
        
        if (obj_id === 0)
        {
            notify_err("Lai ģenerētu reģistru, vispirms veiciet objekta datu saglabāšanu!");
            return;
        }

        var formData = new FormData();
        formData.append("obj_id", obj_id);
        formData.append("register_title", register_title);
        formData.append("form_title", form_title);
        
        var grid_htm_id = $("#" + get_last_form_id()).find(".tab-pane .table-responsive table").attr("id");
        
        var request = new FormAjaxRequest ("/structure/method/register_generate", "", grid_htm_id, formData);

        request.callback = function(data) {
            
            if (grid_htm_id)
            {
                reload_grid(grid_htm_id);
            }            

            notify_info("Reģistrs veiksmīgi noģenerēts!");
        };

        // izpildam AJAX pieprasījumu
        request.doRequest();
    }
    
    setTimeout(function() {
        $("#{{ $form_guid }}").find("input[name=register_title]").focus();
    }, 500);
    
</script>