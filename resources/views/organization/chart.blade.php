@extends('frame')

@section('main_content')
  <div class="portlet light">
    <div class="portlet-title">
      <div class="caption font-grey-cascade uppercase">
        <i class="fa fa-sitemap"></i> {{ trans('organization.chart_title') }}
        <span class="badge badge-info" title="{{ trans('organization.hint_count') }}">{{ count($employees) }}</span>
      </div>
      <div class="actions">
        <a class="btn btn-circle btn-icon-only btn-default" href="javascript:;" id="dx-org-zoom-in" title="{{ trans('organization.btn_zoom_in') }}">
          <i class="fa fa-search-plus"></i>
        </a>
        <a class="btn btn-circle btn-icon-only btn-default" href="javascript:;" id="dx-org-zoom-out" title="{{ trans('organization.btn_zoom_out') }}">
          <i class="fa fa-search-minus"></i>
        </a>
        <a class="btn btn-circle btn-icon-only btn-default" href="javascript:;" id="dx-org-expand-all" title="{{ trans('organization.btn_expand_all') }}">
          <i class="fa fa-expand"></i>
        </a>
        <a class="btn btn-circle btn-icon-only btn-default" href="javascript:;" id="dx-org-collapse-all" title="{{ trans('organization.btn_collapse_all') }}">
          <i class="fa fa-compress"></i>
        </a>
        <a class="btn btn-circle btn-icon-only btn-default" href="javascript:;" id="dx-org-export" title="{{ trans('organization.btn_export') }}">
          <i class="fa fa-download"></i>
        </a>
      </div>
    </div>
    <div class="portlet-body">
      <div class="row">
        <div class="col-md-12">
          <select class="dx-orgchart-select pull-left">
            @foreach($employees as $id => $employee)
              <option value="{{ $id }}" data-levels="{{ isset($levels[$id]) ? $levels[$id] : 1 }}" {{ $id == $rootId ? 'selected' : '' }}>
                {{ $employee->display_name }} ({{ $employee->position_title }})
              </option>
            @endforeach
          </select>
          <select class="dx-orgchart-levels form-control pull-left">
            @for($i = 1; $i <= $startLevels; $i++)
              <option{{ $i == $displayLevels ? ' selected' : '' }}>{{ $i }}</option>
            @endfor
          </select>
          <button type="button" class="dx-orgchart-filter btn btn-primary pull-left">
            <i class="fa fa-filter"></i> {{ trans('organization.btn_filter') }}
          </button>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div id="dx-orgchart-container"></div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('main_custom_javascripts')
  <script>
	  var orgchartData = {
		  source: {!! json_encode($datasource, JSON_UNESCAPED_SLASHES) !!},
		  route: '{{ route('organization_chart') }}',
		  displayLevels: {{ $displayLevels }}
	  };
  </script>
  <script src="{{ elixir('js/elix_orgchart.js') }}" type='text/javascript'></script>
@endsection

@section('main_custom_css')
  <link href="{{ elixir('css/elix_orgchart.css') }}" rel="stylesheet"/>
@endsection