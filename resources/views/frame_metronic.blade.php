<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!--><html lang="en"><!--<![endif]-->

    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8" />

        <title>{{ $portal_name }} :: {{ isset($page_title) ? $page_title : 'Intranet' }}</title>

        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
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

        <link rel="shortcut icon" href="{{Request::root()}}/favicon.ico">
    </head>
    <!-- END HEAD -->

    <body class="page-header-fixed page-content-white page-boxed page-sidebar-fixed dx-main-page" style='overflow: hidden;'
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

        <!-- BEGIN HEADER -->
        <div class="page-header navbar navbar-fixed-top">

            <!-- BEGIN HEADER INNER -->
            <div class="page-header-inner container">

                <div class="page-logo">
                    <a href="/" style="text-decoration: none;">
                         @if (!trans('index.logo_txt'))
                            <img src="{{Request::root()}}/{{ Config::get('dx.logo_small', 'assets/global/logo/logo-default.png') }}" alt="LOGO" class="logo-default" style="margin-top: 8px;"/>
                        @else
                            <div style="font-size: 28px; color: white; text-transform: uppercase; padding-top: 14px;">{{ trans('index.logo_txt') }}</div>
                        @endif
                    </a>
                    <div class="menu-toggler sidebar-toggler" dx_attr=""></div>
                </div>

                <!-- BEGIN RESPONSIVE MENU TOGGLER -->
                <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"> </a>
                <!-- END RESPONSIVE MENU TOGGLER -->

                <div class="page-top">
                    <div class="top-menu pull-left">
                        <ul class="nav navbar-nav pull-right">

                            <li class="dropdown" style="color: #b4bcc8; font-size: 13px; margin: 17px 0 0 15px;">
                                {!! $special_days !!}
                            </li>

                            @if (Auth::check() && Auth::user()->id != Config::get('dx.public_user_id',0))

                            <!-- BEGIN USER LOGIN DROPDOWN -->
                            <li class="dropdown dropdown-user" style="padding: 0 0px; margin-left: 26px;">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                    <img src="{{Request::root()}}/{{ \App\Libraries\Helper::getUserAvatarSmall() }}" class="img-circle" alt="{{ Auth::user()->display_name }}" />
                                    <span class="username username-hide-on-mobile"> {{ Auth::user()->display_name }} </span>
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-default">
                                    <li>
                                        <a href="javascript:;" title="{{ trans("frame.page_size") }}" id='btnScreen'>
                                            <i class="fa fa-arrows-alt"></i> {{ trans("frame.page_fullscreen") }} </a>
                                    </li>
                                    <li>
                                        <a href="javascript:;" title="{{ trans("frame.password_change") }}" class="dx-user-change-passw-link">
                                            <i class="fa fa-key"></i> {{ trans("frame.password_change") }} </a>
                                    </li>
                                    <li>
                                        <a href="{{Request::root()}}/logout" title="{{ trans("frame.logout") }}">
                                            <i class="fa fa-sign-out"></i> {{ trans("frame.logout") }} </a>
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
                                        <h3>{{ trans("frame.tasks") }}:
                                            <span class="bold">{{ $user_tasks_count }}</span></h3>
                                        <a href="{{Request::root()}}/skats_aktualie_uzdevumi">{{ trans("frame.open") }}</a>
                                    </li>                                
                                </ul>
                            </li>
                            <!-- END TODO DROPDOWN -->
                            @endif
                            <!--
                            <li class="dropdown">
                                <a href="{{Request::root()}}/structure/doc_manual" title="{{ trans("frame.user_manual") }}" class="dropdown-toggle" style="padding: 28px 16px 20px 16px!important;">
                                    <i class="fa fa-question-circle"></i>                                
                                </a>
                            </li>
                            -->
                            @endif
                        </ul>
                    </div>


                    <!-- BEGIN TOP NAVIGATION MENU -->
                    <div class="top-menu hidden-xs hidden-sm">
                        <ul class="nav navbar-nav pull-right">

                            <!-- BEGIN SEARCH BOX -->
                            <li id="dx-search-box-top-li">
                                @include('static_blocks.search_top')
                            </li>
                            <!-- END SEARCH BOX -->

                        </ul>
                    </div>
                </div>
            </div>
            <!-- END HEADER INNER -->
        </div>
        <!-- END HEADER -->

        <div class="clearfix"></div>

        <!-- BEGIN CONTAINER -->
        <div class="container">
            <div class="page-container">

                @if (!isset($is_slidable_menu) || !$is_slidable_menu)
                <!-- BEGIN SIDEBAR -->
                <div class="page-sidebar-wrapper">
                    <!-- BEGIN SIDEBAR -->
                    <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
                    <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
                    <div class="page-sidebar navbar-collapse collapse">
                        <!-- BEGIN SIDEBAR MENU -->
                        <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
                        <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
                        <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
                        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
                        <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
                        <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
                        <ul class="page-sidebar-menu  page-header-fixed page-sidebar-menu-compact" data-keep-expanded="true" data-auto-scroll="false" data-slide-speed="200" style="padding-top: 20px">
                            <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
                            <li class="sidebar-toggler-wrapper hide">
                                <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                                <div class="sidebar-toggler"> </div>
                                <!-- END SIDEBAR TOGGLER BUTTON -->
                            </li>                    
                            {!! $menu_htm !!}
                        </ul>
                        <!-- END SIDEBAR MENU -->
                        <!-- END SIDEBAR MENU -->
                    </div>
                    <!-- END SIDEBAR -->
                </div>
                <!-- END SIDEBAR -->
                @endif




                <!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">                
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content" id="td_data">
                        <div id="dx-search-box-in-page">                        
                        </div>
                        @if (isset($is_slidable_menu) && $is_slidable_menu)                    
                        <div class="page-bar">
                            <ul id="breadcrumbs" class="page-breadcrumb no-margins">
                                <li id="crumb-page-holder"><a href="javascript:;" name="backSlide" data-dx-id="page-holder">Sākums</a></li>
                                @if (count($breadcrumb) > 0)
                                <li id="crumb-0"><i class="fa fa-angle-double-right"></i><a href="javascript:;" name="backSlide" data-dx-id="0">Izvēlne</a></li>
                                @endif
                                @foreach($breadcrumb as $item)
                                <li id="crumb-{{ $item["id"] }}"><i class="fa fa-angle-double-right"></i><a href="javascript:;" name="backSlide" data-dx-id="{{ $item["id"] }}">{{ $item["title"] }}</a></li>
                                @endforeach
                            </ul>
                            <div class="page-toolbar" name="{{ (count($breadcrumb) > 0) ? 'backSlide' : 'actionSlide'}}" data-dx-id="0">
                                <div id="dashboard-report-range" class="pull-right tooltips btn btn-fit-height green">
                                    <i class="fa fa-th-large"></i>&nbsp;
                                    <span class="thin uppercase">Izvēlne</span>
                                </div>
                            </div>
                        </div>
                        <div id="slide-page-holder">
                            <div style="position: relative;">
                                @yield('main_content')
                            </div>
                        </div>
                        {!! $slidable_htm !!}
                        @else
                        @yield('main_content')
                        @endif
                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

            </div>
        </div>
        <!-- END CONTAINER -->

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
