<div class="modal fade in" id="dx-crypto-modal-psw" tabindex="-1" role="dialog" aria-labelledby="dx-crypto-modal-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">      
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ trans('form.btn_close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="dx-crypto-modal-label">{{ trans('crypto.title_modal_password') }}</h4>
            </div>
            <div class="modal-body">
                <label>{{ trans('crypto.label_password') }}</label>
                <input class="form-control" id="dx-crypto-modal-input-password" type="password" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn pull-left dx-crypto-modal-decline" data-dismiss="modal">{{ trans('crypto.btn_close') }}</button>
                <button type="button" class="btn btn-primary dx-crypto-modal-accept" data-dismiss="modal">{{ trans('crypto.btn_accept') }}</button>   
            </div>
        </div>
    </div>
</div>