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
                
                <div class="input-group dx-form-chat-user-field" style="width: 100%;"
                        data-is-init = "0"
                        data-field-id="-1" 
                        data-rel-list-id = "259"
                        data-rel-field-id = "1633"
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
                        class='form-control select2-remote required'
                        style="width: 100%;"/>
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
<div class="dx-form-chat-panel col-lg-offset-9 col-md-offset-7 col-lg-3 col-md-5"
    style="position: fixed; bottom: 0; right: 0; display:none; background-color: white; padding: 0; border: solid 1px gray;">
    <div class="portlet light" style='margin-bottom: 0;'>
        <div class="portlet-title">
            <div class="actions">
                <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;" data-original-title="" title=""> </a>
                <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default dx-form-chat-btn-close"><i class="fa fa-close"></i></a>
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
        <div class="portlet-body" style="height: auto;">
            <div class='dx-form-chat-content-container' style='overflow-y:scroll; height: 40vh; margin-bottom: 55px;'>
                <ul class="chats dx-form-chat-content">
                </ul>
            </div>
            <div class="chat-form dx-form-chat-form" style=''>
                <div class="input-cont">
                    <input class='dx-form-chat-input-id' type='hidden'>
                    <textarea rows="2" class="form-control dx-form-chat-input-text" type="text" placeholder="{{ trans('form.chat.type_hint') }}" style="max-height: 20vh;"></textarea>
                </div>
                <div class="btn-cont dx-form-chat-btn-send" style="left: 5px;">
                    <a href="javascript:void(0);" class="btn blue btn-icon-only">
                        <i class="fa fa-send icon-white"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!--<div class="dx-form-chat-panel"style="position: fixed; bottom: 0; right: 0; width: 100%;">   
    <div class='col-lg-offset-9 col-md-offset-7 col-lg-3 col-md-5' style='background-color: white; padding-bottom: 10px; padding-top:10px;'>   
        <div>
            <button></button>
        </div>     
        <div style='overflow-y:scroll; height: 30vh; margin-bottom: 55px;'>        
            <ul class="chats dx-form-chat-content">
                <li>
                    <div class="message">
                        <span class="arrow"> </span>
                        <a href="" class="name"> Janis </a>
                        <span class="datetime"> 10.06.2017 </span>                            
                        <span class="body dx-form-chat-msg-body">Test</span>
                    </div>                    
                </li>
                <li>
                    <div class="message">
                        <span class="arrow"> </span>
                        <a href="" class="name"> Janis </a>
                        <span class="datetime"> 10.06.2017 </span>                            
                        <span class="body dx-form-chat-msg-body">Test 123</span>
                    </div>
                </li>
            </ul>
        </div>
        <div class="chat-form dx-form-chat-form" style='position: absolute; bottom: 0; right: 0; width:100%;'>
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
</div> -->