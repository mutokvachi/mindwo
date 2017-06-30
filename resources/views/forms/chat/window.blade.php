@if(config('dx.is_chat_enabled', false))
<div class="modal dx-form-chat-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal"> <i class="fa fa-sign-out"></i> {{ trans('form.btn_close') }}</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal dx-form-chat-user-add-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <?php

                $dx_empl_list_id = Config::get('dx.employee_list_id', '');

                $dx_empl_name_field_id = DB::table('dx_lists_fields')->where('list_id', $dx_empl_list_id)
                    ->where('db_name', Config::get('dx.empl_fields.empl_name', ''))
                    ->first()
                    ->id;

                ?>
                
                <div class="input-group dx-form-chat-user-field"
                        data-is-init = "0"
                        data-field-id="-1" 
                        data-rel-list-id = "{{ $dx_empl_list_id }}"
                        data-rel-field-id = "{{ $dx_empl_name_field_id }}"
                        data-item-field = "user_id"
                        data-trans-search = "{{ trans("fields.search_record") }}"
                        data-rel-view_id = "0"
                        data-rel-formula-field = ""
                        data-item-value="" 
                        data-item-text="" 
                        data-is-profile = '1'
                        data-profile-url = '{{ Request::root() }}{{ Config::get('dx.employee_profile_page_url', '') }}'
                        data-is-manual-init = "0"
                        data-min-length = "3"> 
                    <input class="dx-auto-input-id dx-form-chat-input-save-user" type="hidden" value = '' />
                    <input class="dx-auto-input-select2 dx-form-chat-input-save-user-title" 
                        type='text' 
                        value = '' 
                        class='form-control select2-remote required' />
                    <span class="input-group-btn" style="display:none">
                        <button class="dx-rel-id-del-btn" type="button"></button>
                        <button class="dx-rel-id-add-btn" type="button"></button>
                    </span>  
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary dx-form-chat-btn-save-user" >{{ trans('form.btn_save') }}</button>
                <button type="button" class="btn btn-white" data-dismiss="modal"> <i class="fa fa-undo"></i> {{ trans('form.btn_cancel') }}</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="dx-form-chat-panel col-lg-offset-9 col-md-offset-7 col-lg-3 col-md-5">
    <div class="portlet light">
        <div class="portlet-title">
            <div class="actions">
                &nbsp<a href="javascript:;" class="btn btn-circle btn-icon-only btn-default dx-form-chat-btn-close"><i class="fa fa-close"></i></a>
            </div>
            <div class="caption">
                <i class="fa fa-comments-o"></i>
                <span class="caption-subject bold uppercase">{{ trans('form.chat.chat') }}</span></br>
                <span class="caption-helper"></span>
            </div>
            <div class="actions">
                <a href="javascript:;" class="btn btn-circle dx-form-chat-btn-users" ><i class="fa fa-users"></i> {{ trans('form.chat.users') }} </a>
                <a href="javascript:;" class="btn btn-circle dx-form-chat-btn-add-user"><i class="fa fa-plus"></i> {{ trans('form.chat.btn_add_user') }} </a>
            </div>
        </div>
        <div class="portlet-body">
            <div class="dx-form-chat-content-err" >
                <div class="note note-danger">
                    <p> {{ trans('form.chat.e_data_not_retrieved') }} </p>
                </div>
                <button type="button" class="btn btn-md btn-primary dx-form-chat-btn-refresh"> <i class="fa fa-refresh"></i> {{ trans('form.chat.btn_try_again') }}</button>
            </div>            
            <div class='dx-form-chat-content-container'>
                <ul class="chats dx-form-chat-content">
                </ul>
                <div class="dx-form-chat-progress">
                    <p>
                        <label class="dx-form-chat-progress-label"></label>                        
                    </p>
                    <div class="progress progress-striped active">
                        <div class="progress-bar progress-bar-success dx-form-chat-progress-bar" 
                             role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                            <span class="sr-only"> </span>
                        </div>                        
                    </div>   
                    <button type="button" class="btn btn-xs btn-white dx-form-chat-btn-file-cancel" data-dismiss="modal"> <i class="fa fa-remove"></i> {{ trans('form.btn_cancel') }}</button> 
                </div>
            </div>
            <div class="chat-form dx-form-chat-form">
                <div class="dx-form-chat-form-btns">
                    <a href="javascript:void(0);" class="btn blue btn-icon-only dx-form-chat-btn-send" title="{{ trans('form.chat.btn_send_msg') }}">
                        <i class="fa fa-send icon-white"></i>
                    </a></br>
                    <input type="file" class="dx-form-chat-file-input" multiple>
                    <a href="javascript:void(0);" class="btn btn-default btn-icon-only dx-form-chat-btn-file" title="{{ trans('form.chat.btn_send_file') }}">
                        <i class="fa fa-paperclip"></i>
                    </a>
                </div>
                <div class="dx-form-chat-form-inputs">
                    <input class='dx-form-chat-input-id' type='hidden'>
                    <textarea rows="2" class="form-control dx-form-chat-input-text" type="text" placeholder="{{ trans('form.chat.type_hint') }}"></textarea>
                </div>                
            </div>            
        </div>
    </div>
</div>
@endif