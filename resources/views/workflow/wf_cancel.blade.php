<div class='modal fade dx-cancel-wf-form' aria-hidden='true' id='wf_cancel_form_{{ $frm_uniq_id }}' role='dialog' aria-hidden='true' data-backdrop='static' style="z-index: 999999;"
    data-is-init = "0"
    data-list-id = "{{ $list_id }}"
    data-item-id = "{{ $item_id }}"
    data-grid-id = "{{ $grid_htm_id }}" 
>
    <div class='modal-dialog modal-md'>
            <div class='modal-content'>
                    @include('elements.form_header',['form_title' => trans('task_form.wf_cancel_form_title'), 'badge' => ''])
                   				
                    <div class='modal-body'>
                        
                                <div class="form-horizontal">                                    
                                    <div class='form-group has-feedback'>
                                        <label class='col-lg-4 control-label'>
                                            {{ trans('task_form.lbl_cancel_comment') }}<span style="color: red"> *</span>
                                        </label>
                                        <div class='col-lg-8'>
                                            <div class="input-group" style="width: 100%;">                                        
                                                <textarea class='form-control' name='comment' rows='4' maxlength='4000'></textarea>
                                            </div>
                                        </div>    
                                    </div>
                                </div>

                    </div>
                    <div class='modal-footer'>                        
                        <button type='button' class='btn btn-primary dx-btn-cancel-wf'>{{ trans('task_form.btn_cancel_wf') }}</button>                          
                        <button type='button' class='btn btn-white' data-dismiss='modal'><i class='fa fa-sign-out'></i>&nbsp;{{ trans('task_form.btn_close') }}</button>                            
                    </div>
            </div>
    </div>
</div>