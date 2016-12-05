@extends('frame')

@section('main_content')
  <div class="portlet light">
    <div class="portlet-title">
      <div class="caption font-grey-cascade uppercase">Orgchart ({{ count($employees) }})</div>
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
              <option>{{ $i }}</option>
            @endfor
          </select>
          <button type="button" class="dx-orgchart-filter btn btn-primary pull-left">
            <i class="fa fa-filter"></i>
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
				  levels.append('<option>' + i + '</option>');
			  }
		  });
		  $('.dx-orgchart-filter').click(function(e)
		  {
			  window.location = '{{ route('organization_chart') }}'
				  + '/' + $('.dx-orgchart-select :selected').attr('value')
				  + '?displayLevels=' + $('.dx-orgchart-levels').val();
		  });
		  
		  var ajaxURLs = {
  		  'parent': '{{ route('organization_chart_parent').'/' }}'
		  };
		  
		  var orgchart = new OrgChart({
			  chartContainer: '#dx-orgchart-container',
			  data: orgchartDatasource,
			  nodeContent: 'title',
			  depth: {{ $displayLevels }},
			  ajaxURL: ajaxURLs,
			  toggleSiblingsResp: true,
			  createNode: function(node, data)
			  {
				  var content = $(node).children('.content');
				  content.wrapInner('<div class="text"></div>');
				  content.prepend('<a href="' + data.href + '"><img src="' + data.avatar + '" alt=""></a>');
				  
				  if(data.subordinates > 0)
					  content.append('<div class="subordinates" title="Number of subordinates">' + data.subordinates + '</div>');
			  }
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
  </style>
@endsection