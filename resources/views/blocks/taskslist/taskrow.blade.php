<div class="mt-action">
    <div class="mt-action-img">
        <button class='btn btn-circle outline' title="{{ trans('task_widget.hint_view_file') }}"><i class='fa fa-file-o'></i></button>
    </div>
    <div class="mt-action-body">
        <div class="mt-action-row">
            <div class="mt-action-info ">
                <div class="mt-action-icon ">
                    <i class="icon-magnet"></i>
                </div>
                <div class="mt-action-details ">
                    <span class="mt-action-author">{{ $task->list_title }}: <a href="javascript:;" title="{{ trans('task_widget.hint_view_form') }}">{{ $task->item_reg_nr }}</a></span>
                    <p class="mt-action-desc">{{ $task->item_info }}</p>
                    <div class="mt-action-datetime"
                    @if (!$task->due_date)
                        style="text-align: left!important;"
                    @endif
                    >
                        <span class="mt-action-date"><a href="javascript:;" title="{{ trans('task_widget.hint_view_task') }}">{{ $task->task_type }}</a></span>
                        @if ($task->due_date)
                        <span class="mt-action-date"> | {{ trans('task_widget.lbl_due') }}
                        <span class="mt-action-dot bg-red"></span>
                        <span class="mt-action-time">{{ short_date($task->due_date) }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-action-buttons">                                                                    
                <div class="btn-group btn-group-circle">
                    <button type="button" class="btn btn-outline green btn-sm" title='{{ trans('task_widget.hint_approve') }}'><i class='fa fa-check'></i></button>
                    @if ($self->is_subordinates)
                        <button type="button" class="btn btn-outline grey-gallery btn-sm" title='{{ trans('task_widget.hint_delegate') }}'><i class='fa fa-code-fork'></i></button>                                                                                            
                    @endif
                    <button type="button" class="btn btn-outline red btn-sm" title='{{ trans('task_widget.hint_reject') }}'><i class='fa fa-times'></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

