<button type='button' 
    class='btn btn-white dx-form-chat-btn-open' 
    data-dx-list-id="{{ $list_id }}"
    data-dx-item-id="{{ $item_id }}"
    data-dx-form-title="{{ $form_title }}"
    data-dx-chat-refresh-time="{{ Config::get('dx.chat_refresh_rate', 1) }}">
    <i class="fa fa-comments-o"></i> {{ trans('form.chat.chat') }}
</button>