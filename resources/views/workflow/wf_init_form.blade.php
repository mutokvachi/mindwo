<div class='modal fade' aria-hidden='true' id='form_init_wf_{{ $frm_uniq_id }}' role='dialog' aria-hidden='true' data-backdrop='static' style="z-index: 999999;" 
     dx_is_init="0"
     dx_frm_uniq_id="{{ $frm_uniq_id }}"
     dx_grid_htm_id="{{ $grid_htm_id }}"
     >
    <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
                    @include('elements.form_header',['form_title' => trans('workflow.wf_init_form_title'), 'badge' => ''])
                   				
                    <div class='modal-body' style="overflow-y: auto; max-height: 500px; padding-left: 40px;">
                        <h2>{{ trans('workflow.wf_init_approvers_title') }}</h2>
                        <p>{{ trans('workflow.wf_init_aproovers_hint') }}</p>
                        <div>
                            <button class="btn btn-primary dx-cms-wf-btn-add-approver" type="button">{{ trans('workflow.wf_init_btn_add_approver') }}</button>
                            
                            <label class="pull-right" style="cursor: pointer;"><input type="radio" name="order" value="0" checked><b>{{ trans('workflow.wf_init_approval_sequence') }}</b></label>
                            <label class="pull-right" style="cursor: pointer;"><input type="radio" name="order" value="1"><b>{{ trans('workflow.wf_init_approval_paralel') }}</b>&nbsp;</label>
                                                      
                        </div>
                        <div class="dx-cms-init-approvers" style="margin-top: 20px;">
                            
                        </div>
                    </div>
                    <div class='modal-footer'>
                        <a href="#" title="{{ trans('workflow.wf_init_hint_answer') }}" class="pull-left" style="cursor: help;"><i class="fa fa-question-circle"></i> {{ trans('workflow.wf_init_hint_label') }}</a>
                        <button class="btn btn-primary dx-cms-wf-btn-start" type="button">{{ trans('workflow.wf_init_btn_start') }}</button>&nbsp;
                        <button type='button' class='btn btn-white' data-dismiss='modal'><i class='fa fa-sign-out'></i>&nbsp;{{ trans('workflow.wf_init_btn_cancel') }}</button>                            
                    </div>
            </div>
    </div>
</div>