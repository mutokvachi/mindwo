<div class='dx-cms-constructor-workflow'>
        <div class='dx-cms-workflow-form-body'>
            <div class="tabbable-line">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#dx-cms-workflow-form-tab-details" class="dx-cms-workflow-form-tab-details-btn" data-toggle="tab"> {{ trans('workflow.wf_details') }} </a>
                    </li>
                    <li>
                        <a href="#dx-cms-workflow-form-tab-steps" class="dx-cms-workflow-form-tab-steps-btn" data-toggle="tab"> {{ trans('workflow.wf_steps') }} </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="dx-cms-workflow-form-tab-details">
                        @include('workflow.visual_ui.wf_details', [
                                'workflow' => $workflow,
                                'item_id' => $workflow ? $workflow->id : 0,
                                'wf_register_id' => $wf_register_id
                        ])
                    </div>
                    <div class="tab-pane dx-cms-workflow-form-tab-steps" id="dx-cms-workflow-form-tab-steps">
                        @include('workflow.visual_ui.wf_component',[
                                'frm_uniq_id' => '',
                                'grid_htm_id' => '',
                                'xml_data' => $xml_data,
                                'item_id' => $workflow ? $workflow->id : 0,
                                'wf_register_id' => $wf_register_id,
                                'max_step_nr' => $max_step_nr
                            ])
                    </div>
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