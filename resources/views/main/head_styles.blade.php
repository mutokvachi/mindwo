    {{-- plugin styles --}}
    <link href="{{ asset('metronic/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('metronic/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ elixir('css/elix_plugins.css') }}" rel="stylesheet" type="text/css"/>
    {{-- /plugin styles --}}
    {{-- theme global styles --}}
    <link href="{{ asset('metronic/global/css/components-md.min.css') }}" rel="stylesheet" id="style_components" type="text/css"/>
    <link href="{{ asset('metronic/global/css/plugins-md.min.css') }}" rel="stylesheet" type="text/css"/>
    {{-- /theme global styles --}}
    {{-- theme layout styles --}}
    <link href="{{ asset('metronic/layouts/layout2/css/layout.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('metronic/layouts/layout2/css/themes/blue.min.css') }}" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="{{ asset('metronic/layouts/layout2/css/custom.min.css') }}" rel="stylesheet" type="text/css"/>
    {{-- /theme layout styles --}}
    @if ($user_tasks_count > 0)
      {{-- Gritter --}}
      <link href="{{ asset('plugins/gritter/jquery.gritter.css') }}" rel="stylesheet"/>
    @endif

    @yield('main_custom_css')

    @if(isset($is_slidable_menu) && $is_slidable_menu)
      @include('box.box_css')
    @endif
  
    <!--[if IE]>
    <style type="text/css">
      #search_criteria {
        height: 34px;
      }
    </style>
    <![endif]-->
    
    <style>
      @include('main.page_background_css')
      {!! get_portal_config('SCRIPT_CSS') !!}
    </style>
    <link href="{{ elixir('css/elix_mindwo.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ elixir('css/elix_mindwo_horizontal.css') }}" rel="stylesheet" type="text/css"/>
