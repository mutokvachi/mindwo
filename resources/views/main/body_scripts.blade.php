    <script>
      var dx_is_slider = {{ ((isset($is_slidable_menu) && $is_slidable_menu)) ? "1" : "0" }};
      var dx_is_cssonly = {{ config('dx.is_cssonly_ui', false) ? 'true' : 'false' }};
    </script>
    <!--[if lt IE 9]>
    <script src="{{ asset('metronic/global/plugins/respond.min.js') }}"></script>
    <script src="{{ asset('metronic/global/plugins/excanvas.min.js') }}"></script>
    <![endif]-->
    
    <script src="{{ asset(getIncludeVersion('js/lang.js')) }}" type="text/javascript"></script>
    
    <script type="text/javascript">
      Lang.setLocale('{{ App::getLocale() }}');
    </script>
    
    <script src="{{ elixir('js/elix_plugins.js') }}" type="text/javascript"></script>
    <script src="{{ elixir('js/elix_mindwo_horizontal_menu.js') }}" type="text/javascript"></script>
    
    @yield('main_custom_javascripts')
    
    @if(Auth::check() && Auth::user()->id != config('dx.public_user_id', 0))
      <script src="{{ elixir('js/elix_userlinks.js') }}" type="text/javascript"></script>
    @endif
    
    {!! get_portal_config('GOOGLE_ANALYTIC') !!}
    
    <script>
      {!! get_portal_config('SCRIPT_JS') !!}
    </script>
    
    @if(isset($is_slidable_menu) && $is_slidable_menu)
      <script type="text/javascript" src="{!! asset('js/box/script.js') !!}"></script>
      <script>
        box.icon = $('<i class="fa fa-angle-double-right"></i>');
        box.dashboardReload = {{ (count($breadcrumb) > 0) ? 1 : 0}};
      </script>
    @endif
