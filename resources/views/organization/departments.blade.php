@extends('frame')

@section('main_content')
  <div class="portlet light">
    <div class="portlet-title">
      <div class="caption font-grey-cascade uppercase">
        <i class="fa fa-sitemap"></i> {{ trans('organization.chart_title') }}
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
				//,
		  // displayLevels: {{ $displayLevels }}
	  };
	  // init orgchart plugin
	  var orgchart = new OrgChart({
		  chartContainer: '#dx-orgchart-container',
		  data: orgchartData.source,
		  nodeContent: 'title',
		  depth: orgchartData.displayLevels,
		  toggleSiblingsResp: true,
		  pan: true,
		  // customize node creation process
		  createNode: function(node, data)
		  {
			  var content = $(node).children('.content');
			  content.prepend('<div class="main-icon"><i class="fa fa-sitemap"></i></div>');
			  
			  if(data.id != 0)
				{
					content.append('<div class="pull-left"><i class="fa fa-users"></i> ' +
						'<a href="/search?searchType={{ trans('search_top.employees') }}' +
						'&source_id=' + data.source_id + '&department=' + data.name + '">' +
						data.count + '</a></div>');
				}
			  
			  if(data.subordinates > 0)
				  content.append('<div class="subordinates" title="' + Lang.get('organization.hint_subord') + '">' + data.subordinates + '</div>');
		  }
	  });
	  $("#dx-org-zoom-in").click(function()
	  {
		  orgchart.set_zoom(-1);
	  });
	  $("#dx-org-zoom-out").click(function()
	  {
		  orgchart.set_zoom(1);
	  });
	  $("#dx-org-export").click(function()
	  {
		  orgchart._clickExportButton();
	  });
  </script>
@endsection

@section('main_custom_css')
  <link href="{{ elixir('css/elix_orgdepartments.css') }}" rel="stylesheet"/>
	<style>
		.orgchart .node .content {
			height: 70px;
		}
		
		.orgchart .node .content .main-icon {
			font-size: 28px;
			margin: 10px 0;
		}
		
	</style>
@endsection