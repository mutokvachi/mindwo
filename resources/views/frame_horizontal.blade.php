<!DOCTYPE html>
<html>
  <head>
    @include('main.head_meta')
    @include('main.head_styles')
  </head>
  
  <body
    class="dx-main-page dx-horizontal-menu-ui dx-no-cssonly"
    style="overflow: hidden;"
    @include('main.body_attributes')
  >
    <div class="dx-wrap">
      {{-- Simple splash screen --}}
      @include('main.splash')
      
      <div class="container-fluid" style='background-color: white;'>
        <div class="row" style="margin-right: 0px!important;">
          <div class="col-xs-6 col-sm-4 col-md-2 navbar-header">
            
            @if(!trans('index.logo_txt'))
              <a href="/">
                <img src="{{ asset(config('dx.logo_small', 'assets/global/logo/medus_black.png')) }}" alt="LOGO" class="logo-default"/>
              </a>
            @else
              <a class="navbar-brand" href="/" style="text-decoration: none;">
                <div style="font-size: 28px; color: #213f5a; text-transform: uppercase; padding-top: 4px;">{{ trans('index.logo_txt') }}</div>
              </a>
            @endif
          </div>
          
          <div class="col-xs-6 col-sm-8 col-md-10" style="padding-right: 20px">
            <button type="button" class="navbar-toggle collapsed dx-main-menu-toggle" style="top: 8px;" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <ul class="nav navbar-nav navbar-right dx-top-right-menu">
            @if(Auth::check() && Auth::user()->id != config('dx.public_user_id', 0))
              
              <!-- BEGIN USER LOGIN DROPDOWN -->
                <li class="dropdown dropdown-user" style="padding: 0 0px;">
                  <a href="javascript:;" class="dropdown-toggle top-link" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                    <img src="{{Request::root()}}/{{ \App\Libraries\Helper::getUserAvatarSmall() }}" class="img-circle" alt="{{ Auth::user()->display_name }}" style="max-height: 24px;"/>
                    <span class="username hidden-xs"> {{ Auth::user()->display_name }} </span>
                    <i class="fa fa-angle-down"></i>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-default dx-sub-menu-right">
                    <li>
                      <a href="javascript:;" class="dx-user-change-passw-link">
                        <i class="fa fa-key dx-user-menu"></i> {{ trans("frame.password_change") }} </a>
                    </li>
                    <!--
                    <li class="hidden-sm hidden-md hidden-lg">
                      <a href="{{Request::root()}}/structure/doc_manual" class="">
                        <i class="fa fa-question-circle"></i> {{ trans("frame.user_manual") }}
                      </a>
                    </li>
                    -->
                    <li class="hidden-sm hidden-md hidden-lg">
                      <a href="{{Request::root()}}/logout" class="">
                        <i class="fa fa-sign-out dx-user-menu"></i> {{ trans("frame.logout") }}
                      </a>
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
                <!--
                <li class="dropdown hidden-xs">
                  <a href="{{Request::root()}}/structure/doc_manual" title="{{ trans("frame.user_manual") }}" class="dropdown-toggle top-link">
                    <i class="fa fa-question-circle"></i>
                  </a>
                </li>
                -->
                <li class="dropdown hidden-xs">
                  <a href="{{Request::root()}}/logout" title="{{ trans("frame.logout") }}" class="top-link">
                    <i class="fa fa-sign-out"></i>
                  </a>
                </li>
              
              @endif
            </ul>
            <!-- hidden-sm hidden-md hidden-lg -->
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
      
      @include('main.content')
      @include('main.scroll_top')
      @include('elements.popup_info')
    </div>
    @include('main.modal_dialog')
    @include('main.body_scripts')
  </body>
</html>
