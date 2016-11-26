<div class="modal modal-danger fade" id="mindwo-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div id='mindwo-modal-content' class="modal-content">      
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ trans('form.btn_close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="mindwo-modal-success-label"></h4>
            </div>
            <div class="modal-body">
                <p id='mindwo-modal-body'></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">{{ trans('form.btn_cancel') }}</button>
                <button id="mindwo-modal-accept" type="button" class="btn btn-outline"></button>   
            </div>
        </div>
    </div>
</div>