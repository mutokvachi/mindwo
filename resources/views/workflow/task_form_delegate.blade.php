<div class='modal fade' aria-hidden='true' id='form_delegate_{{ $frm_uniq_id }}' role='dialog' aria-hidden='true' data-backdrop='static' style="z-index: 999999;" 
     dx_is_init="0"
     dx_task_id="{{ $item_id }}"
     dx_date_format="{{ $date_format }}"
     dx_frm_uniq_id="{{ $frm_uniq_id }}"
     dx_grid_htm_id="{{ $grid_htm_id }}"
     data-is-any-delegate="{{ (isset($task_row) && $task_row) ? $task_row->is_any_delegate : 0 }}"
     data-subordinates ='[
     @foreach($employees as $key => $item)
        @if ($key > 0)
            ,
        @endif
        {"id":{{ $item->id }},"text":"{{ $item->display_name }}", "key":"{{$key}}"}
    @endforeach]'
     >
    <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
                    @include('elements.form_header',['form_title' => trans('task_delegate.form_title'), 'badge' => ''])
                   				
                    <div class='modal-body' style="overflow-y: auto; max-height: 500px;">
                        
                        <div class="tabbable-custom nav-justified" style='margin-left: 13px; margin-top: 15px;'>
                            <ul class="nav nav-tabs nav-justified">
                                <li class="dx-tasks-create active">
                                    <a href="#tab_1_1_1" data-toggle="tab" aria-expanded="true"> {{ trans('task_form.tab_new_deleg_task') }} </a>
                                </li>
                                <li class="dx-tasks-list">
                                    <a href="#tab_1_1_2" data-toggle="tab" aria-expanded="false"> {{ trans('task_form.tab_list_deleg_tasks') }} <span class="badge badge-info dx-task-count">0</span></a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane dx-tab-new-task active" id="tab_1_1_1">
                                    <div class='row' style='padding: 20px;'>
                                        <form class="form-horizontal" method='POST' data-toggle="validator"> 
                                            <div>
                                                <div class='form-group dx-form-field-line'>
                                                    <label class='col-lg-4 control-label'>{{ trans('task_delegate.lbl_employee') }} <span style="color: red"> *</span></label>
                                                    <div class='col-lg-8'>
                                                        <div class="input-group" style="width: 100%;">                                        
                                                            <input type='text' name = 'deleg_empl_txt' value = '' class='form-control' required />
                                                        </div>
                                                    </div>    
                                                </div>
                                            </div>
                                            <div>
                                                <div class='form-group has-feedback dx-form-field-line'>
                                                    <label class='col-lg-4 control-label'>{{ trans('task_delegate.lbl_task_descr') }} <span style="color: red"> *</span></label>
                                                    <div class='col-lg-8'>
                                                        <textarea class="form-control" name = "task_txt"  rows="4" maxlength="4000" required></textarea>
                                                        <span class="glyphicon form-control-feedback" aria-hidden="true" style="margin-right: 10px;"></span>     
                                                        <div class="help-block with-errors"></div>
                                                    </div>    
                                                </div>
                                            </div>
                                            <div>
                                                 <div class='form-group has-feedback dx-form-field-line'>
                                                    <label class='col-lg-4 control-label'><i class="fa fa-question-circle dx-form-help-popup" style="cursor: help;" title="{{ trans('task_delegate.hint_due') }}"></i> {{ trans('task_delegate.lbl_due') }} <span style="color: red"> *</span></label>
                                                    <div class='col-lg-8'>
                                                        <div class='input-group dx-cms-date-field' style="width: 200px;">
                                                            <span class='input-group-btn'>
                                                                <button type='button' class='btn btn-white' style="border: 1px solid #c2cad8!important; margin-right: -2px!important;"><i class='fa fa-calendar'></i></button>
                                                            </span>
                                                            <input class='form-control' type=text name = 'due_date' value = '{{ (isset($task_row) && $task_row) ? short_date($task_row->due_date) : '' }}' required />
                                                        </div>
                                                        <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                    </div>
                                                 </div>
                                            </div>
                                        </form>						
                                    </div>
                                </div>
                                <div class="tab-pane dx-tab-tasks" id="tab_1_1_2"></div>
                            </div>
                        </div>                        
                            
                    </div>
                    <div class='modal-footer'>
                        <button class="btn btn-primary dx-cms-task-btn-delegate" type="button" title="{{ trans('task_delegate.hint_delegate') }}">{{ trans('task_delegate.btn_delegate') }}</button>&nbsp;
                        <button type='button' class='btn btn-white' data-dismiss='modal'><i class='fa fa-sign-out'></i>&nbsp;{{ trans('task_delegate.btn_close') }}</button>                            
                    </div>
            </div>
    </div>
</div>