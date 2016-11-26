@extends('frame')

@section('main_custom_css')    
<link href="{{Request::root()}}/plugins/tree/themes/default/style.min.css" rel="stylesheet" />
<link href="{{Request::root()}}/metronic/global/plugins/bootstrap-colorpicker/css/colorpicker.css" rel="stylesheet" type="text/css" />
<link href="{{Request::root()}}/plugins/select2/select2.css" rel="stylesheet" />

<link href= "{{ elixir('css/elix_view.css') }}" rel="stylesheet" />
<link href="{{ elixir('css/elix_employee_profile.css') }}" rel="stylesheet"/>
@stop

@section('main_content')
<div id='dx-tab_notes' >
    
</div>
@stop

@section('main_custom_javascripts') 
    <script src = "{{ elixir('js/elix_view.js') }}" type='text/javascript'></script>
    <script src = "{{ elixir('js/elix_profile.js') }}" type='text/javascript'></script>
    <script  type='text/javascript'>
        window.DxEmpNotes.init();
        
        window.DxEmpNotes.init(1, true);
          
          window.DxEmpNotes.loadView());
        </script>
@stop