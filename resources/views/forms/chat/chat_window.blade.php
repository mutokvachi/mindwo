<div>
    <div id="dx-form-chat-panel" class='row'>   
        <div class='col-lg-8 col-md-12'>        
            <div>
                <ul class="chats dx-form-chat-content">
                    @foreach ($user->notes()->orderBy('modified_time', 'desc')->get() as $note)
                    @include('profile.control_notes_record', ['is_new' => false])
                    @endforeach
                </ul>
            </div>
            <div class="chat-form dx-form-chat-form">
                <div class="input-cont">
                    <input class='dx-form-chat-input-id' type='hidden'>
                    <input class="form-control dx-form-chat-input-text" type="text" placeholder="{{ trans('form.chat.type_hint') }}"> </div>
                <div class="btn-cont dx-form-chat-btn">
                    <span class="arrow"> </span>
                    <a href="javascript:void(0);" class="btn blue icn-only">
                        <i class="fa fa-check icon-white"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>