@if ($is_form_reloaded === 0)
<div class='modal fade' aria-hidden='true' id='list_item_view_form_{{ $frm_uniq_id }}' role='dialog' data-backdrop='static'>


    <div class='modal-dialog modal-lg' {!! ($form_width > 0) ? 'style="width: ' . $form_width . '%;"' : '' !!}>
        <div class='modal-content'>
@endif
            @include('elements.form_header',['form_title' => $form_title, 'badge' => $form_badge])

                
                <div class='modal-header' style='background-color: #EEEEEE; border-bottom: 1px solid #c1c1c1; min-height: 55px; {{ ($item_id > 0 && !$form_is_edit_mode) ? '' : 'display: none;'}}' id="top_toolbar_list_item_view_form_{{ $frm_uniq_id }}">
                    @if ($is_workflow_defined && $workflow_btn > 1)
                        @include('workflow.wf_status_btn', ['is_wf_cancelable' => $self->is_wf_cancelable])                                      
                    @endif
                    <div class="dx_form_btns_left">
                        @include('elements.form_left_btns')
                    </div>                    
                </div>
                
           

            <div class='modal-body'>
                
                    <div class='row dx-form-row'>
                        <form class="form-horizontal" method='POST' data-toggle="validator" id='item_edit_form_{{ $frm_uniq_id }}'>
                            <div class="dx-cms-form-fields-section" style='margin-left: 20px;' 
                                 dx_attr="form_fields" 
                                 dx_is_init="0" 
                                 dx_form_id="{{ $frm_uniq_id }}" 
                                 dx_grid_id="{{ $grid_htm_id }}" 
                                 dx_is_wf_btn="{{ $workflow_btn }}"
                                 dx_list_id="{{ $list_id }}"
                                 dx_is_custom_approve = "{{ $is_custom_approve }}"
                                 data-parent-field-id = "{{ $parent_field_id }}"
                                 data-parent-item-id = "{{ $parent_item_id }}"
                                 data-is-edit-mode = "{{ $form_is_edit_mode }}"
                                 >
                                @if ($workflow_btn == 3)
                                        <div class="alert alert-danger dx-reject-info" role="alert" style="margin-top: 15px;">
                                            {{ trans('task_form.lbl_rejected_by') }}: <b>{{ $self->reject_task->display_name }}</b>, {{ long_date($self->reject_task->task_closed_time) }}
                                            <br>
                                            <i>{{ $self->reject_task->task_comment }}</i>
                                        </div>
                                @endif
                                {!! $fields_htm !!}
                            </div>

                            <input type=hidden id='{{ $frm_uniq_id }}_edit_form_id' name='edit_form_id' value='{{ $form_id }}'>
                            <input type=hidden id='{{ $frm_uniq_id }}_item_id' name='item_id' value='{{ $item_id }}'>

                            @if ($is_multi_registers == 1)
                                <input type=hidden id='{{ $frm_uniq_id }}_multi_list_id' name='multi_list_id' value='{{ $list_id }}'>
                            @endif

                            @if ($call_field_htm_id)
                                <input type=hidden id='{{ $frm_uniq_id }}_call_field_htm_id' name='call_field_htm_id' value='{{ $call_field_htm_id }}'>
                                <input type=hidden id='{{ $frm_uniq_id }}_call_field_id' name='call_field_id' value='{{ $call_field_id }}'>
                                <input type=hidden id='{{ $frm_uniq_id }}_call_field_type' name='call_field_type' value='{{ $call_field_type }}'>
                            @endif

                            {!! $tabs_htm !!}
                        </form>
                    </div>                   
                
            </div>
            
            <div class='modal-footer' style='border-top: 1px solid #c1c1c1;'>
                <a href='javascript:;' class='dx-cms-history-link pull-left' style='margin-top: 5px; {{ $item_id == 0 ? "display: none" : ""}}' title='{{ trans('form.hint_history') }}'><i class='fa fa-history'></i> {{ trans('form.link_history') }}&nbsp;</a><span class="badge badge-default pull-left dx-history-badge" style="display: {{ ($history_count) ? 'block' : 'none'}};">{{ $history_count }}</span>
                @if ($is_disabled == 0)
                    <button  type='button' class='btn btn-primary dx-btn-save-form' id='btn_save_{{ $frm_uniq_id }}'>{{ trans('form.btn_save') }}</button>
                @endif

                <button type='button' class='btn btn-white dx-btn-cancel-form' data-dismiss='modal'>{!! ($form_is_edit_mode == 0) ? "<i class='fa fa-sign-out'></i>" . trans('form.btn_close') : "<i class='fa fa-undo'></i> " . trans('form.btn_cancel') !!}</button>
            </div>
            <script type='text/javascript'>
                register_form('list_item_view_form_{{ $frm_uniq_id }}', {{ $item_id }});
            </script>
            
            @include('elements.form_custom_js', ['js_code'=>$js_code, 'frm_uniq_id'=>$frm_uniq_id, 'js_form_id' => $js_form_id])

            <script type='text/javascript'>                   

                    $('#list_item_view_form_{{ $frm_uniq_id }}').on('show.bs.modal', function () {
                            $('#list_item_view_form_{{ $frm_uniq_id }} .modal-body').css('overflow-y', 'auto');
                    });

                    $('#list_item_view_form_{{ $frm_uniq_id }}').on('hidden.bs.modal', function (e) {			

                            @if ($grid_htm_id)
                                stop_executing('{{ $grid_htm_id }}');
                            @endif
                            
                            var arr_callbacks = get_form_callbacks('{{ $frm_uniq_id }}');
                            if (typeof arr_callbacks != 'undefined') {
                                if (typeof arr_callbacks.after_close != 'undefined' && $( '#list_item_view_form_{{ $frm_uniq_id }}' ).length > 0) {                                    
                                    arr_callbacks.after_close.call(this, $( '#list_item_view_form_{{ $frm_uniq_id }}' ));
                                }
                            }
                    
                            unregister_form('list_item_view_form_{{ $frm_uniq_id }}');

                            $('#list_item_view_form_{{ $frm_uniq_id }}').remove();
                            $('#form_init_wf_{{ $frm_uniq_id}}').remove();
                            $('#form_init_wf_approver_{{ $frm_uniq_id}}').remove();
                            
                            @if ($tabs_htm)
                                $('{{ $tab_id }}').remove();
                            @endif
                            
                            toastr.clear();
                    });

                    @if ($form_is_edit_mode == 1)

                        $('#btn_save_{{ $frm_uniq_id }}').click(function dx_btn_save_click( event ) {
                            event.stopPropagation();                            
                                                        
                            $('#item_edit_form_{{ $frm_uniq_id }}').validator('validate');
                            
                            if ($('#item_edit_form_{{ $frm_uniq_id }}').find(".with-errors ul").length > 0) {
                                notify_err(Lang.get('errors.form_validation_err'));
                                return false;
                            }
                            
                            // Calls encryption function which encryptes data and on callback it executes again save function
                            if(!event.encryptionFinished || event.encryptionFinished == false){
                                var cryptoFields = $('input.dx-crypto-field,textarea.dx-crypto-field,input.dx-crypto-field-file', $(this).closest('.modal-content'));
                                
                                window.DxCrypto.encryptFields(cryptoFields, event, function(event){
                                    dx_btn_save_click(event);
                                });
                                
                                return;
                            }
                            
                            var arr_callbacks = get_form_callbacks('{{ $frm_uniq_id }}');
                            if (typeof arr_callbacks != 'undefined') {
                                if (typeof arr_callbacks.before_save != 'undefined') {
                                    if (!arr_callbacks.before_save.call(this, $( '#list_item_view_form_{{ $frm_uniq_id }}' ))) {
                                        return false;
                                    }
                                }
                            }

                            save_list_item('item_edit_form_{{ $frm_uniq_id }}', '{{ $grid_htm_id }}',{{ $list_id }}, {{ $parent_field_id }}, {{ $parent_item_id }}, '{{ $parent_form_htm_id }}', arr_callbacks);//replace
                            
                           /* var cryptoFields = $('.dx-crypto-field', $(this).closest('.modal-content'));
                            window.DxCrypto.decryptFields(cryptoFields);  */                      
                        });

                        $('#item_edit_form_{{ $frm_uniq_id }}').validator({
                            custom : {
                                foo: function($el) 
                                { 
                                    if (!($el.val()>0) && $el.attr('required'))
                                    {
                                        return false;
                                    }
                                    return true;
                                },
                                cbotext: function($el) {
                                    if (!($el.val().length > 0) && $el.attr('required'))
                                    {
                                        return false;
                                    }
                                    return true;
                                },
                                auto: function($el)
                                {
                                    alert($el.val());
                                    return false;
                                }
                            },
                            errors: {
                                foo: '{{ trans("form.err_value_not_set") }}',
                                auto: '{{ trans("form.err_value_not_set") }}',
                                cbotext: '{{ trans("form.err_value_not_set") }}'
                            },
                            feedback: {
                                success: 'glyphicon-ok',
                                error: 'glyphicon-alert'
                            }
                        });                    
                    @endif
                    
                    var arr_callbacks = get_form_callbacks('{{ $frm_uniq_id }}');
                    if (typeof arr_callbacks != 'undefined') {
                        if (typeof arr_callbacks.before_show != 'undefined') {
                            arr_callbacks.before_show.call(this, $( '#list_item_view_form_{{ $frm_uniq_id }}' ));
                        }
                    }
                    
                    @if ($is_form_reloaded === 0)                
                        $( '#list_item_view_form_{{ $frm_uniq_id }}' ).modal('show');
                    @endif
                    
            </script>           
            
            
@if ($is_form_reloaded === 0)            
        </div>
    </div>
</div>
@endif

@if (($workflow_btn == 1 || $workflow_btn == 3) && $form_is_edit_mode == 0 && $is_editable_wf == 1 && $is_edit_rights) 
    @include('workflow.wf_init_form')
    @include('workflow.wf_init_add_approver')    
@endif

@if ($form_is_edit_mode == 0)
    @include('workflow.wf_info_task')
@endif

@include('workflow.wf_cancel')
