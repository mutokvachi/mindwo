@extends('frame')

@section('main_custom_css')  
  @include('pages.view_css_includes')
  <link href="{{ elixir('css/elix_menu_builder.css') }}" rel="stylesheet"/>
  <style>
      .dx-menu-builder-stick-title {
          font-size: 16px;
          padding-top: 32px!important;
          text-transform: uppercase;
      }
      .dx-menu-builder {
          width: 70%;
          margin: 0 auto;
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
  <div class="portlet light">
    <div class="portlet-title">
      <div class="caption font-grey-cascade uppercase">
        <i class="fa fa-sitemap"></i> {{ trans('constructor.menu.title') }}        
      </div>
     
    </div>
    <div class="portlet-body">
        <div class="dd">
            <ol class="dd-list">
                <li class="dd-item dd3-item" data-id="13">
                    <div class="dd-handle dd3-handle"> </div>
                    <div class="dd3-content"> Item 13 </div>
                </li>
                <li class="dd-item dd3-item" data-id="14">
                    <div class="dd-handle dd3-handle"> </div>
                    <div class="dd3-content"> Item 14 </div>
                </li>
                <li class="dd-item dd3-item" data-id="15">
                    <div class="dd-handle dd3-handle"> </div>
                    <div class="dd3-content"> Item 15 </div>
                    <ol class="dd-list">
                        <li class="dd-item dd3-item" data-id="16">
                            <div class="dd-handle dd3-handle"> </div>
                            <div class="dd3-content"> Item 16 </div>
                        </li>
                        <li class="dd-item dd3-item" data-id="17">
                            <div class="dd-handle dd3-handle"> </div>
                            <div class="dd3-content"> Item 17 </div>
                        </li>
                        <li class="dd-item dd3-item" data-id="18">
                            <div class="dd-handle dd3-handle"> </div>
                            <div class="dd3-content"> Item 18 </div>
                        </li>
                    </ol>
                </li>
            </ol>
        </div>
    </div>
  </div>
    <div class="dx-stick-footer animated bounceInUp">
      <div class='row'>
        <div class='col-lg-2 col-md-3 hidden-sm hidden-xs dx-left dx-menu-builder-stick-title'>
            <i class="fa fa-sitemap"></i>
            <span>{{ trans('constructor.menu.title') }}</span>
        </div>
        <div class='col-lg-10 col-md-9 col-sm-12 col-xs-12 dx-right'>
          <a href="javascript:;" class="btn btn-primary dx-save-btn">
            <i class="fa fa-save"></i> {{ trans('constructor.menu.save_btn') }}
          </a>
          <a href="javascript:;" class="btn btn-default dx-new-btn">
            <i class="fa fa-plus"></i> {{ trans('constructor.menu.new_btn') }} </a>
        </div>
      </div>
    </div>
</div>
@endsection

