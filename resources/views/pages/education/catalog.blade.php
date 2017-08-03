@extends('frame')

@section('main_custom_css')        
    @include('pages.view_css_includes')
@stop

@section('main_content')
<div class="dx-edu-catalog-page">  
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-university"></i>
                <span class="caption-subject bold uppercase">Kursu grafiks</span>
            </div>
        </div>
        <div class="portlet-body">
            @include('pages.education.catalog_filter')
            @foreach($courses as $course)
            <div class='row' style="margin-bottom: 10px; padding-bottom:10px; border-bottom: 1px solid #eee; margin-left: 5px; margin-right: 5px;">
                <div class='col-lg-1 col-md-2'>
                    <div style="width:60px; height:60px; background-color: #40574d; float:right; margin-top: 10px;">
                        <i class="{{ $course->icon }}" style="color:white; font-size:24px; margin-left:18px; margin-top:22px;"> </i>                     
                    </div>
                </div>
                <div class='col-lg-11 col-md-10'>
                    <div>
                        <h4>{{ $course->title }}</h4>
                    </div>
                    <div>
                        <div style="margin-bottom:3px;">
                            <span style="font-weight:bold;">{{ $course->date->format('d.m.Y') }}</span> {{$course->time_from . ' - ' . $course->time_to }}
                        </div>
                        <div>
                            <button class="btn btn-sm">Uzzināt vairāk</button>
                            @if($course->is_full)
                            <button class="btn btn-sm btn-danger disabled">Grupa ir pilna</button>
                            @else
                            <button class="btn btn-sm btn-success">Pieteikties</button>
                            @endif
                        </div>                        
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @include('pages.crypto.modal_generate_cert')
</div>
@stop

@section('main_custom_javascripts')
    @include('pages.view_js_includes')
@stop