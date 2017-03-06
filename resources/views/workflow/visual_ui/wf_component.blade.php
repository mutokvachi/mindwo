<div style="margin-bottom: 10px;">
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
<textarea id="txt_xml" ></textarea>
<button id="set_xml" >Set XML</button>
<button id="get_xml" >Get XML</button>