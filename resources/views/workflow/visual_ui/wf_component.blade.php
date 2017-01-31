<div class="dx-wf-container" data-xml_data = '{{ $xml_data }}' data-list_wf_steps_id="{{ App\Libraries\DBHelper::getListByTable('dx_workflows')->id }}">
    <div class='dx-wf-toolbar' style="height:20px; background-color: lightgray;"></div>
    <div class='dx-wf-graph'></div>
</div>
<textarea id="txt_xml" ></textarea>
<button id="set_xml" >Set XML</button>
<button id="get_xml" >Get XML</button>
<button id="save_xml" >Save</button>