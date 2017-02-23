<div class='modal fade dx-popup-modal' id='{{ $block_id }}_popup' aria-hidden='true' role='dialog' aria-hidden='true' data-backdrop='static' style="z-index: 999999;">
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
            @include('elements.form_header',['form_title' => trans('grid.view_editor_form_title'), 'badge' => trans('grid.badge_edit'), 'form_icon' => '<i class="fa fa-list"></i>'])

            <div class='modal-body' style="overflow-y: auto; max-height: 500px;">
                
            </div>
            <div class='modal-footer dx-view-popup-footer'>
                <button type='button' class='btn btn-white pull-left dx-view-btn-delete'>{{ trans('form.btn_delete') }}</button>
                <button type='button' class='btn btn-white pull-left dx-view-btn-copy'>{{ trans('form.btn_copy') }}</button>
                <button type='button' class='btn btn-primary dx-view-btn-save'>{{ trans('form.btn_save') }}</button>                
                <button type='button' class='btn btn-white' data-dismiss='modal'>{{ trans('form.btn_cancel') }}</button>                            
            </div>
        </div>
    </div>
</div>