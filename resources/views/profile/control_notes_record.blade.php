@if ($note->createdUser && (($has_manager_access && !$has_hr_access && !$note->is_hr) || $has_hr_access) )
<li class="{{ $note->created_user_id == Auth::user()->id ? 'out' : 'in' }}" style='{{ $is_new ? 'display:none' : '' }}'>
    <img class="avatar dx-emp-notes-avatar" 
         alt=""  
         src="{{Request::root()}}/{{ $note->createdUser->picture_guid ? 'img/' .$note->createdUser->picture_guid : 'assets/global/avatars/default_avatar_small.jpg' }}">
    <div class="message">
        <span class="arrow"> </span>
        <a href="{{Request::root() . '/employee/profile/' . $note->createdUser->id }}" class="name"> {{ $note->createdUser->display_name }} </a>
        <span class="datetime"> {{ $note->created_time->format(config('dx.txt_datetime_format')) }} </span>
        @if($note->created_time != $note->modified_time)
        <span class="dx-emp-notes-modified">
        &nbsp;({{ trans('employee.notes.modified')}}&nbsp;
        <a href="{{Request::root() . '/employee/profile/' . $note->modifiedUser->id }}" class="name">{{ $note->modifiedUser->display_name }} </a>
        <span class="datetime"> {{ $note->modified_time->format(config('dx.txt_datetime_format')) }}</span>)
        </span>
        @endif
        <span class="body dx-emp-notes-edit-body">{{ $note->note }}</span>
        @if (($has_manager_access && Auth::user()->id == $note->created_user_id) || $has_hr_access)
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
