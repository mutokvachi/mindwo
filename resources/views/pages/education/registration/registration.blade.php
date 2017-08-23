@extends('frame')

@section('main_custom_css')        
    @include('pages.view_css_includes')
    <link href="{{ elixir('css/elix_education.css') }}" rel="stylesheet" />
@stop

@section('main_content')
<div class="dx-edu-registration-page" data-subject_id="{{ $subject_id }}" data-dx-date-format="{{ config('dx.date_format') }}">  
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-bookmark"></i>
                <span class="caption-subject bold uppercase">Reģistrācija</span>
            </div>
        </div>
        <div class="portlet-body">
            <div class='dx-edu-reg-suc' style='display:none;'>
                <h2>Pieteikums pieņemts!</h4>
                Uz Jūsu norādīto e-pastu tiks nosūtīts rēķins
                
            </div>
            <div class='dx-edu-reg-win'>
                <div style="{{ $is_coordinator ? '' : 'display:none' }}">
                    <div style="font-weight:bold">Pieteikt dalībniekus kā organizācija</div>
                    <div style='margin-bottom:20px'>
                        <input
                            type="checkbox" 
                            class="dx-bool dx-edu-reg-is-coordinator"                 
                            data-size="small"
                            data-off-text="Nē" 
                            data-on-text="Jā" />
                    </div> 
                </div> 
                <a href="{{Request::root()}}/edu/catalog" class="btn btn-sm btn-default " style="margin-right:5px;">Atgriezties uz katalogu</a> 
                <button class="btn btn-sm btn-default dx-edu-reg-btn-add-group">Pievienot vēl vienu grupu</button>

                <div class="dx-edu-reg-group-container" style="margin-top:20px; margin-bottom:20px;">
                    <div class='dx-edu-reg-group-panel-empty' style='color:red'>
                        <i>Nav pievienota neviena mācību grupa. Lai pieteiktos mācību grupām, vispirms pievienojiet vismaz vienu mācību grupu!</i>
                    </div>
                </div>
                @include('pages.education.registration.invoice') 
                <button class="btn btn-primary dx-edu-reg-btn-save disabled">Apstiprināt un saglabāt</button>
                <p class='dx-edu-reg-label-save' style="font-style:italic; font-size:12px; margin-top:5px; color:red">
                    Lai saglabātu pieteikumu jāpievieno vismaz viena mācību grupa un tai ir jāpiesaista vismaz viens dalībnieks
                </p>

                @include('pages.education.registration.group_form')   
                @include('pages.education.registration.participant_form')    
                @include('pages.education.registration.registration_group_row')
                @include('pages.education.registration.registration_participant_row')   
            </div>
            <div style="margin-bottom:20px;"></div>
            <a href="{{Request::root()}}/edu/catalog" class="btn btn-default ">Atgriezties uz katalogu</a>
        </div>
    </div>
</div>
@stop

@section('main_custom_javascripts')
    @include('pages.view_js_includes')
    <script src = "{{ elixir('js/elix_education.js') }}" type='text/javascript'></script>
@stop