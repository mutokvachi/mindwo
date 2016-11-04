<div style="margin: 20px;">
    <p>Kopējot darbplūsmu, tiks automātiski kopēti visi soļi un soļos izmantotie datu lauki.</p>
    <p>Jāņem vērā sekojoši nosacījumi/ierobežojumi:</p>
    <ul>
        <li>Kopējamās darbplūsmas reģistra laukiem, kas izmantoti darbplūsmā, ir jāsakrīt ar jaunā reģistra laukiem - salīdzināšana tiek veikta pēc sistēmiskajiem lauku nosaukumiem;</li>
        <li>Ja izvēlēts integritātes nosacījums, kas ignorē lauku neatbilstības, tad jāatcērās, ka jāpārnumurē soļu secīgums;</li>
        <li>Ja jaunajā reģistrā nebūs definēts darbplūsmas statusa lauks, tad kopēšanas laikā tas tiks automātiski nodefinēts, bet netiks iekļauts ne skatā, ne formā</li>
    </ul>
</div>
<div id="{{ $form_guid }}">
    <form class="form-horizontal" id='item_edit_form_{{ $form_guid }}' method='POST' data-toggle="validator">        
        <div>
            <div class='form-group has-feedback'>
                <label class='col-lg-4 control-label'>Kopējamā darbplūsma <span style="color: red"> *</span></label>
                <div class='col-lg-8'>
                    <select class='form-control' name = 'wf_id' required data-foo="bar">
                        <option value="0" selected></option>
                        @foreach($wfs as $item)
                            <option value='{{ $item->id }}'>{{ $item->title }}</option>
                        @endforeach
                </select>
                <span class="glyphicon form-control-feedback" aria-hidden="true" style="margin-right: 10px;"></span>     
                    <div class="help-block with-errors"></div>
                </div>    
            </div>
        </div>
        <div>
            @include('fields.visible', ['frm_uniq_id'=>'copy_wf', 'fld_name' => '', 'group_label' => '', 'label_title' => 'Jaunās darbplūsmas nosaukums', 'is_required' => 1, 'hint' => '', 'item_htm' => '<input class="form-control" type=text name = "wf_title"  maxlength="255" value = "" required /><span class="glyphicon form-control-feedback" aria-hidden="true"></span>'])
        </div>
        <div>
            <div class='form-group has-feedback'>
                <label class='col-lg-4 control-label'>Integritāte <span style="color: red"> *</span></label>
                <div class='col-lg-8'>
                    <select class='form-control' name = 'integrity_id' required data-foo="bar">
                        <option value="0" selected></option>
                        <option value="1">Ziņot par kļūdu, ja neatbilstoši lauki</option>
                        <option value="2">Ignorēt lauku neatbilstības - nekopēt soli</option>                        
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
                <button class="btn btn-primary" type="button" id='{{ $form_guid }}_copy_wf' title="Kopēt darbplūsmu" style="margin-left: -5px;">Kopēt</button>
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
    
    $("#{{ $form_guid }}_copy_wf").click(function(event) {
        event.stopPropagation();
        $('#item_edit_form_{{ $form_guid }}').validator('validate');
        
        copyWf();
    });
    
    function copyWf()
    {   
        var form_el = $("#{{ $form_guid }}");
        
        var wf_id = form_el.find("select[name=wf_id]").val();
        var wf_title = form_el.find("input[name=wf_title]").val();
        var integrity_id = form_el.find("select[name=integrity_id]").val();
        
        var formData = new FormData();
        formData.append("wf_id", wf_id);
        formData.append("wf_title", wf_title);
        formData.append("item_id", {{ $list_id }});
        formData.append("integrity_id", integrity_id);
        
        var grid_htm_id = $("#" + get_last_form_id()).find("div[dx_list_id={{ $wf_list_id }}]").attr("dx_grid_id");
        
        var request = new FormAjaxRequest ("/structure/method/wf_copy", "", grid_htm_id, formData);

        request.callback = function(data) {
            
            if (grid_htm_id)
            {
                reload_grid(grid_htm_id);
            }            

            notify_info("Darbplūsma veiksmīgi nokopēta!");
        };

        // izpildam AJAX pieprasījumu
        request.doRequest();
    }
    
    setTimeout(function() {
        $("#{{ $form_guid }}").find("select[name=wf_id]").focus();
    }, 500);
    
</script>