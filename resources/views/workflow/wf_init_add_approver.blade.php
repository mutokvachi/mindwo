<div class='modal fade' aria-hidden='true' id='form_init_wf_approver_{{ $frm_uniq_id }}' role='dialog' aria-hidden='true' data-backdrop='static' style="z-index: 999999;"
     dx_added_success = "{{ trans('workflow.wf_init_add_form_employee_success') }}"
     dx_search_placeholder = "{{ trans('workflow.wf_init_add_form_employee_search_placeholder') }}"
     dx_system_error = "{{ trans('workflow.wf_init_add_form_employee_system_error') }}"
     dx_error_empl_not_set = "{{ trans('workflow.wf_init_add_form_employee_error_not_set') }}"
     dx_error_already_added = "{{ trans('workflow.wf_init_add_form_employee_error_already_added') }}"
     >
    <div class='modal-dialog modal-md'>
            <div class='modal-content'>
                    @include('elements.form_header',['form_title' => trans('workflow.wf_init_approver_form_title'), 'badge' => ''])
                   				
                    <div class='modal-body' style="overflow-y: auto; max-height: 500px; padding-left: 40px;">
                        <div class="form-horizontal" style="margin-top: 20px; margin-bottom: 40px;">
                            <div class='form-group has-feedback'>
                                <label class='col-lg-4 control-label'>
                                    <i class='fa fa-question-circle dx-form-help-popup' title='{{ trans('workflow.wf_init_add_form_employee_label_hint') }}' style='cursor: help;'></i>&nbsp;{{ trans('workflow.wf_init_add_form_employee_label') }}<span style="color: red"> *</span>
                                </label>
                                <div class='col-lg-8'>
                                    <div class="input-group" style="width: 100%;">                                        
                                        <input type='text' name = 'empl_txt' value = '' class='form-control select2-remote' required />
                                    </div>
                                </div>    
                            </div>
                            <div class='form-group has-feedback'>
                                <label class='col-lg-4 control-label'></span>
                                </label>
                                <div class='col-lg-8'>
                                    <div class="dx-cms-empl-position-title" style="min-height: 33px; border: 1px solid #ccc; border-radius: 3px; padding: 7px;">
                                        {{ trans('workflow.wf_init_add_form_employee_position_placeholder') }}
                                    </div>
                                </div>    
                            </div>
                        </div>
                        
                        <div style="display: none;" class="dx-cms-approver-item-template">
                            @include('workflow.wf_init_approver_item', [
                                'employee_id' => "[empl_id]",
                                'display_name' => "[display_name]",
                                'position_title' => "[position_title]",
                                'subst_info' => "",
                                'due_days' => "[due_days]"
                            ])
                        </div>
                    </div>
                    <div class='modal-footer'>                        
                        <button class="btn btn-primary dx-cms-wf-btn-add-approver" type="button">{{ trans('workflow.wf_init_approver_btn_add') }}</button>&nbsp;
                        <button type='button' class='btn btn-white' data-dismiss='modal'><i class='fa fa-sign-out'></i>&nbsp;{{ trans('workflow.wf_init_btn_cancel') }}</button>                            
                    </div>
            </div>
    </div>
</div>