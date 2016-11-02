@extends('frame')

@section('main_custom_css')    
<link href="{{Request::root()}}/plugins/tree/themes/default/style.min.css" rel="stylesheet" />
<link href="{{Request::root()}}/metronic/global/plugins/bootstrap-colorpicker/css/colorpicker.css" rel="stylesheet" type="text/css" />
<link href="{{Request::root()}}/plugins/select2/select2.css" rel="stylesheet" />

<link href= "{{ elixir('css/elix_view.css') }}" rel="stylesheet" />
@stop

@section('main_content')
    @include('blocks.empl_profile.personal_docs', ['user_id' => 1])
@stop

@section('main_custom_javascripts') 
    <script src = "{{ elixir('js/elix_view.js') }}" type='text/javascript'></script>
@stop