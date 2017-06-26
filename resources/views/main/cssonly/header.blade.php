<div id="dx-header-wrap">
  <div class="container-fluid">
    <div class="row" style="margin-right: 0px!important;">
      <div class="col-xs-6 col-sm-4 col-md-2 navbar-header">
      
        @if(!trans('index.logo_txt'))
          <a href="/">
            @if(($theme = \App\Models\UI\Theme::getCurrent()) && file_exists(public_path('assets/global/logo/mindwo-'.strtolower($theme->title).'.png')))
              <img src="{{ asset('assets/global/logo/mindwo-'.strtolower($theme->title).'.png') }}" alt="LOGO" class="logo-default"/>
            @else
              <img src="{{ asset(config('dx.logo_small', 'assets/global/logo/medus_black.png')) }}" alt="LOGO" class="logo-default"/>
            @endif
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
                @if(config('dx.is_horizontal_menu'))
                  <li class="dropdown-submenu">
                    <a href="javascript:;" class="dx-user-change-design-link">
                      <i class="fa fa-eye dx-user-menu"></i> {{ trans("frame.change_design") }}
                    </a>
                    <ul class="dropdown-menu">
                      @foreach(\App\Models\UI\Theme::all() as $theme)
                        <li>
                          <a href="javascript:;" class="dx-theme-link" data-theme-id="{{ $theme->id }}"
                            {!! Auth::user()->ui_theme_id == $theme->id ? ' style="font-weight: bold"' : '' !!}>
                            {{ $theme->title }}
                          </a>
                        </li>
                      @endforeach
                    </ul>
                  </li>
                @endif
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
        
          @include('static_blocks.chat_notif')
        
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
</div>