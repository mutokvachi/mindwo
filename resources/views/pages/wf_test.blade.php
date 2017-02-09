@extends('frame')

@section('main_custom_css')  
    @include('pages.view_css_includes')
@stop

@section('main_content')
     @include('workflow.visual_ui.wf_component', [ 
     'frm_uniq_id'=> '40043d91-d8d0-4521-8341-03ad4464fe6a',
     'grid_htm_id'=>'grid_e2bcc195-5eb1-4242-95b2-fe962f02e262'])
    
@stop

@section('main_custom_javascripts') 
    @include('pages.view_js_includes')
@stop