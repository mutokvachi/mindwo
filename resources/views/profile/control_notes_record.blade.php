@if ($note->modifiedUser)
<li class="{{ $note->modified_user_id == Auth::user()->id ? 'out' : 'in' }}" style='{{ $is_new ? 'display:none' : '' }}'>
    <img class="avatar dx-emp-notes-avatar" 
         alt=""  
         src="{{Request::root()}}/{{ $note->modifiedUser->picture_guid ? 'img/' .$note->modifiedUser->picture_guid : 'assets/global/avatars/default_avatar_small.jpg' }}">
    <div class="message">
        <span class="arrow"> </span>
        <a href="{{Request::root() . '/employee/profile/' . $note->modifiedUser->id }}" class="name"> {{ $note->modifiedUser->display_name }} </a>
        <span class="datetime"> {{ $note->modified_time->format(config('dx.txt_datetime_format')) }} </span>
        <span class="body dx-emp-notes-edit-body">{{ $note->note }}</span>
        @if (($has_manager_access && Auth::user()->id == $note->modified_user_id) || $has_hr_access)
        <div class='dx-emp-notes-btn-link-panel'>
            <input class='dx-emp-notes-edit-id' type="hidden" value='{{ $note->id }}'>
            <span>
                <a class='dx-emp-notes-btn-link dx-emp-notes-btn-link-edit' href='javascript:void(0);'>{{ trans('form.btn_edit') }}</a>&nbsp;&nbsp;
            </span>
            <span>
                <a class='dx-emp-notes-btn-link dx-emp-notes-btn-link-delete' href='javascript:void(0);'>{{ trans('form.btn_delete') }}</a>
            </span>
        </div>
        @endif
    </div>
</li>
@endif
