@extends('frame')

@section('main_custom_css')  
  @include('pages.view_css_includes')
  <link href="{{ elixir('css/elix_menu_builder.css') }}" rel="stylesheet"/>
  <link href="{{ elixir('css/elix_scheduler.css') }}" rel="stylesheet"/>
  <style>
      .dx-menu-builder-stick-title {
          font-size: 14px;
          padding-top: 32px!important;
          text-transform: uppercase;
      }
      .dx-event {
          margin-bottom: 6px;
          font-size: 14px;
          padding: 6px;
          border-radius: 2px!important;
          background-color: #f5eeef;
          overflow: hidden;
      }
      
      .dx-cafe {
          background-color: #d6df32!important;
            padding: 6px;
            border: 1px solid gray;
     margin-right: 6px;
        color: white;
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
      
      .dx-group {
          background-color: #f5cbd1;
      }
      
      /*
      #calendar {
		max-width: 700px;
		margin: 50px auto;
        }
        */

  </style>
@endsection

@section('main_custom_javascripts')
    @include('pages.view_js_includes')
    <script src="{{ elixir('js/elix_scheduler.js') }}" type='text/javascript'></script>
    <script>
        $(document).ready(function()
        {
            $('.dx-scheduler').DxScheduler();
        });
    </script>
@endsection

@section('main_content')
<div class="dx-scheduler" data-group-list-id="{{ $groups_list_id }}">
    <div class="portlet light portlet-fit bordered calendar">
	<div class="portlet-title">
            <div class="caption font-grey-cascade uppercase">
                <i class="fa fa-calendar"></i> {{ trans('calendar.scheduler.page_title') }}        
             </div>
	</div>
	<div class="portlet-body">
		<div class="row">
			<div class="col-md-5 col-sm-12">
				   <div class="row" style="margin-bottom: 15px;">
                                        <div class="col-md-6 dx-title">
                                            Mācību pasākumi
                                        </div>
                                        <div class="col-md-6">
                                            <div class="dx-cafe pull-left"><i class="fa fa-coffee" title="Kafijas pauze"></i></div>
                                            <div class="input-group">
                                                <input type="text" class="form-control dx-search-subj" placeholder="Meklēt pasākumu...">
                                                <span class="input-group-btn">
                                                  <button class="btn btn-default dx-new-btn" title="Jauns mācību pasākums" type="button">Jauns</button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>    
                                <div id="ext-cont">
                                    <div id="external-events">
                                        @foreach($subjects as $subj)
                                        <div class='dx-event'>{{ $subj->title }}</div>
                                        @endforeach 
                                    </div>
                                </div>
                            
                                <div class="row" style="margin-bottom: 15px;">
                                        <div class="col-md-6 dx-title">
                                            Mācību grupas sagatavošanā
                                        </div>
                                </div>
				<div id="dx-groups-box">
                                </div>
			</div>
                       
			<div class="col-md-7 col-sm-12">
				<div id="calendar" class="has-toolbar"> </div>
			</div>
		</div>
	</div>
    </div>
    @include('calendar.scheduler.footer')
</div>
@endsection

