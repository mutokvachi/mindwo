<div class='dx-cms-workflow-form'
     data-dx_is_init="0"
     data-date-format="{{ config('dx.txt_date_format') }}" 
     data-locale='{{ Lang::locale() }}'
     data-xml_data = '{{ $xml_data }}' 
     data-wf_id = '{{ $workflow->id }}' 
     data-wf_register_id = '{{ $workflow->list_id }}' 
     data-wf_task_types = "{{ App\Models\Workflow\TaskType::select('id', 'code')->get()->toJson() }}"
     data-wf_steps_list_id="{{ App\Libraries\DBHelper::getListByTable('dx_workflows')->id }}"
     data-max_step_nr="{{ $max_step_nr }}"
     style="display: block; margin-top: -440px;">
        @include('elements.form_header',['form_title' => $form_title, 'badge' => ''])
        <div class='modal-body dx-cms-workflow-form-body' style="max-height:none">
            @include('workflow.visual_ui.wf_component')
        </div>
        <div >       
            <button type="button" class="btn btn-primary dx-cms-workflow-form-btn-save">&nbsp;{{ trans('workflow.save') }}</button>
            <button type='button' class='btn btn-white' data-dismiss='modal'><i class='fa fa-sign-out'></i>&nbsp;{{ trans('task_form.btn_close') }}</button>
        </div>
</div>
<!-- 

data-backdrop="static" 
data-is-init="0" 
data-frm-uniq-id="40043d91-d8d0-4521-8341-03ad4464fe6a" 
data-grid-htm-id="grid_e2bcc195-5eb1-4242-95b2-fe962f02e262" 
data-item-id="5" 

-->