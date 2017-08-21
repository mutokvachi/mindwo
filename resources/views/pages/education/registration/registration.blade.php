@extends('frame')

@section('main_custom_css')        
    @include('pages.view_css_includes')
    <link href="{{ elixir('css/elix_education.css') }}" rel="stylesheet" />
@stop

@section('main_content')
<div class="dx-edu-registration-page">  
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-bookmark"></i>
                <span class="caption-subject bold uppercase">Reģistrācija</span>
            </div>
        </div>
        <div class="portlet-body">
            <button class="btn btn-sm btn-default dx-edu-reg-btn-add-group">Pievienot grupu</button>

            <div class="dx-edu-reg-group-panel" style="margin-top:20px; margin-bottom:20px;">
                <div clas='dx-edu-reg-group-panel-empty'>
                    <i>Nav pievienota neviena mācību grupa. Lai pieteiktos mācību grupām, vispirms pievienojiet vismaz vienu mācību grupu!</i>
                </div>
            </div>

            <button class="btn btn-primary dx-edu-reg-btn-save disabled">Apstiprināt un saglabāt</button>
            <p class='dx-edu-reg-label-save' style="font-style:italic; font-size:12px; margin-top:5px;">
                Lai saglabātu pieteikumu jāpievieno vismaz viena mācību grupa un tai ir jāpiesaista vismaz viens dalībnieks
            </p>

            @include('pages.education.registration.group_form')   
            @include('pages.education.registration.participant_form')    
        </div>
    </div>
</div>
@stop

@section('main_custom_javascripts')
    @include('pages.view_js_includes')
    <script src = "{{ elixir('js/elix_education.js') }}" type='text/javascript'></script>
@stop