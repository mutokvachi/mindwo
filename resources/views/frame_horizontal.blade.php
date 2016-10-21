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
    
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    
    <!-- BEGIN PLUGINS STYLES -->
    <link href="{{Request::root()}}/metronic/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="{{Request::root()}}/metronic/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />    
    <link href="{{ elixir('css/elix_plugins.css') }}" rel="stylesheet" type="text/css" />
    <!-- BEGIN PLUGINS STYLES -->

    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="{{Request::root()}}/metronic/global/css/components-md.min.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="{{Request::root()}}/metronic/global/css/plugins-md.min.css" rel="stylesheet" type="text/css" />
    <!-- END THEME GLOBAL STYLES -->

    <!-- BEGIN THEME LAYOUT STYLES -->
    <link href="{{Request::root()}}/metronic/layouts/layout2/css/layout.css" rel="stylesheet" type="text/css" />
    <link href="{{Request::root()}}/metronic/layouts/layout2/css/themes/blue.min.css" rel="stylesheet" type="text/css" id="style_color" />
    <link href="{{Request::root()}}/metronic/layouts/layout2/css/custom.min.css" rel="stylesheet" type="text/css" />
    <!-- END THEME LAYOUT STYLES -->

    @if ($user_tasks_count > 0)
    <!-- Gritter -->
    <link href="{{Request::root()}}/plugins/gritter/jquery.gritter.css" rel="stylesheet" />
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

    <link href="{{ elixir('css/elix_mindwo.css') }}" rel="stylesheet" type="text/css" />
    
    <link href="{{ elixir('css/elix_mindwo_horizontal.css') }}" rel="stylesheet" type="text/css" />      
    
    <link rel="shortcut icon" href="{{Request::root()}}/favicon.ico">
  </head>

  <body class="dx-main-page"
          dx_valid_html_elements = "{{ get_portal_config('VALID_HTML_ELEMENTS') }}"
          dx_valid_html_styles = "{{ get_portal_config('VALID_HTML_STYLES') }}"
          dx_user_tasks_count = "{{ $user_tasks_count }}"
          dx_current_route = "{{ Route::current()->getName()}}"
          dx_root_url = "{{Request::root()}}/"
          dx_public_root_url = "{{ get_portal_config('PORTAL_PUBLIC_URL') }}"
          dx_max_file_size = "{{ ini_get('upload_max_filesize') }}"
          dx_post_max_size = "{{ ini_get('post_max_size') }}"
          trans_data_processing = "{{ trans('frame.data_processing') }}"
          trans_please_wait = "{{ trans('frame.please_wait') }}"
          trans_sys_error = "{{ trans('frame.sys_error') }}"
          trans_session_end = "{{ trans('frame.session_end') }}"
          trans_general_error = "{{ trans('frame.general_error') }}"
          trans_first_save_msg = "{{ trans('frame.first_save_msg') }}"
          trans_data_saved = "{{ trans('frame.data_saved') }}"
          trans_data_deleted = "{{ trans('frame.data_deleted') }}"
          trans_data_deleted_all = "{{ trans('frame.data_deleted_all') }}"
          trans_word_generating = "{{ trans('frame.word_generating') }}"
          trans_word_generated = "{{ trans('frame.word_generated') }}"
          trans_excel_downloaded = "{{ trans('frame.excel_downloaded') }}"
          trans_file_downloaded = "{{ trans('frame.file_downloaded') }}"
          trans_file_error = "{{ trans('frame.file_error') }}"
          trans_confirm_delete = "{{ trans('frame.confirm_delete') }}"
          trans_page_fullscreen = "{{ trans('frame.page_fullscreen') }}"
          trans_page_boxed = "{{ trans('frame.page_boxed') }}"
          trans_tree_close = "{{ trans('fields.tree_close') }}"
          trans_tree_chosen = "{{ trans('fields.tree_chosen') }}"
          trans_tree_cancel = "{{ trans('fields.tree_cancel') }}"
          trans_tree_choose = "{{ trans('fields.tree_choose') }}"
          trans_passw_form_title = "{{ trans('password_form.form_title') }}"
    >

    <!-- Simple splash screen-->    
    <div class="splash">
        <div class="color-line"></div>
        <div class="splash-title">
            <h1>{{ $portal_name }}</h1><p>{{ trans("frame.data_loading") }}</p><img src="{{Request::root()}}/assets/global/progress/loading-bars.svg" width="64" height="64" />
        </div>
    </div> 
      
    <nav class="navbar navbar-fixed-top">
      <div class="container-fluid" style='background-color: white;'>
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
            
                @if (trans('index.logo'))
                <a href="/">
                    <img src="{{Request::root()}}/{{ trans('index.logo_small') }}" alt="LOGO" class="logo-default" />
                </a>
                @else
                <a class="navbar-brand" href="/" style="text-decoration: none;">
                    <div style="font-size: 28px; color: #213f5a; text-transform: uppercase; padding-top: 4px;">{{ trans('index.logo_txt') }}</div>
                </a>
                @endif
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            @if (Auth::check() && Auth::user()->id != Config::get('dx.public_user_id',0))

                <!-- BEGIN USER LOGIN DROPDOWN -->
                <li class="dropdown dropdown-user" style="padding: 0 0px; margin-left: 26px;">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <img src="{{Request::root()}}/formated_img/small_avatar/{{ (Auth::user()->picture_guid) ? Auth::user()->picture_guid : get_portal_config('EMPLOYEE_AVATAR') }}" class="img-circle" alt="{{ Auth::user()->display_name }}" style="width: 24px;"/>
                        <span class="username username-hide-on-mobile"> {{ Auth::user()->display_name }} </span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-default">
                        <li>
                            <a href="#" title="Mainīt lapas izmēru" id='btnScreen'>
                                <i class="icon-size-fullscreen"></i> Lapa pa visu ekrānu </a>
                        </li>
                        <li>
                            <a href="#" title="Paroles maiņa" class="dx-user-change-passw-link">
                                <i class="icon-key"></i> Paroles maiņa </a>
                        </li>
                        <li>
                            <a href="{{Request::root()}}/logout" title="Iziet">
                                <i class="fa fa-sign-out"></i> Iziet </a>
                        </li>
                    </ul>
                </li>
                <!-- END USER LOGIN DROPDOWN -->


               @if ($user_tasks_count > 0)
                    <!-- BEGIN TODO DROPDOWN -->
                    <li class="dropdown dropdown-extended dropdown-tasks hidden-xs hidden-sm" id="dx_tasks_count_li">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <i class="fa fa-calendar"></i>
                            <span class="badge badge-default" id="dx_tasks_count_badge"> {{ $user_tasks_count }} </span>
                        </a>
                        <ul class="dropdown-menu extended tasks">
                            <li class="external">
                                <h3>{{ trans("frame.tasks") }}
                                    <span class="bold">{{ $user_tasks_count }}</span></h3>
                                <a href="{{Request::root()}}/skats_aktualie_uzdevumi">{{ trans("frame.open") }}</a>
                            </li>                                
                        </ul>
                    </li>
                    <!-- END TODO DROPDOWN -->
                @endif

                <li class="dropdown">
                    <a href="{{Request::root()}}/structure/doc_manual" title="Lietotāja rokasgrāmata" class="dropdown-toggle">
                        <i class="fa fa-question-circle"></i>                                
                    </a>
                </li>

            @endif
          </ul>          
        </div>
      </div>
      <div class="container-fluid" style='background-color: #2D5F8B;'>
          <nav class="navbar navbar-default" role="navigation">
            <ul class="nav navbar-nav">
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
        <div class="page-content" id="td_data">
          <div id="dx-search-box-in-page">                        
          </div>
          @yield('main_content')
        </div>
    </div>
      
    <!-- Scroll to top -->
    <div class="btn yellow-gold scroll-to-top">
        <i class="fa fa-lg fa-arrow-up"></i>
    </div>

    @include('elements.popup_info')

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

    <script src = "{{ elixir('js/elix_plugins.js') }}" type='text/javascript'></script>

    @yield('main_custom_javascripts')

    @if (Auth::check() && Auth::user()->id != Config::get('dx.public_user_id',0))
    <script src = "{{ elixir('js/elix_userlinks.js') }}" type='text/javascript'></script>
    @endif

    {!! get_portal_config('GOOGLE_ANALYTIC') !!}

    <script>
        {!! get_portal_config('SCRIPT_JS') !!}
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
