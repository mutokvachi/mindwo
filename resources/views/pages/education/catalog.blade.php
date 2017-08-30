@extends('frame')

@section('main_custom_css')        
    @include('pages.view_css_includes')
    <link href="{{ elixir('css/elix_education.css') }}" rel="stylesheet" />
@stop

@section('main_content')
<div class="dx-edu-catalog-page" data-dx-date-format="{{ config('dx.date_format') }}">  
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-university"></i>
                <span class="caption-subject bold uppercase">Mācību pasākumu katalogs</span>
            </div>
        </div>
        <div class="portlet-body">
            @include('pages.education.catalog_filter')
            <div class="dx-edu-catalog-body">
            </div>
        </div>
    </div>
</div>
@stop

@section('main_custom_javascripts')
    @include('pages.view_js_includes')
    <script src = "{{ elixir('js/elix_education.js') }}" type='text/javascript'></script>
@stop