<ul class="chats" style="margin-left: 20px!important; margin-right: 20px!important;">
    @foreach($tasks as $key => $task)
        <li class="{{ ($task->task_status_id == 3) ? 'out' : 'in' }}">
            <img class="avatar" alt="" src="{{Request::root()}}/{{ \App\Libraries\Helper::getEmployeeAvatarBig($task->performer_picture) }}">
            <div class="message">
                <span class="arrow"> </span>
                @if ($profile_url)
                    <a class="name"  href='{{Request::root()}}{{ $profile_url}}{{ $task->task_employee_id }}' target="_blank">
                        {{ $task->performer_employee }}
                    </a>
                @else
                    <b>{{ $task->performer_employee }}</b>
                @endif
                <br>
                <br>
                <span class="datetime"> {{ trans('task_form.lbl_task') }}: {{ $task->task_type }} </span>
                <span class="body"> {{ trans('task_form.lbl_status') }}: <b>{{ $task->task_status }}</b>
                    <br>
                    {{ long_date($task->task_created_time) }}
                    @if ($task->task_closed_time)
                     - {{ long_date($task->task_closed_time) }}
                    @endif
                @if ($task->task_comment)
                <br>
                <br>
                <i>{{ $task->task_comment }}</i>
                @endif
                </span>
            </div>
        </li>
    @endforeach                                                
</ul>

