@extends('frame')

@section('main_content')
  <div class="portlet light">
    <div class="portlet-title">
        <div class="caption font-grey-cascade uppercase"><i class="fa fa-sitemap"></i> {{ trans('organization.chart_title') }} <span class="badge badge-info" title="{{ trans('organization.hint_count') }}">{{ count($employees) }}</span></div>
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
  <script src="{{ elixir('js/elix_orgchart.js') }}" type='text/javascript'></script>
  <script>
	  var orgchartDatasource = {!! json_encode($datasource, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) !!};
	  
	  $(document).ready(function()
	  {
		  $('.dx-orgchart-select').select2();
		  $('.dx-orgchart-select').change(function(e)
		  {
			  var count = $(this).children(':selected').data('levels');
			  var levels = $('.dx-orgchart-levels');
			  levels.children().remove();
			  for(var i = 1; i <= count; i++)
			  {
				  levels.append('<option' + (i == 2 ? ' selected' : '') + '>' + i + '</option>');
			  }
		  });
		  $('.dx-orgchart-filter').click(function(e)
		  {
			  window.location = '{{ route('organization_chart') }}'
				  + '/' + $('.dx-orgchart-select :selected').attr('value')
				  + '?displayLevels=' + $('.dx-orgchart-levels').val();
		  });
		  // init orgchart plugin
		  var orgchart = new OrgChart({
			  chartContainer: '#dx-orgchart-container',
			  data: orgchartDatasource,
			  nodeContent: 'title',
			  depth: {{ $displayLevels }},
			  toggleSiblingsResp: true,
			  pan: true,
			  //zoom: true,
			  //exportButton: true,
        // customize node creation process
			  createNode: function(node, data)
			  {
				  var content = $(node).children('.content');
				  content.wrapInner('<div class="text"></div>');
				  content.prepend('<a href="' + data.href + '"><img src="' + data.avatar + '" alt=""></a>');
				  
				  if(data.subordinates > 0)
					  content.append('<div class="subordinates" title="' + Lang.get('organization.hint_subord') + '">' + data.subordinates + '</div>');
				  
				  // add up arrow button to top node
				  if(data.hasParent)
					  $(node).append('<i class="edge verticalEdge topEdge fa"></i>');
			  }
		  });
		  // save original handler of click event of up arrow button
			orgchart._clickTopEdgeOld = orgchart._clickTopEdge;
  		// override event handler of up arrow button
		  orgchart._clickTopEdge = function(event)
		  {
			  var node = $(event.target).parents('.node').first();
			  var data = node.data('source');
			  
			  if(data.top)
				  location.href = data.parentUrl;
			  
			  else
				  this._clickTopEdgeOld(event);
		  };
                  
                $("#dx-org-zoom-in").click(function() {
                   orgchart.set_zoom(-1);
                });
                
                $("#dx-org-zoom-out").click(function() {
                   orgchart.set_zoom(1);
                });
                
                $("#dx-org-export").click(function() {
                   orgchart._clickExportButton();
                });
	  });
  </script>
@endsection

@section('main_custom_css')
  <link href="{{ elixir('css/elix_orgchart.css') }}" rel="stylesheet"/>
  <style>
    .dx-orgchart-select {
      min-width: 300px;
      margin-right: 20px;
    }
    
    .dx-orgchart-levels {
      width: 60px;
      margin-right: 20px;
    }
    
    .orgchart {
      background-image: none;
      width: 100%;
    }
    
    .orgchart td {
      margin: 0;
      padding: 0;
    }
    
    .orgchart .node {
      width: 170px;
      border: 0px none !important;
      background-color: transparent;
      padding: 3px;
      margin: 0;
    }
    
    .orgchart .node:hover {
      border: 0px none !important;
      background-color: transparent;
    }
    
    .orgchart .node .title {
      background-color: white;
      color: black;
      font-weight: normal;
      padding: 5px;
      height: auto;
      border: 1px solid #ccc;
      border-bottom: none;
    }
    
    .orgchart .node .title i {
      display: none;
    }
    
    .orgchart .node .content {
      height: 140px;
      white-space: normal;
      border-top: none;
      padding: 5px;
      border: 1px solid #ccc;
      border-top: none;
    }
    
    .orgchart .node .content .text {
      height: 32px;
      line-height: 15px;
      vertical-align: middle;
      padding-top: 8px;
    }
    
    .orgchart .node a {
      display: block;
    }
    
    .orgchart .node img {
      width: 80px;
      height: 80px;
      border-radius: 50% !important;
    }
    
    .orgchart tr.lines td {
      border-color: #ccc !important;
    }
    
    .orgchart tr.lines .downLine {
      background-color: #ccc !important;
    }
    
    .orgchart .subordinates {
      float: right;
    }
    
    .oc-export-btn {
        right: 15px!important;
        top: -100px!important;
        background-color: #3e7c99!important;
        border-color: #2e6da4!important;
    }
    
    .actions .btn-icon-only {
        border: 1px solid #ccc!important;
    }
  </style>
@endsection