<!-- Popup info -->

<div class='modal fade' aria-hidden='true' id='popup_window' role='dialog' aria-hidden='true' data-backdrop='static' style="z-index: 999999;">
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
            @include('elements.form_header',['form_title' => '', 'badge' => ''])

            <div class='modal-body' style="overflow-y: auto; max-height: 500px;">
                <div class='row' id="popup_body">

                </div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-white' data-dismiss='modal' id = "popup_button">{{ trans('form.btn_close') }}</button>                            
            </div>
        </div>
    </div>
</div>


<div class="modal fade" aria-hidden="true" id="popup_authorization" role="dialog" aria-hidden="true" data-backdrop="static" style="z-index: 999999;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            @include('elements.form_header',['form_title' => trans("relogin_form.form_title"), 'badge' => ''])

            <div class="modal-body" style="padding-right: 15px;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet" style="margin-bottom: 0px;">
                            <div class="portlet-body form">
                                <div class="note note-success">
                                    <p>{{ trans("relogin_form.info") }}</p>
                                </div>
                                <form id="reLoginForm">
                                    <div class="form-group">
                                        <label class="control-label" for="relogin_user_name">{{ trans('relogin_form.user_name') }}</label>
                                        <input id="relogin_user_name" type="text" class="form-control" name="user_name" placeholder="{{ trans('index.placeholder_user_name') }}" required maxlength="100" data-minlength="3" data-error="{{ trans('index.user_name_data_error') }}">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="relogin_password">{{ trans('relogin_form.password') }}</label>
                                        <input id="relogin_password" type="password" class="form-control" name="password" placeholder="{{ trans('index.placeholder_password') }}" required maxlength="100" data-minlength="8" data-error="{{ trans('index.password_data_error') }}">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary" id="dx-relogin-user-btn">{{ trans('index.login') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">{{ trans('form.btn_close') }}</button>  
            </div>
        </div>
    </div>
</div>