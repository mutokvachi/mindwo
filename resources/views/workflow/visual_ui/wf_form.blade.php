<div class='modal dx-cms-workflow-form' aria-hidden='true' id='list_item_view_form_{{ $frm_uniq_id }}' role='dialog' data-backdrop='static' 
     data-dx_is_init=0   
     data-frm-uniq-id = "{{ $frm_uniq_id }}"
     data-grid-htm-id = "{{ $grid_htm_id }}"
     data-date-format="{{ config('dx.txt_date_format') }}" 
     data-locale='{{ Lang::locale() }}'
     data-xml_data = '{{ $xml_data }}' 
     data-wf_id = '{{ $item_id }}' 
     data-wf_register_id = '{{ $wf_register_id }}' 
     data-wf_task_types = '{{ App\Models\Workflow\TaskType::select('id', 'code')->get()->toJson() }}' 
     data-wf_steps_list_id="{{ App\Libraries\DBHelper::getListByTable('dx_workflows')->id }}"
     data-max_step_nr="{{ $max_step_nr }}"
     style="display: block; margin-top: -440px;">
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
            @include('elements.form_header',['form_title' => $form_title, 'badge' => ''])
            <div class='modal-body dx-cms-workflow-form-body' style="max-height:none">
                @include('workflow.visual_ui.wf_component')
            </div>
            <div class="modal-footer">       
                <button type="button" class="btn btn-primary dx-cms-workflow-form-btn-save">&nbsp;{{ trans('workflow.save') }}</button>
                <button type='button' class='btn btn-white' data-dismiss='modal'><i class='fa fa-sign-out'></i>&nbsp;{{ trans('task_form.btn_close') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- 

data-backdrop="static" 
data-is-init="0" 
data-frm-uniq-id="40043d91-d8d0-4521-8341-03ad4464fe6a" 
data-grid-htm-id="grid_e2bcc195-5eb1-4242-95b2-fe962f02e262" 
data-item-id="5" 

-->