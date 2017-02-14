<div class='modal fade' aria-hidden='true' id='form_import_{{ $menu_id }}' role='dialog' aria-hidden='true' data-backdrop='static' style="z-index: 999999;"
    data-grid-id = "{{ $grid_id }}"
    data-list-id = "{{ $list_id }}"
    data-trans-invalid-file = "{{ trans('grid.invalid_file') }}"
    data-trans-invalid-file-format = "{{ trans('grid.invalid_file_format') }}"
    data-trans-success = "{{ trans('grid.success') }}"
    data-trans-ignored-columns = "{{ trans('grid.ignored_columns') }}"
    data-trans-excel-row = "{{ trans('errors.excel_row') }}"
    data-trans-excel-dependent = "{{ trans('errors.excel_dependent') }}"
>
    <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
                    @include('elements.form_header',['form_title' => trans('grid.import_title'), 'badge' => ''])
                   				
                    <div class='modal-body' style="overflow-y: auto; max-height: 500px; padding-left: 40px;">
                        <div class="ŗow">
                            {{ trans('grid.import_hint') }}
                        </div>
                        <div class="row">
                            <div class="col-md-12" style="min-height: 70px; margin-top: 20px;">
                                <div class="form-horizontal dx-import-fields">
                                    <div class='form-group has-feedback dx-form-field-line' dx_fld_name_form="import_file">
                                        <label class='col-lg-4 control-label'>                                            
                                            <i class='fa fa-question-circle dx-form-help-popup' title='{{ trans('grid.file_hint') }}' style='cursor: help;'></i>&nbsp;
                                            
                                            {{ trans('grid.lbl_file')}}

                                            <span style="color: red"> *</span>                                            
                                        </label>
                                        <div class='col-lg-8'>
                                            @include('fields.file', ['class_exist' => 'new', 'field_id' => 0,  'is_disabled' => 0, 'item_value' => '', 'item_field' => 'import_file', 'is_required' => 1, 'item_field_remove' => 'import_file_remove'])                                            
                                            <div class="help-block with-errors"></div>
                                        </div>    
                                    </div>
                                </div>
                                <div class="dx-import-progress text-center" style="display: none;">
                                    <img src="{{Request::root()}}/assets/global/progress/loading.gif" alt="{{ trans('frame.please_wait') }}" title="{{ trans('frame.please_wait') }}" />
                                    {{ trans('frame.data_processing') }}
                                </div>
                                <div class="text-center alert alert-info bg-green-jungle bg-font-green-jungle" role="alert" style="display: none;">                                    
                                </div>
                                <div class="text-center alert alert-error bg-red-sunglo bg-font-red-sunglo" role="alert" style="display: none;">                                    
                                </div>
                            </div>                            
                        </div>
                    </div>
                    <div class='modal-footer'>                        
                        <button type='button' class='btn btn-primary' id='btn_start_import_{{ $menu_id }}'><i class='fa fa-upload'></i>&nbsp;{{ trans('grid.btn_start_import') }}</button>
                        <button type='button' class='btn btn-white' data-dismiss='modal'><i class='fa fa-sign-out'></i>&nbsp;{{ trans('grid.btn_close') }}</button>                            
                    </div>
            </div>
    </div>
</div>