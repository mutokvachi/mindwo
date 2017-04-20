<div class='modal fade dx-popup-modal' id='agenda_set_popup' aria-hidden='true' role='dialog' aria-hidden='true' data-backdrop='static' style="z-index: 999999;">
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
            @include('elements.form_header',['form_title' => 'Darba kārtības iestatīšana', 'badge' => '', 'form_icon' => '<i class="fa fa-list"></i>'])

            <div class='modal-body' style="overflow-y: auto; max-height: 500px;">
                
            </div>
            <div class='modal-footer dx-view-popup-footer'>
                <button type='button' class='btn btn-primary dx-view-btn-save'>{{ trans('form.btn_save') }}</button>                
                <button type='button' class='btn btn-white' data-dismiss='modal'>{{ trans('form.btn_cancel') }}</button>                            
            </div>
        </div>
    </div>
</div>