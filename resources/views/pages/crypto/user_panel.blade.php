@extends('frame')

@section('main_custom_css')        
@include('elements.employee_css', ['is_advanced_filter' => 1])


@include('pages.view_css_includes')
@stop

@section('main_content')

<div class="dx-employees-page">  
    <h3 class="page-title"></h3>
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-lock"></i>
                <span class="caption-subject bold uppercase">{{ trans('crypto.user_profile_title') }}</span>
            </div>
        </div>
        <div class="portlet-body">
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
@stop

@section('main_custom_javascripts') 

@include('pages.view_js_includes')

@stop