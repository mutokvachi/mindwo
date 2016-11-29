<div id="dx-emp-notes-panel" class='row'
     data-has_hr_access="{{ $has_hr_access }}"
     data-has_manager_access="{{ $has_manager_access }}">   
    <div class='col-lg-8 col-md-12'>
        <div class="chat-form dx-emp-notes-chat-form">
            <div class="input-cont">
                <input class='dx-emp-notes-input-id' type='hidden'>
                <input class="form-control dx-emp-notes-input-text" type="text" placeholder="{{ trans('employee.notes.type_hint') }}"> </div>
            <div class="btn-cont dx-emp-notes-btn">
                <span class="arrow"> </span>
                <a href="javascript:void(0);" class="btn blue icn-only">
                    <i class="fa fa-check icon-white"></i>
                </a>
            </div>
        </div>
        <div>
            <ul class="chats dx-emp-notes-chat">
                @foreach ($user->notes()->orderBy('modified_time', 'desc')->get() as $note)
                @include('profile.control_notes_record', ['is_new' => false])
                @endforeach
            </ul>
        </div>
    </div>
</div>