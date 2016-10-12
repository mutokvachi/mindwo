<div class='modal fade' aria-hidden='true' id='form_task_info_{{ $frm_uniq_id }}' role='dialog' aria-hidden='true' data-backdrop='static' style="z-index: 999999;"
     dx_added_success = "{{ trans('wf_info_task.msg_success') }}"
     dx_search_placeholder = "{{ trans('wf_info_task.plh_search') }}"
     dx_system_error = "{{ trans('wf_info_task.err_system_error') }}"
     dx_error_empl_not_set = "{{ trans('wf_info_task.err_empl_not_set') }}"
     dx_is_init = "0"
     >
    <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
                    @include('elements.form_header',['form_title' => trans('wf_info_task.form_title'), 'badge' => ''])
                   				
                    <div class='modal-body' style="overflow-y: auto; max-height: 500px; padding-left: 40px;">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-horizontal" style="margin-top: 20px; margin-bottom: 40px;">
                                    <div class='form-group has-feedback'>
                                        <label class='col-lg-4 control-label'>
                                            <i class='fa fa-question-circle dx-form-help-popup' title='{{ trans('wf_info_task.hint_employee') }}' style='cursor: help;'></i>&nbsp;{{ trans('wf_info_task.lbl_employee') }}<span style="color: red"> *</span>
                                        </label>
                                        <div class='col-lg-8'>
                                            <div class="input-group" style="width: 100%;">                                        
                                                <input type='text' name = 'empl_txt' value = '' class='form-control' required />
                                            </div>
                                        </div>    
                                    </div>
                                    <div class='form-group has-feedback'>
                                        <label class='col-lg-4 control-label'></span>
                                        </label>
                                        <div class='col-lg-8'>
                                            <div class="dx-cms-empl-position-title" style="min-height: 33px; border: 1px solid #ccc; border-radius: 3px; padding: 7px; color: silver;">
                                                {{ trans('wf_info_task.plh_position') }}
                                            </div>
                                        </div>    
                                    </div>
                                    <div class='form-group has-feedback'>
                                        <label class='col-lg-4 control-label'>
                                            <i class='fa fa-question-circle dx-form-help-popup' style='cursor: help;'></i>&nbsp;{{ trans('wf_info_task.lbl_task_info') }}
                                        </label>
                                        <div class='col-lg-8'>
                                            <div class="input-group" style="width: 100%;">                                        
                                                <textarea class='form-control' name='task_details' rows='4' maxlength='4000'></textarea>
                                            </div>
                                        </div>    
                                    </div>
                                    <button class="btn btn-primary dx-cms-info-btn-send pull-right" type="button">{{ trans('wf_info_task.btn_send') }}</button>
                                </div>
                                
                            </div>
                            <div class="col-md-4">
                                <div class="portlet box grey-cascade dx-cms-info-task-portlet" style="margin-top: 18px;">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            Nodots informācijai <span class="badge badge-info dx-cms-info-task-count"> {{ count($info_tasks) }} </span></div>                                            
                                    </div>
                                    <div class="portlet-body dx-cms-info-list">
                                        <div class="scroller" style="height: 175px;" data-always-visible="0" data-rail-visible="1">
                                            @if (count($info_tasks) ==0)
                                                <p class="dx-cms-no-info">Dokuments vēl nav nodots informācijai nevienam darbiniekam</p>
                                            @else
                                                @foreach($info_tasks as $info)
                                                    <p>{{ $info->display_name }}
                                                    @if ($info->task_closed_time)
                                                    &nbsp; <font color="green"<i class="fa fa-check-circle-o" title="Iepazinās: {{ short_date($info->task_closed_time) }}"></i></font>
                                                    @endif
                                                    </p>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>                                
                                
                            </div>
                        </div>
                        
                        
                    </div>
                    <div class='modal-footer'>                        
                        
                        <button type='button' class='btn btn-white' data-dismiss='modal'><i class='fa fa-sign-out'></i>&nbsp;{{ trans('wf_info_task.btn_close') }}</button>                            
                    </div>
            </div>
    </div>
</div>