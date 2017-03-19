@if (count($tasks) > 0)
<div class="table-scrollable">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th> {{ trans('task_form.lbl_task_performer') }} </th>
                <th> {{ trans('task_form.lbl_task_created') }} </th>
                <th> {{ trans('task_form.lbl_due_date') }} </th>
                <th> {{ trans('task_form.lbl_task') }}</th>
                <th> {{ trans('task_form.lbl_status') }} </th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $task)
            <tr>
                <td> {{ $task->performer_name }} </td>
                <td> {{ long_date($task->task_created_time) }} </td>
                <td> {{ short_date($task->due_date) }} </td>
                <td> {{ $task->task_details }} </td>
                <td>
                    <span class="label label-sm dx-task-status" style='background-color:  {{ ($task->color) ? $task->color : 'silver' }}'
                       @if ($task->task_comment)
                            title='{{ $task->task_comment }}'
                       @endif
                    > {{ $task->task_status }} </span>
                    @if ($task->is_revocable)
                        &nbsp;<a href="javascript:;" class="dx-revoke-task" title="{{ trans('task_form.revoke_hint') }}" data-task-id="{{ $task->task_id }}"><i class="fa fa-undo"></i></a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
    <div class="alert alert-info" role="alert" style='margin: 20px;'>{{ trans('task_form.info_no_delegated_tasks') }}</div>
@endif