@extends('frame')

@section('main_custom_css')        
    @include('pages.view_css_includes')
    <link href="{{ elixir('css/elix_education.css') }}" rel="stylesheet" />
@stop

@section('main_content')
<div class="dx-edu-course-page">  
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-bookmark"></i>
                <span class="caption-subject bold uppercase">Reģistrācija</span>
            </div>
        </div>
        <div class="portlet-body">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#dx-edu-registration-tab-courses" id="dx-edu-registration-tab-btn-courses" data-toggle="tab"> 1. kursu izvēle </a>
                </li>
                <li>
                    <a href="#dx-edu-registration-tab-participants" id="dx-edu-registration-tab-btn-participants" data-toggle="tab"> 2. dalībnieki </a>
                </li>
                <li>
                    <a href="#dx-edu-registration-tab-invoice" id="dx-edu-registration-tab-btn-invoice" data-toggle="tab"> 3. informācija norēķiniem </a>
                </li>
            </ul>
            <div class="tab-content dx-edu-tab-content">
                <div class="tab-pane fade active in" id="dx-edu-registration-tab-courses">
                    @include('pages.education.registration.courses')
                </div>
                <div class="tab-pane fade" id="dx-edu-registration-tab-participants">
                    @include('pages.education.registration.participants')
                </div>
                <div class="tab-pane fade" id="dx-edu-registration-tab-invoice">
                    @include('pages.education.registration.invoice')
                </div>
            </div>            
        </div>
    </div>
</div>
@stop

@section('main_custom_javascripts')
    @include('pages.view_js_includes')
    <script src = "{{ elixir('js/elix_education.js') }}" type='text/javascript'></script>
@stop