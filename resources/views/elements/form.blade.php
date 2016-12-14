@if ($is_form_reloaded === 0)
<div class='modal fade' aria-hidden='true' id='list_item_view_form_{{ $frm_uniq_id }}' role='dialog' data-backdrop='static'>


    <div class='modal-dialog modal-lg' {!! ($form_width > 0) ? 'style="width: ' . $form_width . '%;"' : '' !!}>
        <div class='modal-content'>
@endif
            @include('elements.form_header',['form_title' => $form_title, 'badge' => $form_badge])

                
                <div class='modal-header' style='background-color: #EEEEEE; border-bottom: 1px solid #c1c1c1; min-height: 55px; {{ ($item_id > 0 && !$form_is_edit_mode) ? '' : 'display: none;'}}' id="top_toolbar_list_item_view_form_{{ $frm_uniq_id }}">
                    @if ($is_workflow_defined && $workflow_btn > 1)
                        <div class="btn-group pull-right">
                            <button type="button" class="btn 
                                @if ($workflow_btn == 2)
                                    blue-hoki
                                @endif
                                
                                @if ($workflow_btn == 3)
                                    red-soft
                                @endif
                                
                                @if ($workflow_btn == 4)
                                    green-meadow
                                @endif
                                
                                btn-sm btn-outline dropdown-toggle" data-toggle="dropdown" aria-expanded="true"
                                
                                style="border: 1px solid {{ ($workflow_btn == 2) ? '#67809F' : (($workflow_btn == 3) ? '#E43A45' : '#1BBC9B') }}!important;"> 
                                    @if ($workflow_btn == 2)
                                        {{ trans('task_form.doc_in_process') }}
                                    @endif

                                    @if ($workflow_btn == 3)
                                        {{ trans('task_form.doc_rejected') }}
                                    @endif

                                    @if ($workflow_btn == 4)
                                        {{ trans('task_form.doc_approved') }}
                                    @endif
                            
                                        <i class="fa fa-angle-down"></i>
                            </button>
                    
                            <ul class="dropdown-menu pull-right" role="menu">                                                              
                                <li>
                                    <a href="javascript:;" class="dx-menu-task-history">
                                        <i class="fa fa-tasks"></i> {{ trans('task_form.menu_task_history') }}</a>
                                </li>
                                @if ($workflow_btn == 2)
                                    <li class="divider"> </li>
                                    <li>
                                        <a href="javascript:;">
                                            <i class="fa fa-undo"></i> {{ trans('task_form.menu_cancel_wf') }}</a>
                                    </li>
                                @endif
                            </ul>   
                        </div>                        
                    @endif
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
                    @if ($workflow_btn == 3)
                            <div class="alert alert-danger" role="alert" style="margin-top: 15px;">
                                Noraidīja: <b>Jānis Supe</b>
                                <br>
                                <i>Te ir noraidīšanas pamatojums kaut kāds iespējams garš</i>
                            </div>
                    @endif
                    <!--<button  type='button' class='btn btn-white pull-right' id='btn_print_{{ $frm_uniq_id }}'><i class="fa fa-print"></i> Drukāt</button>-->
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
                
            </div>
            
            <div class='modal-footer' style='border-top: 1px solid #c1c1c1;'>

                @if ($is_disabled == 0)
                    <button  type='button' class='btn btn-primary' id='btn_save_{{ $frm_uniq_id }}'>{{ trans('form.btn_save') }}</button>
                @endif

                <button type='button' class='btn btn-white' data-dismiss='modal'>{!! ($form_is_edit_mode == 0) ? "<i class='fa fa-sign-out'></i>" . trans('form.btn_close') : "<i class='fa fa-undo'></i> " . trans('form.btn_cancel') !!}</button>
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

                            unregister_form('list_item_view_form_{{ $frm_uniq_id }}');

                            $('#list_item_view_form_{{ $frm_uniq_id }}').remove();
                            $('#form_init_wf_{{ $frm_uniq_id}}').remove();
                            $('#form_init_wf_approver_{{ $frm_uniq_id}}').remove();
                            
                            @if ($tabs_htm)
                                $('{{ $tab_id }}').remove();
                            @endif
                    });

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

                    @if ($is_form_reloaded === 0)                
                        $( '#list_item_view_form_{{ $frm_uniq_id }}' ).modal('show');
                    @endif
                    
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