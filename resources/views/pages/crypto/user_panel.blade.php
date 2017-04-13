@extends('frame')

@section('main_custom_css')        
@include('elements.employee_css', ['is_advanced_filter' => 1])


@include('pages.view_css_includes')
@stop

@section('main_content')

<div class="dx-crypto-user_panel-page">  
    <h3 class="page-title"></h3>
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-lock"></i>
                <span class="caption-subject bold uppercase">{{ trans('crypto.user_profile_title') }}</span>
            </div>
        </div>
        <div class="portlet-body">
            <button class="btn btn-primary dx-crypto-generate-cert-btn">
                {{ trans('crypto.generate_cert') }} <i class="fa fa-shield"></i>
            </button>
            
            <button class="btn btn-primary dx-crypto-generate-masterkey-btn">
                Generate master key <i class="fa fa-shield"></i>
            </button>
            <div id="dx-master-key"></div>

            <div class="dx-crypto-field">
                tests
            </div>
            <div class="dx-crypto-field">
                tests2
            </div>
            <input type="text" value="input test" class="dx-crypto-field" />
        </div>
    </div>
    <div class="modal fade in" id="dx-crypto-modal-generate-cert" tabindex="-1" role="dialog" aria-labelledby="dx-crypto-modal-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id='dx-crypto-modal-content' class="modal-content">      
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ trans('form.btn_close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="dx-crypto-modal-label"></h4>
                </div>
                <div class="modal-body">
                    <p>
                        <label>{{ trans('crypto.label_password') }}</label>
                        <input autocomplete="new-password" class="form-control" id="dx-crypto-modal-input-password" type="password" />
                    </p>
                    <p>
                        <label>{{ trans('crypto.label_password_again') }}</label>
                        <input autocomplete="new-password" class="form-control" id="dx-crypto-modal-input-password-again" type="password" />
                    </p>                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn pull-left dx-crypto-modal-decline" data-dismiss="modal">{{ trans('crypto.btn_close') }}</button>
                    <button type="button" class="btn btn-primary dx-crypto-modal-accept">{{ trans('crypto.btn_accept') }}</button>   
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('main_custom_javascripts') 

@include('pages.view_js_includes')

@stop