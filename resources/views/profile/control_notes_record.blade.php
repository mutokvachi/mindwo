@if ($note->createdUser && (($has_manager_access && !$has_hr_access && !$note->is_hr) || $has_hr_access) )
<li class="{{ $note->created_user_id == Auth::user()->id ? 'out' : 'in' }}" style='{{ $is_new ? 'display:none' : '' }}'>
    <img class="avatar dx-emp-notes-avatar" 
         alt=""  
         src="{{Request::root()}}/{{ $note->createdUser->picture_guid ? 'img/' .$note->createdUser->picture_guid : 'assets/global/avatars/default_avatar_small.jpg' }}">
    <div class="message">
        <span class="arrow"> </span>
        <a href="{{Request::root() . config('dx.employee_profile_page_url') . $note->createdUser->id }}" class="name"> {{ $note->createdUser->display_name }} </a>
        <span class="datetime"> {{ $note->created_time->format(config('dx.txt_datetime_format')) }} </span>
        @if($note->created_time != $note->modified_time)
        <span class="dx-emp-notes-modified">
            &nbsp;({{ trans('empl_profile.notes.modified')}}&nbsp;
            <a href="{{Request::root() . config('dx.employee_profile_page_url') . $note->modifiedUser->id }}" class="name">{{ $note->modifiedUser->display_name }} </a>
            <span class="datetime"> {{ $note->modified_time->format(config('dx.txt_datetime_format')) }}</span>)
        </span>
        @endif        
        <span class="body dx-emp-notes-edit-body">{{ $note->note }}</span>
        <div class='dx-emp-notes-btn-link-panel'>
            <a  href="javascript:;" 
                class="popovers dx-emp-notes-btn-whosee dx-emp-notes-btn-link"              
                data-html="true" 
                data-container="body" 
                data-trigger="hover"
                data-placement="bottom" 
                data-content="
                @foreach ($users_who_see as $user_who_see)
                @if(($user_who_see['is_manager'] === true && $user_who_see['id'] == $note->created_user_id) || $user_who_see['is_manager'] === false)
                <div class='dx-emp-notes-who_see'>
                <img class='dx-emp-notes-avatar dx-emp-notes-avatar-sm' 
                alt='' 
                src=&quot;{{Request::root()}}/{{ $user_who_see['picture_guid'] ? 'img/' .$user_who_see['picture_guid'] : 'assets/global/avatars/default_avatar_small.jpg' }}&quot;>
                {{ $user_who_see['display_name'] . ' (' . $user_who_see['position_title'] . ($user_who_see['department'] ? ', ' . $user_who_see['department'] : '') . ')'}}
                </div>
                @endif
                @endforeach
                " 
                data-original-title="<b>{{ trans('empl_profile.notes.who_can_see')}}</b>">
                <i class="fa fa-eye"></i>&nbsp;&nbsp;
            </a>
            @if (($has_manager_access && Auth::user()->id == $note->created_user_id) || $has_hr_access)
            <input class='dx-emp-notes-edit-id' type="hidden" value='{{ $note->id }}'>
            <span>
                <a class='dx-emp-notes-btn-link dx-emp-notes-btn-link-edit' href='javascript:void(0);'>{{ trans('form.btn_edit') }}</a>&nbsp;&nbsp;
            </span>
            <span>
                <a class='dx-emp-notes-btn-link dx-emp-notes-btn-link-delete' href='javascript:void(0);'>{{ trans('form.btn_delete') }}</a>
            </span>
            @endif            
        </div>
    </div>
</li>
@endif
