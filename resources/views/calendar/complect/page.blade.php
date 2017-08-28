@extends('frame')

@section('main_custom_css')  
  @include('pages.view_css_includes')
  <link href="{{ elixir('css/elix_complect.css') }}" rel="stylesheet"/>
  <style>
        .dx-menu-builder-stick-title {
            font-size: 14px;
            padding-top: 32px!important;
            text-transform: uppercase;
        }
        
        .dx-event {            
            color: black;

            margin: 0 0 20px 0;
            padding: 10px 10px 10px 10px;
            border-left: 5px solid #eee;
        }       

        .calendar {
            margin-bottom: 100px;
        }
        
        .dx-title {
              margin-top: 3px;
              font-size: 17px;
              font-family: "Open Sans",sans-serif;
              font-weight: 300;
              text-transform: uppercase;
        }
        
        .dx-item-title {
            font-weight: bold;
        }

        .dx-signup-due {
            font-weight: 300;
        }
               

        .dx-status-free {
            background-color: #c0edf1;
            border-color: #58d0da;
        }
        
        .dx-status-full {
            background-color: #faeaa9;
            border-color: #f3cc31;
        }
        

        .portlet.calendar .fc-button {
            top: -10px!important;
        }
          
        .ext-cont {
            height: 350px;
            overflow-y: scroll;
            overflow-x: hidden;
            padding: 6px;
            border: 1px solid #dddddd;
        }

        .dx-limit-info {
            font-size: 10px;
            padding-top: 10px;
        }

        .dx-group-edit {
            font-size: 12px;
        }

        .dx-signup-due {
            padding-left: 10px;
        }

        .dx-group-info-cont {
            height: 135px;
        }

        .dx-empl-info {
            position: relative;
            border: 1px solid #adadad;
            border-radius: 2px!important;
            padding: 10px;
            background-color: #f1f1f1;
            margin: 5px;
        }

        #dx-avail-box .dx-is-avail .dx-empl-add{
            display: block;
        }

        #dx-avail-box .dx-is-member .dx-empl-add{
            display: none;
        }

        #dx-members-box .dx-empl-remove{
            display: block;
        }

        #dx-avail-box .dx-empl-remove{
            display: none;
        }

        #dx-members-box .dx-empl-add{
            display: none;
        }

        .dx-empl-name {
            font-weight: bold;            
        }

        .dx-empl-code {
            font-size: 10px;
            margin-left: 5px;
        }

        .dx-empl-main {
            padding-right: 30px;
        }
        
        .dx-empl-cmd {
            position: absolute;
            top: 6px;
            right: 12px;
            font-size: 20px;
        }

        .dx-empl-count {
            font-size: 11px;
        }

        .dx-count-section {
            text-align: right;
        }
  </style>
@endsection

@section('main_custom_javascripts')
    @include('pages.view_js_includes')
    <script src="{{ elixir('js/elix_complect.js') }}" type='text/javascript'></script>
    <script>
        $(document).ready(function()
        {
            $('.dx-complect').DxComplect();
        });
    </script>
@endsection

@section('main_content')
<div class="dx-complect"      
     data-current-date="{{ ($current_date) ? $current_date : date('Y-m-d') }}"
     data-org-id="{{ $current_org_id }}"
     >
    <div class="portlet light portlet-fit bordered calendar">
	<div class="portlet-title">
            <div class="caption font-grey-cascade uppercase">
                <i class="fa fa-users"></i> {{ trans('calendar.complect.page_title') }}        
            </div>
            @include('calendar.complect.orgs')
	</div>
	<div class="portlet-body">
		<div class="row">
			<div class="col-md-4 col-sm-12">
                                <div class="row" style="margin-bottom: 8px; margin-top: 15px;">
                                    <div class="col-md-3 dx-title">
                                        Grupas
                                    </div>
                                    <div class="col-md-9">                                        
                                        <div class="input-group pull-right">
                                            <div class="input-group-btn dx-group-filter-btn" data-status='all'>
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="btn-title">Visas</span> <span class="caret"></span></button>
                                                <ul class="dropdown-menu">
                                                  <li><a href="javascript:;" data-status='free'>Ir vietas</a></li>
                                                  <li><a href="javascript:;" data-status='full'>Vietu nav</a></li>
                                                  <li role="separator" class="divider"></li>
                                                  <li><a href="javascript:;" data-status='all'>Visas</a></li>
                                                </ul>
                                            </div><!-- /btn-group -->
                                            <input type="text" class="form-control dx-search-group" placeholder="Meklēt grupu...">                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="ext-cont">
                                    <div id="dx-groups-box">
                                        @include('calendar.complect.group_box')                                       
                                    </div>
                                </div>
			</div>
                       
			<div class="col-md-8 col-sm-12">
				<div id="calendar" class="has-toolbar"> </div>
			</div>
		</div>
	</div>
    </div>
    @include('calendar.complect.group_popup')
</div>
@endsection

