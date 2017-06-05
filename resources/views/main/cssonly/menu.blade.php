<div id="dx-menu-wrap" class="dx-top-menu dx-nonfixed-top">
  <div class="container-fluid" style="padding: 0">
    <div class="col-xs-12 col-sm-7 col-md-8 col-lg-9">
      <nav id="navbar" class="navbar navbar-default navbar-collapse collapse" role="navigation">
        <ul class="nav navbar-nav dx-main-menu">
          {!! $menu_htm !!}
          <li id="more-items-wrap" class="last-item">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
              {{ trans('frame.more') }} <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" id="more-items"></ul>
          </li>
        </ul>
      </nav>
    </div>
    <div class="col-xs-12 col-sm-5 col-md-4 col-lg-3">
      @include('main.cssonly.search_top')
    </div>
  </div>
</div>