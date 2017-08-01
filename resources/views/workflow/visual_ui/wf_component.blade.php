<div class='dx-cms-workflow-form' 
     data-dx_is_init=0   
     data-frm-uniq-id = "{{ $frm_uniq_id }}"
     data-grid-htm-id = "{{ $grid_htm_id }}"
     data-date-format="{{ config('dx.txt_date_format') }}" 
     data-locale='{{ Lang::locale() }}'
     data-xml_data = '{{ $xml_data }}' 
     data-wf_id = '{{ $item_id }}' 
     data-wf_register_id = '{{ $wf_register_id }}' 
     data-wf_task_types = "{{ App\Models\Workflow\TaskType::select('id', 'code')->get()->toJson() }}"
     data-wf_steps_list_id="{{ App\Libraries\DBHelper::getListByTable('dx_workflows')->id }}"
     data-max_step_nr="{{ $max_step_nr }}">
    <div style="margin-bottom: 10px; margin-left: 10px;">
        <button type="button" 
                class="btn btn-sm btn-primary dx-cms-workflow-form-btn-arrange" 
                data-toggle="tooltip"
                title="{{ trans('workflow.arrangee_tooltip') }}">
            {{ trans('workflow.arrange') }}
        </button>
    </div>
    <div class="dx-wf-container">
        <div class='dx-wf-toolbar'></div>
        <div class='dx-wf-graph'></div>
    </div>
</div>
