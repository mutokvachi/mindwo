<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    
    <link rel="icon" href="{{Request::root()}}/favicon.ico">

    <title>{{ $portal_name }} :: {{ ($page_title) ? $page_title : 'Intranet' }}</title>

    <!-- Bootstrap core CSS -->
    <link href="{{Request::root()}}/metronic/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <style>
        
        /*!
        * IE10 viewport hack for Surface/desktop Windows 8 bug
        * Copyright 2014-2015 Twitter, Inc.
        * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
        */

       /*
        * See the Getting Started docs for more information:
        * http://getbootstrap.com/getting-started/#support-ie10-width
        */
       @-ms-viewport     { width: device-width; }
       @-o-viewport      { width: device-width; }
       @viewport         { width: device-width; }
    </style>
    
    <!-- Custom styles for this template -->
    <style>
        /*
        * Base structure
        */

       /* Move down content because we have a fixed navbar that is 50px tall */
       body {
         padding-top: 50px;
       }

       /*
        * Global add-ons
        */

       .sub-header {
         padding-bottom: 10px;
         border-bottom: 1px solid #eee;
       }

       /*
        * Top navigation
        * Hide default border to remove 1px line.
        */
       .navbar-fixed-top {
         border: 0;
       }

       /*
        * Sidebar
        */

       /* Hide for mobile, show later */
       .sidebar {
         display: none;
       }
       @media (min-width: 768px) {
         .sidebar {
           position: fixed;
           top: 51px;
           bottom: 0;
           left: 0;
           z-index: 1000;
           display: block;
           padding: 20px;
           overflow-x: hidden;
           overflow-y: auto; /* Scrollable contents if viewport is shorter than content. */
           background-color: #f5f5f5;
           border-right: 1px solid #eee;
         }
       }

       /* Sidebar navigation */
       .nav-sidebar {
         margin-right: -21px; /* 20px padding + 1px border */
         margin-bottom: 20px;
         margin-left: -20px;
       }
       .nav-sidebar > li > a {
         padding-right: 20px;
         padding-left: 20px;
       }
       .nav-sidebar > .active > a,
       .nav-sidebar > .active > a:hover,
       .nav-sidebar > .active > a:focus {
         color: #fff;
         background-color: #428bca;
       }


       /*
        * Main content
        */

       .main {
         padding: 20px;
       }
       @media (min-width: 768px) {
         .main {
           padding-right: 40px;
           padding-left: 40px;
         }
       }
       .main .page-header {
         margin-top: 0;
       }
    </style>
    
    <link href="{{Request::root()}}/metronic/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="{{Request::root()}}/plugins/modal_bootstrap/css/bootstrap-modal.css" rel="stylesheet">    
    <link href="{{Request::root()}}/css/plugins/toastr/toastr.min.css" rel="stylesheet">    
    <link href="{{Request::root()}}/plugins/tooltipster-master/css/tooltipster.css" rel="stylesheet">
    <link href="{{Request::root()}}/plugins/tooltipster-master/css/themes/tooltipster-light.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    @yield('main_custom_css')
  </head>

  <body class="dx-main-page"
      dx_valid_html_elements = "{{ get_portal_config('VALID_HTML_ELEMENTS') }}"
      dx_valid_html_styles = "{{ get_portal_config('VALID_HTML_STYLES') }}"
      dx_user_tasks_count = "{{ $user_tasks_count }}"
      dx_current_route = "{{ Route::current()->getName()}}"
      dx_root_url = "{{Request::root()}}/"
      dx_public_root_url = "{{ get_portal_config('PORTAL_PUBLIC_URL') }}"
      dx_max_file_size = "{{ ini_get('post_max_size') }}"
      >

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">MEDUS</a>
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
                            <i class="icon-calendar"></i>
                            <span class="badge badge-default" id="dx_tasks_count_badge"> {{ $user_tasks_count }} </span>
                        </a>
                        <ul class="dropdown-menu extended tasks">
                            <li class="external">
                                <h3>Izpildāmie uzdevumi:
                                    <span class="bold">{{ $user_tasks_count }}</span></h3>
                                <a href="{{Request::root()}}/skats_aktualie_uzdevumi">Atvērt</a>
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
          <form class="navbar-form navbar-right">
            <input type="text" class="form-control" placeholder="Search...">
          </form>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            {!! $menu_htm !!}
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <div class="page-content" id="td_data">
            <div id="dx-search-box-in-page">                        
            </div>
            @yield('main_content')
          </div>          
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="{{Request::root()}}/metronic/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="{{Request::root()}}/metronic/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script>
        /*!
        * IE10 viewport hack for Surface/desktop Windows 8 bug
        * Copyright 2014-2015 Twitter, Inc.
        * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
        */

       // See the Getting Started docs for more information:
       // http://getbootstrap.com/getting-started/#support-ie10-width

       (function () {
         'use strict';

         if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
           var msViewportStyle = document.createElement('style')
           msViewportStyle.appendChild(
             document.createTextNode(
               '@-ms-viewport{width:auto!important}'
             )
           )
           document.querySelector('head').appendChild(msViewportStyle)
         }

       })();
    </script>
    <!-- Make modals draggable -->
    <script src="{{Request::root()}}/metronic/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    
    <script src="{{Request::root()}}/metronic/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="{{Request::root()}}/js/plugins/toastr/toastr.js" type="text/javascript"></script>
    
    <script src="{{Request::root()}}/plugins/modal_bootstrap/js/bootstrap-modalmanager.js" type="text/javascript"></script>
    <script src="{{Request::root()}}/plugins/modal_bootstrap/js/bootstrap-modal.js" type="text/javascript"></script>
       
    <script src="{{Request::root()}}/{{ getIncludeVersion('js/dx_core.js') }}" type="text/javascript"></script>
    
    <script src="{{Request::root()}}/js/jquery.cookie.js" type="text/javascript"></script>
    
    <script src="{{Request::root()}}/plugins/tooltipster-master/js/jquery.tooltipster.min.js" type="text/javascript"></script>
    
    <script src='{{Request::root()}}/{{ getIncludeVersion('js/pages/main.js') }}' type='text/javascript'></script>
    <script src='{{Request::root()}}/{{ getIncludeVersion('js/pages/employees_links.js') }}' type='text/javascript'></script>
    <script src='{{Request::root()}}/{{ getIncludeVersion('js/pages/search_top.js') }}' type='text/javascript'></script>
    
    @if (Auth::check() && Auth::user()->id != Config::get('dx.public_user_id',0))
        <script src='{{Request::root()}}/{{ getIncludeVersion('js/pages/userlinks.js') }}' type='text/javascript'></script>
    @endif
    
    {!! get_portal_config('GOOGLE_ANALYTIC') !!}
    
    <script>
        {!! get_portal_config('SCRIPT_JS') !!}
    </script>
    
    @yield('main_custom_javascripts')
    
  </body>
</html>
