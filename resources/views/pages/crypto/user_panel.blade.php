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
            
            <button class="btn dx-crypto-regen">
                TEST Master key regen <i class="fa fa-car"></i>
            </button>
        </div>
    </div>
    @include('pages.crypto.modal_generate_cert')
</div>
@stop

@section('main_custom_javascripts') 

@include('pages.view_js_includes')

@stop