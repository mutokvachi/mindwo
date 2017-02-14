<div class='modal fade dx-popup-modal-settings' id='{{ $block_id }}_settings' aria-hidden='true' role='dialog' aria-hidden='true' data-backdrop='static' style="z-index: 999999;">
    <div class='modal-dialog modal-md'>
        <div class='modal-content'>
            @include('elements.form_header',['form_title' => trans('grid.field_settings_form_title'), 'badge' => '', 'form_icon' => '<i class="fa fa-cog"></i>'])

            <div class='modal-body' style="overflow-y: auto; max-height: 500px;">
                <div class="form-group col-lg-12">
                    <label for="field_title">                        
                        <span class="dx-fld-title">{{ trans('grid.lbl_field_title') }}</span>
                    </label>
                    <div class='pull-right' style='position: absolute; right: 0px; top: -2px; padding-right: 20px;'>
                        <input type='checkbox' name='is_hidden'> {{ trans('grid.ch_is_hidden') }}</span>
                    </div>            
                    <input class="form-control" autocomplete="off" type="text" name="field_title" value="" disabled>
                </div>
                <div class="form-group col-lg-12">
                    <label for="field_operation">                        
                        <span class="dx-fld-title">{{ trans('grid.lbl_field_operation') }}</span>
                    </label>        
                    <select class="form-control" name="field_operation" data-criteria="text">
                        <option value='0' data-is-criteria="0"></option>
                        @foreach ($operations as $oper)
                            <option value='{{ $oper->id }}' data-is-criteria="{{ $oper->is_criteria }}">{{ $oper->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-12 dx-criteria-text" style="display: none;">
                    <label for="criteria_value">                        
                        <span class="dx-fld-title">{{ trans('grid.lbl_criteria_title') }}</span>
                    </label>          
                    <input class="form-control" type="text" name="criteria_value" value="">
                </div>
                <div class="form-group col-lg-12 dx-criteria-auto" style="display: none;">
                    <label for="lookup_value">                        
                        <span class="dx-fld-title">{{ trans('grid.lbl_criteria_title') }}</span>
                    </label>          
                    @include('fields.autocompleate', [
                        'frm_uniq_id' => $block_id, 
                        'item_field' => 'lookup_value', 
                        'field_id' => 0,
                        'item_value' => 0,
                        'is_disabled' => 0,
                        'rel_list_id' => 0,
                        'rel_field_id' => 0,
                        'rel_view_id' => 0,
                        'rel_display_formula_field' => 0,
                        'txt_display' => '',
                        'is_required' => 0,
                        'form_url' => 'form',
                        'frm_uniq_id_js' => str_replace("-", "_", $block_id),
                        'is_manual_init' => 1
                    ])
                </div>
            </div>
            <div class='modal-footer dx-view-popup-footer'>                
                <button type='button' class='btn btn-primary dx-settings-btn-save'>{{ trans('form.btn_save') }}</button>                
                <button type='button' class='btn btn-white' data-dismiss='modal'>{{ trans('form.btn_cancel') }}</button>                            
            </div>
        </div>
    </div>
</div>