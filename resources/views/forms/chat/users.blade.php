<div class="mt-element-list">
    <div class="mt-list-container list-simple">
        <ul>
            @if(count($users) > 0)
                @foreach($users as $user)
                    <li class="mt-list-item">
                        <img class='dx-form-chat-avatar dx-form-chat-avatar-sm' 
                            alt='' 
                            src"{{Request::root()}}/{{ $user['picture_guid'] ? 'img/' .$user['picture_guid'] : 'assets/global/avatars/default_avatar_small.jpg' }}">
                        {{ $user['display_name'] . ' (' . $user['position_title'] . ($user['department'] ? ', ' . $user['department'] : '') . ')'}}
                    </li>
                @endforeach
            @else
                <li class="mt-list-item">
                    {{ trans('form.chat.e_no_users') }}
                </li>
            @endif
        </ul>
    </div>
</div>