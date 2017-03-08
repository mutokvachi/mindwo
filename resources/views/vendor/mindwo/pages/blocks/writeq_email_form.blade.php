<!-- Email entering popup -->
<div class='modal fade' aria-hidden='true' id='popup_email_{{ $block_guid }}' role='dialog' aria-hidden='true' data-backdrop='static' style="z-index: 999999;">
    <div class='modal-dialog modal-md'>
        <div class='modal-content'>
            @include('mindwo/pages::elements.form_header',['form_title' => trans('mindwo/pages::writeq.title_email_form'), 'badge' => ''])

            <div class='modal-body' style="overflow-y: auto; max-height: 500px;">
                <div style="padding-left: 20px;">
                    <p>{{ trans('mindwo/pages::writeq.hint_email') }}<p>
                    <div class='form-group has-feedback row'>
                        <div class='col-lg-4'>
                        <label class='control-label pull-right'><b>{{ trans('mindwo/pages::writeq.lbl_email') }}:                                    
                                <span style="color: red"> *</span></b>                                    
                        </label>
                        </div>
                        <div class='col-lg-8'>
                            <input type="email" name="q_email" value="{{ (Auth::user()->email) ? Auth::user()->email : '' }}" class='form-control' type=text maxlength="250" required data-foo='bar'/>   
                            <div class="help-block with-errors"></div>
                        </div>    
                    </div>							
                </div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-primary' data-dismiss='modal' id="email_btn_{{ $block_guid }}">{{ trans('mindwo/pages::writeq.btn_send') }}</button>
                <button type='button' class='btn btn-white' data-dismiss='modal'>{{ trans('mindwo/pages::writeq.btn_cancel') }}</button>                            
            </div>
        </div>
    </div>
</div>