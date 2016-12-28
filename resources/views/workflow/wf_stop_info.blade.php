<li class="out">
    <img class="avatar" alt="" src="{{Request::root()}}/{{ \App\Libraries\Helper::getEmployeeAvatarBig($wf_info->picture_guid) }}">
    <div class="message">
        <span class="arrow"> </span>
        @if ($profile_url)
            <a class="name"  href='{{Request::root()}}{{ $profile_url}}{{ $wf_info->end_user_id }}' target="_blank">
                {{ $wf_info->display_name }}
            </a>
        @else
            <b>{{ $wf_info->display_name }}</b>
        @endif
        <br>
        <br>
        <span class="datetime"> {{ trans('task_form.lbl_stoped_workflow') }}</span>
        <span class="body"> {{ long_date($wf_info->task_closed_time) }}
            <br>
            <br>
            <i>{{ $wf_info->task_comment }}</i>
        </span>
    </div>
</li>