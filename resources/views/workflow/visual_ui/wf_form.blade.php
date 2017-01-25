<div class='modal fade dx-cms-workflow-form' aria-hidden='true' id='list_item_view_form_{{ $frm_uniq_id }}' role='dialog' data-backdrop='static' 
    data-is-init = "0"     
    data-frm-uniq-id = "{{ $frm_uniq_id }}"
    data-grid-htm-id = "{{ $grid_htm_id }}"
    data-item-id = "{{ $item_id }}"
    data-json-model = '{{ $json_model }}'
    >
        <div class='modal-dialog modal-lg'>
                <div class='modal-content'>
                        
                        @include('elements.form_header',['form_title' => $form_title, 'badge' => ''])
                                            
                        <div class='modal-body'>
                            <div class="row">
                                <div class="col-lg-2">
                                    <div id="dx_cms-wf-palette" style="border: solid 1px gray; height: 720px;"></div>
                                </div>
                                <div class="col-lg-10">
                                    <div id="dx-cms-wf-area" style="border: solid 1px gray; height: 720px;"></div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="modal-footer">                            
                            <button type='button' class='btn btn-white' data-dismiss='modal'><i class='fa fa-sign-out'></i>&nbsp;{{ trans('task_form.btn_close') }}</button>
                        </div>
                </div>
        </div>
</div>