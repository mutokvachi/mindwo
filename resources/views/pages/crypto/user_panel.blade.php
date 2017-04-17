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
    </div>
    @include('pages.crypto.modal_generate_cert')
</div>
@stop

@section('main_custom_javascripts') 

@include('pages.view_js_includes')

@stop