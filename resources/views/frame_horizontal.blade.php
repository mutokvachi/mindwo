<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    
    <title>{{ $portal_name }} :: {{ isset($page_title) ? $page_title : 'Intranet' }}</title>
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    
    <!-- BEGIN PLUGINS STYLES -->
    <link href="{{Request::root()}}/metronic/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{Request::root()}}/metronic/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ elixir('css/elix_plugins.css') }}" rel="stylesheet" type="text/css"/>
    <!-- BEGIN PLUGINS STYLES -->
    
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="{{Request::root()}}/metronic/global/css/components-md.min.css" rel="stylesheet" id="style_components" type="text/css"/>
    <link href="{{Request::root()}}/metronic/global/css/plugins-md.min.css" rel="stylesheet" type="text/css"/>
    <!-- END THEME GLOBAL STYLES -->
    
    <!-- BEGIN THEME LAYOUT STYLES -->
    <link href="{{Request::root()}}/metronic/layouts/layout2/css/layout.css" rel="stylesheet" type="text/css"/>
    <link href="{{Request::root()}}/metronic/layouts/layout2/css/themes/blue.min.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="{{Request::root()}}/metronic/layouts/layout2/css/custom.min.css" rel="stylesheet" type="text/css"/>
    <!-- END THEME LAYOUT STYLES -->
  
  @if ($user_tasks_count > 0)
    <!-- Gritter -->
      <link href="{{Request::root()}}/plugins/gritter/jquery.gritter.css" rel="stylesheet"/>
      @endif

    @yield('main_custom_css')

    @if (isset($is_slidable_menu) && $is_slidable_menu)
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
      
      <link rel="shortcut icon" href="{{Request::root()}}/favicon.ico">
      <style>
        @media screen and (max-width: 767px) {
          .navbar {
            min-height: auto;
          }
          
          .dx-top-right-menu li {
            float: left;
          }
          
          .dx-top-menu .navbar-nav .open .dropdown-menu {
            position: static;
            float: none;
            background-color: inherit;
            box-shadow: none !important;
            padding-left: 20px;
          }
          
          .dx-top-menu .navbar-nav .open .dropdown-menu > li > a {
            background-color: transparent;
          }
          
          .dropdown-submenu:hover > .dropdown-menu {
            display: none;
          }
        }
        
      </style>
  </head>
  
  <body class="dx-main-page dx-horizontal-menu-ui"
    dx_valid_html_elements="{{ get_portal_config('VALID_HTML_ELEMENTS') }}"
    dx_valid_html_styles="{{ get_portal_config('VALID_HTML_STYLES') }}"
    dx_user_tasks_count="{{ $user_tasks_count }}"
    dx_current_route="{{ Route::current()->getName()}}"
    dx_root_url="{{Request::root()}}/"
    dx_public_root_url="{{ get_portal_config('PORTAL_PUBLIC_URL') }}"
    dx_max_file_size="{{ ini_get('upload_max_filesize') }}"
    dx_post_max_size="{{ ini_get('post_max_size') }}"
    trans_data_processing="{{ trans('frame.data_processing') }}"
    trans_please_wait="{{ trans('frame.please_wait') }}"
    trans_sys_error="{{ trans('frame.sys_error') }}"
    trans_session_end="{{ trans('frame.session_end') }}"
    trans_general_error="{{ trans('frame.general_error') }}"
    trans_first_save_msg="{{ trans('frame.first_save_msg') }}"
    trans_data_saved="{{ trans('frame.data_saved') }}"
    trans_data_deleted="{{ trans('frame.data_deleted') }}"
    trans_data_deleted_all="{{ trans('frame.data_deleted_all') }}"
    trans_word_generating="{{ trans('frame.word_generating') }}"
    trans_word_generated="{{ trans('frame.word_generated') }}"
    trans_excel_downloaded="{{ trans('frame.excel_downloaded') }}"
    trans_file_downloaded="{{ trans('frame.file_downloaded') }}"
    trans_file_error="{{ trans('frame.file_error') }}"
    trans_confirm_delete="{{ trans('frame.confirm_delete') }}"
    trans_page_fullscreen="{{ trans('frame.page_fullscreen') }}"
    trans_page_boxed="{{ trans('frame.page_boxed') }}"
    trans_tree_close="{{ trans('fields.tree_close') }}"
    trans_tree_chosen="{{ trans('fields.tree_chosen') }}"
    trans_tree_cancel="{{ trans('fields.tree_cancel') }}"
    trans_tree_choose="{{ trans('fields.tree_choose') }}"
    trans_passw_form_title="{{ trans('password_form.form_title') }}"
  >
    <div class="dx-wrap">
      <!-- Simple splash screen-->
      <div class="splash">
        <div class="color-line"></div>
        <div class="splash-title">
          <h1>{{ $portal_name }}</h1>
          <p>{{ trans("frame.data_loading") }}</p>
          <img src="{{Request::root()}}/assets/global/progress/loading-bars.svg" width="64" height="64"/>
        </div>
      </div>
      
      <div class="container-fluid" style='background-color: white;'>
        <div class="row" style="margin-right: 0px!important;">
          <div class="col-xs-4 col-sm-4 col-md-2 znavbar-header">
            
            @if (!trans('index.logo_txt'))
              <a href="/">
                <img src="{{Request::root()}}/{{ Config::get('dx.logo_small', 'assets/global/logo/medus_black.png') }}" alt="LOGO" class="logo-default"/>
              </a>
            @else
              <a class="navbar-brand" href="/" style="text-decoration: none;">
                <div style="font-size: 28px; color: #213f5a; text-transform: uppercase; padding-top: 4px;">{{ trans('index.logo_txt') }}</div>
              </a>
            @endif
          </div>
          
          <div class="col-xs-6 col-sm-8 col-md-10">
            <ul class="nav navbar-nav navbar-right dx-top-right-menu">
            @if (Auth::check() && Auth::user()->id != Config::get('dx.public_user_id',0))
              
              <!-- BEGIN USER LOGIN DROPDOWN -->
                <li class="dropdown dropdown-user" style="padding: 0 0px;">
                  <a href="javascript:;" class="dropdown-toggle top-link" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                    <img src="{{Request::root()}}/{{ \App\Libraries\Helper::getUserAvatarSmall() }}" class="img-circle" alt="{{ Auth::user()->display_name }}" style="max-height: 24px;"/>
                    <span class="username hidden-xs"> {{ Auth::user()->display_name }} </span>
                    <i class="fa fa-angle-down"></i>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-default">
                    <li>
                      <a href="javascript:;" class="dx-user-change-passw-link">
                        <i class="fa fa-key dx-user-menu"></i> {{ trans("frame.password_change") }} </a>
                    </li>
                  </ul>
                </li>
                <!-- END USER LOGIN DROPDOWN -->
              
              
              @if ($user_tasks_count > 0)
                <!-- BEGIN TODO DROPDOWN -->
                  <li class="dropdown" id="dx_tasks_count_li">
                    <a href="{{Request::root()}}/skats_aktualie_uzdevumi" class="dropdown-toggle top-link" title="{{ trans("frame.tasks") }}">
                      <i class="fa fa-calendar"></i>
                      <div class="badge bg-red-soft" id="dx_tasks_count_badge"> {{ $user_tasks_count }} </div>
                    </a>
                  </li>
                  <!-- END TODO DROPDOWN -->
                @endif
                
                <li class="dropdown">
                  <a href="{{Request::root()}}/structure/doc_manual" title="{{ trans("frame.user_manual") }}" class="dropdown-toggle top-link">
                    <i class="fa fa-question-circle"></i>
                  </a>
                </li>
                
                <li class="dropdown">
                  <a href="{{Request::root()}}/logout" title="{{ trans("frame.logout") }}" class="top-link">
                    <i class="fa fa-sign-out"></i>
                  </a>
                </li>
              
              @endif
            </ul>
          </div>
          <div class="col-xs-2 hidden-sm hidden-md hidden-lg hidden-xl">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
          </div>
        </div>
      </div>
      
      <nav class="navbar dx-top-menu dx-nonfixed-top">
        <div class="container-fluid" style='background-color: #3e7c99;'>
          <nav id="navbar" class="navbar navbar-default navbar-collapse collapse" role="navigation">
            <ul class="nav navbar-nav dx-main-menu">
              {!! $menu_htm !!}
            </ul>
            <ul class="nav navbar-nav pull-right">
              
              <!-- BEGIN SEARCH BOX -->
              <li id="dx-search-box-top-li" style="margin-top: -9px;">
                @include('static_blocks.search_top')
              </li>
              <!-- END SEARCH BOX -->
            
            </ul>
          </nav>
        </div>
      </nav>
      
      <div class="container-fluid dx-page-container">
        <div id="dx-search-box-in-page">
        </div>
        <div class="page-content" id="td_data" style="padding: 15px;">
          @yield('main_content')
        </div>
        <div id="td_form_data">
        </div>
      </div>
      
      <!-- Scroll to top -->
      <div class="btn yellow-gold scroll-to-top">
        <i class="fa fa-lg fa-arrow-up"></i>
      </div>
      
      @include('elements.popup_info')
    </div>
    <script>
      dx_is_slider = {{ ((isset($is_slidable_menu) && $is_slidable_menu)) ? "1" : "0" }};
    </script>
    <!--[if lt IE 9]>
    <script src="{{Request::root()}}/metronic/global/plugins/respond.min.js"></script>
    <script src="{{Request::root()}}/metronic/global/plugins/excanvas.min.js"></script>
    <![endif]-->
    
    <script src="{{Request::root()}}/{{ getIncludeVersion('js/lang.js') }}" type='text/javascript'></script>
    
    <script type='text/javascript'>
      Lang.setLocale('{{ App::getLocale() }}');
    </script>
    
    <script src="{{ elixir('js/elix_plugins.js') }}" type='text/javascript'></script>
    <script src="{{ elixir('js/elix_mindwo_horizontal_menu.js') }}" type='text/javascript'></script>
    
    @yield('main_custom_javascripts')
    
    @if (Auth::check() && Auth::user()->id != Config::get('dx.public_user_id',0))
      <script src="{{ elixir('js/elix_userlinks.js') }}" type='text/javascript'></script>
    @endif
    
    {!! get_portal_config('GOOGLE_ANALYTIC') !!}
    
    <script>
      {!! get_portal_config('SCRIPT_JS') !!}
    </script>
    
    <script>
      $(document).ready(function()
      {
        // select all dropdown toggles under the top level
        $('.dx-main-menu .dropdown-submenu > a.dropdown-toggle').each(function()
        {
          $(this).click(function(e)
          {
            if($(window).width() < 768)
            {
              e.stopPropagation();
              
              // select the ul element next to the toggle (submenu itself)
              var submenu = $(this).next();
              
              if(submenu.is(':visible'))
              {
                // hide submenu and all open sub-submenus of it
                submenu.add('.dropdown-menu', submenu).hide();
              }
              else
              {
                // hide already open submenus at the same level
                $(this).parent().siblings('.dropdown-submenu').find('.dropdown-menu:visible').hide();
                submenu.show();
              }
            }
          });
        });
        
        // close open submenus when closing a top-level menu
        $('.dx-main-menu > li > a.dropdown-toggle').click(function()
        {
          if($(window).width() < 768)
          {
            // if user is closing menu, then hide submenus of it
            if($(this).attr('aria-expanded') == 'true')
            {
              $(this).next().find('.dropdown-menu:visible').hide();
            }
            // if user opens another menu, hide submenus of an already open menu
            else
            {
              $(this).parent().siblings('.open').find('.dropdown-submenu .dropdown-menu:visible').hide();
            }
          }
        });
      });
    </script>
    
    @if (isset($is_slidable_menu) && $is_slidable_menu)
      <script type='text/javascript' src="{!! asset('js/box/script.js') !!}"></script>
      <script>
        box.icon = $('<i class="fa fa-angle-double-right"></i>');
        box.dashboardReload = {{ (count($breadcrumb) > 0) ? 1 : 0}};
      </script>
    @endif
  
  </body>
</html>
