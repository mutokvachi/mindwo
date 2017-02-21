<div class='modal dx-cms-workflow-form' aria-hidden='true' id='list_item_view_form_{{ $frm_uniq_id }}' role='dialog' data-backdrop='static' 
     data-is-init = "0"     
     data-frm-uniq-id = "{{ $frm_uniq_id }}"
     data-grid-htm-id = "{{ $grid_htm_id }}"
     data-item-id = "{{ $item_id }}"
     style="display: block; margin-top: -440px;">
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
            @include('elements.form_header',['form_title' => $form_title, 'badge' => ''])
            <div class='modal-body' style="max-height:none">
                @include('workflow.visual_ui.wf_component')
            </div>
            <div class="modal-footer">                            
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