<div class="modal fade" id="dx-crypto-modal-generate-cert" tabindex="-1" role="dialog" aria-labelledby="dx-crypto-modal-gen-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div id='dx-crypto-modal-gen-content' class="modal-content">      
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ trans('form.btn_close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="dx-crypto-modal-gen-label">{{ trans('crypto.btn_generate_cert') }}</h4>
            </div>
            <div class="modal-body">
                <p>
                    <label>{{ trans('crypto.label_password') }}</label>
                    <input autocomplete="new-password" class="form-control" id="dx-crypto-modal-gen-input-password" type="password" />
                </p>
                <p>
                    <label>{{ trans('crypto.label_password_again') }}</label>
                    <input autocomplete="new-password" class="form-control" id="dx-crypto-modal-gen-input-password-again" type="password" />
                </p>                    
            </div>
            <div class="modal-footer">
                <button type="button" class="btn pull-left dx-crypto-modal-gen-decline" data-dismiss="modal">{{ trans('crypto.btn_close') }}</button>
                <button type="button" class="btn btn-primary dx-crypto-modal-gen-accept">{{ trans('crypto.btn_accept') }}</button>   
            </div>
        </div>
    </div>
</div>