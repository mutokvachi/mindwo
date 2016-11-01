@extends('frame')

@section('main_custom_css')    

@stop

@section('main_content')
    @include('blocks.empl_profile.personal_docs', ['employee_id' => 1])
@stop

@section('main_custom_javascripts') 
    
@stop