<div class="mt-action animated bounceInUp" data-task-id="{{ $task->id }}" data-task-list-id = "{{ $self->task_list_id }}" data-is-any-delegate="{{ $task->is_any_delegate }}">
    <div class="mt-action-img">
        @if ($task->item_empl_id)
        <a href="{{ url('employee/profile/' . $task->item_empl_id)}}"><img src="{{Request::root()}}/{{ \App\Libraries\Helper::getEmployeeAvatarBig($task->picture_guid) }}" title="{{ $task->item_employee }}" style="width: 50px; height: 50px; border-radius: 50%!important;"></a>
        @else
            <a class='btn btn-circle outline' title="{{ trans('task_widget.hint_view_file') }}" href="{{ url('download_first_file_'. $task->item_id . '_' . $task->list_id) }}"><i class='fa fa-file-o'></i></a>            
        @endif
    </div>
    <div class="mt-action-body">
        <div class="mt-action-row">
            <div class="mt-action-info ">
                <div class="mt-action-icon ">
                    <i class="icon-magnet"></i>
                </div>
                <div class="mt-action-details ">
                    <span class="mt-action-author">{{ $task->list_title }}: <a href="javascript:;" class='dx-form-link' data-item-id="{{ $task->item_id }}" data-list-id="{{ $task->list_id }}" data-is-edit="{{ ($task->task_type_id == 3) ? 1 : 0 }}" title="{{ trans('task_widget.hint_view_form') }}">{{ ($task->item_empl_id) ? $task->item_employee : $task->item_reg_nr }}</a></span>&nbsp;
                    <a href="{{Request::root()}}/web/viewer.html?file={{Request::root()}}/get_form_pdf_{{ $task->item_id }}_{{ $task->list_id }}.pdf" target="_blank" title="{{ trans('task_widget.hint_form_in_pdf') }}"><i class="fa fa-file-pdf-o"></i></a>
                    <p class="mt-action-desc">{{ $task->item_info }}</p>
                    <div class="mt-action-datetime"
                    @if (!$task->due_date || $task->task_type_id == 6)
                        style="text-align: left!important;"
                    @endif
                    >
                        <span class="mt-action-date"><a href="javascript:;" class="dx-task-link" title="{{ trans('task_widget.hint_view_task') }}">{{ $task->task_type }}</a></span>
                        @if ($task->due_date && $task->task_type_id != 6)
                        <span class="mt-action-date"> | {{ trans('task_widget.lbl_due') }}
                        <span class="mt-action-dot {{ ($task->days_left < 0) ? 'bg-red' : 'bg-default' }}" title="{{ $task->days_left }}"></span>
                        <span class="mt-action-time dx-task-due">{{ short_date($task->due_date) }}</span>
                        @endif
                    </div>
                    @if ($task->task_type_id != 6)
                        <p class="mt-action-desc">
                            <span class="dx-task-status">{{ $task->task_status }}</span>
                            @if ($task->days_left == 0)
                                | {{ trans('task_widget.hint_due_today') }}
                            @endif

                            @if ($task->days_left < 0)
                                | {{ sprintf(trans('task_widget.hint_overdue_days'), abs($task->days_left)) }}
                            @endif
                        </p>
                    @else
                        <p class="mt-action-desc">
                            {{ $task->task_details }}
                        </p>
                    @endif
                    
                    @if( $task->is_history)
                        <p class="mt-action-desc">
                            <a href="javascript:;" class="dx-task-history" data-list-id="{{ $task->list_id }}" data-item-id="{{ $task->item_id }}" title="{{ trans('task_widget.hint_history') }}">{{ trans('task_widget.link_history') }}</a>
                        </p>
                    @endif
                    
                </div>
            </div>
            @if (!$task->task_closed_time)
                <div class="mt-action-buttons">
                    @if ($task->task_type_id == 6)
                        <button type="button" class="btn btn-outline green btn-sm pull-right btn-circle dx-btn-info" title='{{ trans('task_widget.hint_read') }}'><i class='fa fa-check'></i></button>
                    @else
                    <div class="btn-group btn-group-circle pull-right">
                        <button type="button" class="btn btn-outline green btn-sm dx-btn-yes" title='{{ ($task->task_type_id == 2) ? trans('task_widget.hint_do') : trans('task_widget.hint_approve') }}'><i class='fa fa-check'></i></button>
                        @if ($self->is_subordinates || $task->is_any_delegate)
                            <button type="button" class="btn btn-outline grey-gallery btn-sm dx-btn-deleg" title='{{ trans('task_widget.hint_delegate') }}' data-details="{{ $task->task_details }}" data-task-type="{{ $task->task_type }}"><i class='fa fa-code-fork'></i></button>                                                                                            
                        @endif
                        <button type="button" class="btn btn-outline red btn-sm dx-btn-no" title='{{ trans('task_widget.hint_reject') }}'><i class='fa fa-times'></i></button>
                    </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

