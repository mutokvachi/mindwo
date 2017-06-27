<div class="modal fade in" id="dx-crypto-modal-regen-masterkey" tabindex="-1" role="dialog" aria-labelledby="dx-crypto-modal-regen-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div id='dx-crypto-modal-regen-content' class="modal-content">      
            <div class="modal-header">
                <button type="button" class="close dx-crypto-modal-regen-btn-cancel" aria-label="{{ trans('form.btn_close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="dx-crypto-modal-regen-label">{{ trans('crypto.regen_masterkey_label') }}</h4>
            </div>
            <div class="modal-body">
                <div class="dx-crypto-modal-regen-progress">
                    <p>
                        <label class="dx-crypto-modal-regen-progress-label"></label>
                    </p>
                    <div class="progress progress-striped active">
                        <div class="progress-bar progress-bar-success dx-crypto-modal-regen-progress-bar" 
                             role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                            <span class="sr-only"> </span>
                        </div>
                    </div>    
                </div>
                <div class="alert alert-success dx-crypto-modal-regen-succ" style="display: none">
                    <strong>{{ trans('crypto.help_success_func') }}</strong> {{ trans('crypto.help_success_regen_text') }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn pull-left dx-crypto-modal-regen-btn-cancel">{{ trans('crypto.btn_cancel') }}</button>
                <button type="button" class="btn pull-left dx-crypto-modal-regen-btn-close" data-dismiss="modal" style="display:none">{{ trans('crypto.btn_close') }}</button>
            </div>
        </div>
    </div>
</div>