@if ($is_form_reloaded === 0)
<div id='list_item_view_form_{{ $frm_uniq_id }}' class="dx-form-fullscreen-frame">


    <div>
        <div>
@endif
                <div class='modal-header dx-form-header' style='background-color: #31708f;'>					
                    <button type='button' class='close dx-form-close-btn' title="{{ trans('form.btn_close') }}"><i class='fa fa-times' style="color: white"></i></button>
                    <h4 class='modal-title' style="color: white;">
                        {{ $form_title }}
                        @if ($form_badge)
                            &nbsp;<span class='badge'>{{ $form_badge }}</span>
                        @endif
                    </h4>
                </div>
           
                <div style='background-color: #EEEEEE; border-bottom: 1px solid #c1c1c1;' id="top_toolbar_list_item_view_form_{{ $frm_uniq_id }}">
                    <div class="dx_form_btns_left">
                        @if ($is_edit_rights && $form_is_edit_mode == 0 && $is_editable_wf == 1)
                            <button  type='button' class='btn btn-primary' id='btn_edit_{{ $frm_uniq_id }}'><i class="fa fa-pencil-square-o"></i> {{ trans('form.btn_edit') }}</button>
                        @endif

                        @if ($is_delete_rights && $item_id > 0 && $is_editable_wf == 1 && $form_is_edit_mode == 0)
                            <button  type='button' class='btn btn-white' id='btn_delete_{{ $frm_uniq_id }}'><i class="fa fa-trash-o"></i> {{ trans('form.btn_delete') }}</button>
                        @endif

                        @if ($is_edit_rights && $is_word_generation_btn && $is_editable_wf == 1 && $form_is_edit_mode == 0)
                            <button  type='button' class='btn btn-white' id='btn_word_{{ $frm_uniq_id }}' title='{{ trans('form.word_hint') }}'><i class="fa fa-file-word-o"></i> {{ trans('form.word_generate_btn') }}</button>
                        @endif

                        @if ($workflow_btn == 1 && $form_is_edit_mode == 0 && $is_editable_wf == 1 && $is_edit_rights)    
                            <button  type='button' class='btn btn-white dx-init-wf-btn'><font color="green"><i class="fa fa-play"></i></font> {{ trans('form.btn_start_workflow') }}</button>
                        @endif
                        @if ($form_is_edit_mode == 0 && $is_info_tasks_rights)
                            <button  type='button' class='btn btn-white dx-for-info-btn' title="{{ trans('form.btn_info_hint') }}">{{ trans('form.btn_info') }}

                                &nbsp;<span class="badge badge-info dx-cms-info-task-count"
                                            @if (count($info_tasks) == 0)
                                                style="display: none;"
                                            @endif
                                            > {{ count($info_tasks) }} </span>

                            </button>
                        @endif
                    </div>                    
                    <!--<button  type='button' class='btn btn-white pull-right' id='btn_print_{{ $frm_uniq_id }}'><i class="fa fa-print"></i> Drukāt</button>-->
                </div>
           

            <div>
               
                    <form class="form-horizontal dx-form-fullscreen" id='item_edit_form_{{ $frm_uniq_id }}' method='POST' data-toggle="validator">
                        <div class="dx-cms-form-fields-section" 
                             dx_attr="form_fields" 
                             dx_is_init="0" 
                             dx_form_id="{{ $frm_uniq_id }}" 
                             dx_grid_id="{{ $grid_htm_id }}" 
                             dx_is_wf_btn="{{ $workflow_btn }}"
                             dx_list_id="{{ $list_id }}"
                             dx_is_custom_approve = "{{ $is_custom_approve }}"
                             >
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
            
            <div style='border-top: 1px solid #c1c1c1;'>

                @if ($is_disabled == 0)
                    <button  type='button' class='btn btn-primary' id='btn_save_{{ $frm_uniq_id }}'>{{ trans('form.btn_save') }}</button>
                @endif

                <button type='button' class='btn btn-white dx-form-close-btn'>{!! ($form_is_edit_mode == 0) ? "<i class='fa fa-sign-out'></i>" . trans('form.btn_close') : "<i class='fa fa-undo'></i> " . trans('form.btn_cancel') !!}</button>
            </div>

            @include('elements.form_custom_js', ['js_code'=>$js_code, 'frm_uniq_id'=>$frm_uniq_id, 'js_form_id' => $js_form_id])

            <script type='text/javascript'>                   

                    

                    

                    @if ($form_is_edit_mode == 1)

                        $('#btn_save_{{ $frm_uniq_id }}').click(function( event ) {
                            event.stopPropagation();
                            $('#item_edit_form_{{ $frm_uniq_id }}').validator('validate');

                            save_list_item('item_edit_form_{{ $frm_uniq_id }}', '{{ $grid_htm_id }}',{{ $list_id }}, {{ $parent_field_id }}, {{ $parent_item_id }}, '{{ $parent_form_htm_id }}');//replace
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
                                auto: function($el)
                                {
                                    alert($el.val());
                                    return false;
                                }
                            },
                            errors: {
                                foo: '{{ trans("form.err_value_not_set") }}',
                                auto: '{{ trans("form.err_value_not_set") }}'
                            },
                            feedback: {
                                success: 'glyphicon-ok',
                                error: 'glyphicon-alert'
                            }
                        });
                    @else
                        $('#btn_print_{{ $frm_uniq_id }}').click(function(event){
                            event.stopPropagation();
                            printForm('item_edit_form_{{ $frm_uniq_id }}');
                        });

                        $('#btn_edit_{{ $frm_uniq_id }}').click(function(event){
                            event.stopPropagation();
                            open_form('form', {{ $item_id }}, {{ $list_id }}, {{ $parent_field_id }}, {{ $parent_item_id }}, '{{ $grid_htm_id }}', 1, 'list_item_view_form_{{ $frm_uniq_id }}');
                        });

                        $('#btn_delete_{{ $frm_uniq_id }}').click(function( event ) {
                          event.stopPropagation();
                          delete_list_item('list_item_view_form_{{ $frm_uniq_id }}', '{{ $grid_htm_id }}');
                        });

                        $('#btn_word_{{ $frm_uniq_id }}').click(function(event){
                            event.stopPropagation();
                            generate_word({{ $item_id }}, {{ $list_id }}, '{{ $grid_htm_id }}', 'list_item_view_form_{{ $frm_uniq_id }}');
                        });
                    @endif

                    
                    register_form('list_item_view_form_{{ $frm_uniq_id }}', {{ $item_id }});
            </script>
            
@if ($is_form_reloaded === 0)            
        </div>
    </div>
</div>
@endif

@if ($workflow_btn == 1 &&  $is_custom_approve == 1)
    @include('workflow.wf_init_form')
    @include('workflow.wf_init_add_approver')    
@endif

@if ($form_is_edit_mode == 0 && $is_info_tasks_rights)
    @include('workflow.wf_info_task')
@endif