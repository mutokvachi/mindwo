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
            <button class="btn btn-sm btn-default">Pievienot grupu</button>

            <div class="panel panel-default" style='border: 1px solid #ddd; margin-top: 20px;'>
                <div class="panel-heading">
                    <h3 class="panel-title" style="font-weight:bold">Jaunais Publisko iepirkumu likums - iesācējiem</h3>                    
                    <div>C modulis „Valsts pārvaldes juridiskie jautājumi”</div>
                    <div>08.08.2017</div>
                    <div>
                        <button class="btn btn-xs btn-default">Labot grupu</button>
                        <button class="btn btn-xs btn-default">Dzēst grupu</button>
                    </div>
                </div>
                <div class="panel-body">            
                    <div style="">
                        <h5 style="font-weight:bold">Pievienotie dalībnieki</h5>
                        <div style="margin-bottom:10px;">
                            <div class='row' style="margin-bottom:5px;">
                                <div class="col-lg-4 col-md-5 col-sm-6"> 
                                    Jana Garne Garne (janaa.Garne.Garnesssa@inbox.lv)
                                </div>
                                <div class="col-lg-8 col-md-7 col-sm-6">
                                    <button class="btn btn-xs btn-default">Labot</button>
                                    <button class="btn btn-xs btn-default">Dzēst</button>
                                </div>
                            </div>
                            <div class='row' style="margin-bottom:5px;">
                                <div class="col-lg-4 col-md-5 col-sm-6"> 
                                    Jana Garne Garne (janaa.Garne.Garnesssa@inbox.lv)
                                </div>
                                <div class="col-lg-8 col-md-7 col-sm-6">
                                    <button class="btn btn-xs btn-default">Labot</button>
                                    <button class="btn btn-xs btn-default">Dzēst</button>
                                </div>
                            </div>
                            <div class='row' style="margin-bottom:5px;">
                                <div class="col-lg-4 col-md-5 col-sm-6"> 
                                    Jana Garne Garne (janaa.Garne.Garnesssa@inbox.lv)
                                </div>
                                <div class="col-lg-8 col-md-7 col-sm-6">
                                    <button class="btn btn-xs btn-default">Labot</button>
                                    <button class="btn btn-xs btn-default">Dzēst</button>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-default">Pievienot dalībnieku</button>
                    </div>                    
                </div>       
            </div>

            <button class="btn btn-primary">Apstiprināt un saglabāt</button>

            <!--  include('pages.education.registration.courses') -->      
        </div>
    </div>
</div>
@stop

@section('main_custom_javascripts')
    @include('pages.view_js_includes')
    <script src = "{{ elixir('js/elix_education.js') }}" type='text/javascript'></script>
@stop