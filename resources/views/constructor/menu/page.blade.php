@extends('frame')

@section('main_custom_css')  
  @include('pages.view_css_includes')
  <link href="{{ elixir('css/elix_menu_builder.css') }}" rel="stylesheet"/>
  <style>
      .dx-menu-builder {
          width: 70%;
          margin: 0 auto;
      }
      
      .dx-menu-area {
          margin-bottom: 100px;
      }
      
      .dx-site-edit-btn {
          border: 1px solid #c2cad8!important; 
          margin-left: -2px!important;
      }
      
      .parentError {
          border: 1px solid red;
      }
  </style>
@endsection

@section('main_custom_javascripts')
    @include('pages.view_js_includes')
    <script src="{{ elixir('js/elix_menu_builder.js') }}" type='text/javascript'></script>
    <script>
        $(document).ready(function()
        {
            $('.dx-menu-builder').MenuBuilder();
        });
    </script>
@endsection

@section('main_content')
<div class="dx-menu-builder">
  <div class="portlet light dx-menu-area">
    <div class="portlet-title">
        <div class="caption font-grey-cascade uppercase">
          <i class="fa fa-sitemap"></i> {{ trans('constructor.menu.title') }}        
        </div>
        @include('constructor.menu.sites')
    </div>
    <div class="portlet-body">
        <div class="dd">
            <ol class="dd-list">
            {!! $menu !!}
            </ol>
        </div>
    </div>
  </div>
    @include('constructor.menu.footer')
</div>
@endsection

