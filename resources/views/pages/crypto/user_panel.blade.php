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
            <button class="btn green-jungle dx-crypto-generate-cert-btn" style="{{ $has_cert ? 'display: none;' : ''  }}">
                {{ trans('crypto.btn_generate_cert') }} <i class="fa fa-shield"></i>
            </button>
            <button class="btn dx-crypto-generate-new-cert-btn" style="{{ $has_cert ? '' : 'display: none;'  }}">
                {{ trans('crypto.btn_generate_new_cert') }} <i class="fa fa-warning"></i>
            </button>

            <div style="margin-top:30px; border: 1px solid gray; padding: 10px;">
                <button class="btn btn-primary dx-crypto-generate-masterkey-btn">
                    Generate master key <i class="fa fa-key"></i>
                </button>
                <button class="btn btn-primary dx-crypto-encrypt-test-btn">
                    Encrypt
                </button>
                <div id="dx-master-key"></div>

                <div class="dx-crypto-field" data-masterkey-group="3">000000000000000000000000000000009d80cba910cdcb9dbcaa5a41e6040a51cdc9bcd290efc6bc859b1daf9a40b2d8ed832bc5eaca3333a8a138</div>
                <div class="dx-crypto-field" data-masterkey-group="5">0000000000000000000000000000000098f312f0b13a253892a52c06609e41be410bdce3b491a2941fbd1894b310a90a0f85001a69a3d355899612f6</div>
                <div class="dx-crypto-field" data-masterkey-group="3">000000000000000000000000000000009d80cba910cdcb9dbcaa5a41e6040a51cdc9bcd290efc6bc859b25859a40b2d8ed832bc5eaca3333a8a13872</div>
                <input type="text" value="00000000000000000000000000000000e3c598fd43" class="dx-crypto-field"  data-masterkey-group="3" />
            </div>
        </div>
    </div>
    @include('pages.crypto.modal_generate_cert')
</div>
@stop

@section('main_custom_javascripts') 

@include('pages.view_js_includes')

@stop