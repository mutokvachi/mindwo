@if(count($users) > 0)
    @foreach($users as $user)
        <div class="row dx-form-chat-user-list-row">
            <div class="col-sm-10">
                <img class='dx-form-chat-avatar dx-form-chat-avatar-sm' 
                    alt='' 
                    src="{{Request::root()}}/{{ $user->picture_guid ? 'img/' . $user->picture_guid : 'assets/global/avatars/default_avatar_small.jpg' }}">
                <a href="{{Request::root() . config('dx.employee_profile_page_url') . $user->id }}">
                    {{ $user['display_name'] . ' (' . $user['position_title'] . ($user['department'] ? ', ' . $user['department']->title : '') . ')'}}
                </a>
            </div>
            <div class="col-sm-2">
                <button type="button" class="btn btn-xs btn-circle dx-form-chat-btn-del-user" title='{{ trans("form.chat.btn_del_user") }}' data-user-id='{{ $user->id }}'>
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
    @endforeach
@else
    <div class="row dx-form-chat-user-list-row">
        <div class="col-sm-12">
            {{ trans('form.chat.e_no_users') }}
        </div>
    </div>
@endif