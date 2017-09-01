<div class='modal fade dx-popup-modal dx-group-popup' aria-hidden='true' role='dialog' aria-hidden='true' data-backdrop='static' style="z-index: 999999;">
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
            @include('elements.form_header',['form_title' => trans('calendar.complect.group_popup_title'), 'badge' => '', 'form_icon' => '<i class="fa fa-users"></i>'])

            <div class='modal-body' style="background-color: #e4e8e7!important;">
                
            </div>

            <div class='modal-footer' style="border-top: 1px solid lightgray;">                
                <button type='button' class='btn btn-white dx-new-empl-btn pull-left' data-is-init="0"><i class="fa fa-user"></i> {{ trans('calendar.complect.btn_new_empl') }}</button>    
                <button type='button' class='btn btn-primary dx-import-btn pull-left'><i class="fa fa-file-excel-o"></i> {{ trans('calendar.complect.btn_import') }}</button>                                          
                <button type='button' class='btn btn-white dx-cancel-btn' data-dismiss='modal'>{{ trans('form.btn_close') }}</button>                            
            </div>
        </div>
    </div>
</div>