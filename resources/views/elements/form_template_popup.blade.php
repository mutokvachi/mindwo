<div class='modal fade dx-popup-modal dx-template-popup' id='template_form_{{ $frm_uniq_id }}' aria-hidden='true' role='dialog' aria-hidden='true' data-backdrop='static' style="z-index: 999999;">
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
            @include('elements.form_header',['form_title' => trans('form.template.popup_title'), 'badge' => '', 'form_icon' => '<i class="fa fa-files-o"></i>'])

            <div class='modal-body'>
                <div class='dx-template-intro'>
                    {{ trans('form.template.intro')}}
                </div>
                <div class='dx-templates-list'></div>
            </div>

            <div class='modal-footer' style="border-top: 1px solid lightgray;">  
                <a type='button' class='btn btn-white dx-manage-templ-btn pull-left' href="javascript:;" target="_blank"><i class='fa fa-cog'></i> {{ trans('form.template.btn_manage_templ') }}</a>              
                <button type='button' class='btn btn-white dx-cancel-btn' data-dismiss='modal'>{{ trans('form.btn_cancel') }}</button>                            
            </div>
        </div>
    </div>
</div>