<div class="modal fade in" id="dx-edu-modal-participant" tabindex="-1" role="dialog" aria-labelledby="dx-edu-modal-participant-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div id='dx-edu-modal-participant-content' class="modal-content">      
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ trans('form.btn_close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="dx-edu-modal-participant-label">Dalībnieka izvēle</h4>
            </div>
            <div class="modal-body">
                <div class='dx-edu-modal-participant-regular'>
                    <div class="form-group required">
                        <label>Vārds</label>
                        <input class='form-control dx-edu-modal-participant-input-name' type="text">
                    </div>            
                    <div class="form-group required">
                        <label>Uzvārds</label>
                        <input class='form-control dx-edu-modal-participant-input-lastname' type="text">
                    </div>
                    <div class="form-group required">
                        <label>Personas kods</label>
                        <input class='form-control dx-edu-modal-participant-input-pers_code' type="text">
                    </div>
                    <div class="form-group">
                        <label>Darba vieta</label>
                        <input class='form-control dx-edu-modal-participant-input-job' type="text">
                    </div>
                    <div class="form-group">
                        <label>Amats</label>
                        <input class='form-control dx-edu-modal-participant-input-position' type="text">
                    </div>
                    <div class="form-group required">
                        <label>Tālrunis</label>
                        <input class='form-control dx-edu-modal-participant-input-telephone' type="text">
                    </div>
                    <div class="form-group required">
                        <label>E-pasts</label>
                        <input class='form-control dx-edu-modal-participant-input-email' type="text">
                    </div> 
                </div>   
                <div class='dx-edu-modal-participant-organization'>
                    <div class="form-group required">
                        <label>Darbinieks</label>
                        <input class='form-control dx-edu-modal-participant-input-employee' type="text">
                    </div>          
                </div>            
            </div>
            <div class="modal-footer">
                <button type="button" class="btn pull-left dx-edu-modal-participant-decline" data-dismiss="modal">{{ trans('form.btn_close') }}</button>
                <button type="button" class="btn btn-primary dx-edu-modal-participant-accept">{{ trans('form.btn_accept') }}</button>   
            </div>
        </div>
    </div>
</div>