<div style="padding-left: 20px;" class="dx-user-change-passw-form"
     dx_block_init = "0"
     >
    <p>{{ trans('password_form.description') }}<p>
    <div class='form-group has-feedback row'>
        <div class='col-lg-4'>
        <label class='control-label pull-right'><b>{{ trans('password_form.current_pass') }}                                    
                <span style="color: red"> *</span></b>                                    
        </label>
        </div>
        <div class='col-lg-8'>
            <input type="password" name="pass_old" value="" class='form-control' required maxlength='100' data-minlength="8" data-error='{{ trans('password_form.err_length') }}'/>   
            <div class="help-block with-errors"></div>
        </div>    
    </div>
    <div class='form-group has-feedback row'>
        <div class='col-lg-4'>
        <label class='control-label pull-right'><b>{{ trans('password_form.new_pass') }}                                    
                <span style="color: red"> *</span></b>                                    
        </label>
        </div>
        <div class='col-lg-8'>
            <input type="password" name="pass_new1" value="" class='form-control' required maxlength='100' data-minlength="8" data-error='{{ trans('password_form.err_length') }}' id="dx-user-pass-new1"/>   
            <div class="help-block with-errors"></div>
        </div>    
    </div>
    <div class='form-group has-feedback row'>
        <div class='col-lg-4'>
        <label class='control-label pull-right'><b>{{ trans('password_form.new_pass_repeat') }}                                    
                <span style="color: red"> *</span></b>                                    
        </label>
        </div>
        <div class='col-lg-8'>
            <input type="password" name="pass_new2" value="" class='form-control' required maxlength='100' data-minlength="8" data-error='{{ trans('password_form.err_length') }}' data-match="#dx-user-pass-new1" data-match-error="{{ trans('password_form.err_pass_not_match') }}"/>   
            <div class="help-block with-errors"></div>
        </div>    
    </div>
    <div class='form-group row'>
        <div class='col-lg-4'>        
        </div>
        <div class='col-lg-8'>
            <button type='button' class='btn btn-primary dx-user-change-passw-btn'>{{ trans('password_form.btn_change_pass') }}</button>
        </div>    
    </div>    
</div>