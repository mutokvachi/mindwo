<?php

$chatUsers = App\Models\Chat\User::where('user_id', Auth::user()->id)
    ->where(function ($query) {
                $query->where('has_seen', '<>', 1)
                      ->orWhereNull('has_seen');
          })
    ->get();

?>
@if($chatUsers && count($chatUsers) > 0)
<li class="dropdown dx-chat-notif-dropdown">
    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" title="New messages">
        <i class="fa fa-comments-o"></i> 
        <div class="badge badge-warning dx-chat-notif-count" >{{ count($chatUsers) }}</div>
    </a>    
    <ul class="dropdown-menu dropdown-menu-default dx-sub-menu-right">
        @foreach($chatUsers as $chatUser)
        <?php $chat = $chatUser->chat; ?>
        <li>
            <a href="javascript:;" onclick="(function(self) { 
                    $(self).parent('li').remove(); 

                    var count = $('.dx-chat-notif-count').html() - 1;

                    if(count <= 0){
                        $('.dx-chat-notif-dropdown').hide();
                    } else {
                        $('.dx-chat-notif-count').html(count);
                    }
                    
                    open_form('form', {{ $chat->item_id }}, {{ $chat->list_id }}, 0, 0, '', 0, '');                     
                })(this)">
                <i class="fa fa-comment-o" style="font-size:medium"></i> {{ $chat->list->list_title }} <span style="font-style:italic;">(#{{ $chat->id }})</span> 
            </a>
        </li>
        @endforeach
    </ul>
</li>
@endif