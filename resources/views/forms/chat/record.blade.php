<li class="{{ $msg->created_user_id == Auth::user()->id ? 'out' : 'in' }}">
    <!--<img class="avatar dx-form-chat-msg-avatar" 
         alt=""  
         src="{{Request::root()}}/{{ $msg->createdUser->picture_guid ? 'img/' .$msg->createdUser->picture_guid : 'assets/global/avatars/default_avatar_small.jpg' }}"> -->
    <div class="message">
        <span class="arrow"> </span>
        <a href="{{Request::root() . '/employee/profile/' . $msg->createdUser->id }}" class="name"> {{ $msg->createdUser->display_name }} </a>
        <span class="datetime" style='font-size: x-small;'> {{ $msg->created_time->format(config('dx.txt_datetime_format')) }} </span>
        @if($msg->created_time != $msg->modified_time)
        <span class="dx-form-chat-msg-modified" style='font-size: x-small;'>
            &nbsp;({{ trans('form.chat.modified')}}&nbsp;
            <span class="datetime" style='font-size: x-small;'> {{ $msg->modified_time->format(config('dx.txt_datetime_format')) }}</span>)
        </span>
        @endif      
        <span class="body dx-form-chat-msg-body">{{ $msg->message }}</span>
    </div>
</li>
