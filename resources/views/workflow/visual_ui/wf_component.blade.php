<div class="dx-wf-container" 
     data-xml_data = '{{ $xml_data }}' 
     data-wf_id = '{{ $item_id }}' 
     data-wf_register_id = '{{ $wf_register_id }}' 
     data-wf_task_types = '{{ App\Models\Workflow\TaskType::select('id', 'code')->get()->toJson() }}' 
     data-wf_steps_list_id="{{ App\Libraries\DBHelper::getListByTable('dx_workflows')->id }}">
    <div class='dx-wf-toolbar' style="height:20px; background-color: lightgray;"></div>
    <div class='dx-wf-graph' style="background-color: white;"></div>
</div>
<textarea id="txt_xml" ></textarea>
<button id="set_xml" >Set XML</button>
<button id="get_xml" >Get XML</button>