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
                <a href="javascript:;" class="btn btn-circle"><i class="fa fa-users"></i> Users </a>
                <a href="javascript:;" class="btn btn-circle"><i class="fa fa-plus"></i> Add user </a>
            </div>
        </div>
        <div class="portlet-body" style="height: auto;">
            <div style='overflow-y:scroll; height: 30vh; margin-bottom: 55px;'>        
                <ul class="chats dx-form-chat-content">
                </ul>
            </div>
            <div class="chat-form dx-form-chat-form" style=''>
                <div class="input-cont">
                    <input class='dx-form-chat-input-id' type='hidden'>
                    <textarea rows="2" class="form-control dx-form-chat-input-text" type="text" placeholder="{{ trans('form.chat.type_hint') }}" style="max-height: 20vh;"></textarea>
                </div>
                <div class="btn-cont dx-form-chat-btn-send" style="left: 5px;">
                    <a href="javascript:void(0);" class="btn blue icn-only">
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