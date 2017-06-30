@extends('frame')

@section('main_content')
  <div class="portlet light">
    <div class="portlet-title">
      <div class="caption font-grey-cascade uppercase">
        <i class="fa fa-sitemap"></i> {{ trans('organization.deps_title') }}
        <span class="badge badge-info" title="{{ trans('organization.hint_count') }}">{{ count($departments) }}</span>
      </div>
      <div class="actions">
        <a class="btn btn-circle btn-icon-only btn-default" href="javascript:;" id="dx-org-zoom-in" title="{{ trans('organization.btn_zoom_in') }}">
          <i class="fa fa-search-plus"></i>
        </a>
        <a class="btn btn-circle btn-icon-only btn-default" href="javascript:;" id="dx-org-zoom-out" title="{{ trans('organization.btn_zoom_out') }}">
          <i class="fa fa-search-minus"></i>
        </a>
        <a class="btn btn-circle btn-icon-only btn-default" href="javascript:;" id="dx-org-export" title="{{ trans('organization.btn_export') }}">
          <i class="fa fa-download"></i>
        </a>
      </div>
    </div>
    <div class="portlet-body">
      <div class="row">
        <div class="col-md-12">
          <div id="dx-orgchart-container"></div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('main_custom_javascripts')
  <script src="{{ elixir('js/elix_orgdepartments.js') }}" type='text/javascript'></script>
  <script>
	  var orgchartData = {
		  source: {!! json_encode($datasource, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) !!},
		  route: '{{ route('organization_departments') }}'
	  };
  </script>
  @include('pages.view_js_includes')
@endsection

@section('main_custom_css')
  <link href="{{ elixir('css/elix_orgdepartments.css') }}" rel="stylesheet"/>
  @include('pages.view_css_includes')
@endsection