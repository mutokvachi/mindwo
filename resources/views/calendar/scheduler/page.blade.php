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

        .portlet.calendar .fc-button {
            top: -10px!important;
        }

        .dx-group input[type=checkbox] {          
            margin-right: 5px;
        }
          
        .ext-cont {
            height: 150px;
            overflow-y: scroll;
            overflow-x: hidden;
            padding: 6px;
            border: 1px solid #dddddd;
        }
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
<div class="dx-scheduler" 
     data-subjects-list-id="{{ $subjects_list_id }}" 
     data-groups-list-id="{{ $groups_list_id }}"
     data-days-list-id="{{ $days_list_id }}"
     data-rooms-list-id="{{ $rooms_list_id }}"
     data-coffee-list-id="{{ $coffee_list_id }}"
     data-rooms-json='{!! json_encode($rooms, JSON_UNESCAPED_UNICODE) !!}'     
     data-room-id="{{ $current_room_id }}"
     data-crrent-date="{{ date('Y-m-d') }}"
     >
    <div class="portlet light portlet-fit bordered calendar">
	<div class="portlet-title">
            <div class="caption font-grey-cascade uppercase">
                <i class="fa fa-calendar"></i> {{ trans('calendar.scheduler.page_title') }}        
            </div>
            @include('calendar.scheduler.rooms')
	</div>
	<div class="portlet-body">
		<div class="row">
			<div class="col-md-4 col-sm-12">
                                <div class="row" style="margin-bottom: 8px;">
                                    <div class="col-md-5 dx-title">
                                        Mācību pasākumi
                                    </div>
                                    <div class="col-md-7">
                                        
                                        <div class="input-group pull-right">
                                            <input type="text" class="form-control dx-search-subj" placeholder="Meklēt pasākumu...">                                            
                                        </div>
                                        <div class="dx-cafe pull-right"><i class="fa fa-coffee" title="Kafijas pauze"></i></div>
                                    </div>
                                </div>    
                                <div class="ext-cont">
                                    <div id="external-events">
                                        @foreach($subjects as $subj)
                                        <div class='dx-event' data-subject-id="{{ $subj->id }}"><span class="dx-item-title">{{ $subj->title_full }}</span><a class="pull-right" href="javascript:;"><i class="fa fa-edit dx-subj-edit"></i></a></div>
                                        @endforeach 
                                    </div>
                                </div>
                            
                                <div class="row" style="margin-bottom: 15px; margin-top: 15px;">
                                    <div class="col-md-6 dx-title">
                                        Grupas sagatavošanā
                                    </div>
                                    <div class="col-md-6">                                        
                                        <div class="input-group pull-right">
                                            <input type="text" class="form-control dx-search-group" placeholder="Meklēt grupu...">                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="ext-cont">
                                    <div id="dx-groups-box">
                                        @foreach($groups as $group)
                                        <div class='dx-event dx-group' data-subject-id="{{ $group->subject_id }}" data-group-id="{{ $group->id }}"><input type="checkbox"/><span class="dx-item-title">{{ $group->title }}</span><a class="pull-right" href="javascript:;"><i class="fa fa-edit dx-group-edit"></i></a></div>
                                        @endforeach
                                    </div>
                                </div>
			</div>
                       
			<div class="col-md-8 col-sm-12">
				<div id="calendar" class="has-toolbar"> </div>
			</div>
		</div>
	</div>
    </div>
    @include('calendar.scheduler.publish_popup')
    @include('calendar.scheduler.footer')
</div>
@endsection

