<div class="portlet light dx-employee-profile" dx_is_init="0">
  <div class="portlet-title">
    <div class="caption">
      <i class="fa fa-user"></i>
      <span class="caption-subject bold uppercase">{{ $is_my_profile ? 'My profile' : 'Employee profile' }}</span>
      <span class="caption-helper">{{ $is_my_profile ? "" : "profile" }}</span>
    </div>
    <div class="actions">
      {{--
      @if($is_empl_edit_rights && 1 == 2)
        <a href="javascript:;" class="btn btn-circle btn-default dx-edit-general" dx_employee_id="{{ $empl_row->id }}" dx_list_id="{{ $empl_list_id }}">
          <i class="fa fa-pencil"></i> Edit </a>
      @endif
      --}}
    </div>
  </div>
  <div class="portlet-body">
    <div class="employee-panel">
      <div class="well">
        <div class="row">
          <div class="hidden-xs col-sm-3 col-md-3 employee-pic-box">
            <img src="{{ $external['avatar']['thumb'] }}" class="img-responsive img-thumbnail" style="max-height: 178px;">
          </div>
          <div class="col-xs-12 col-sm-9 col-md-9">
            <div class="employee-details-1">
              <a href="javascript:;" class="btn btn-circle btn-default {{ $external['activity']['class'] }} pull-right" title="{{ $external['activity']['title'] }}"> {{ $external['activity']['button'] }} </a>
              <h4>{{ $empl_row->display_name }}</h4>
              <a href="#" class="dx_position_link" dx_attr="Biroja vadītāja" dx_source_id=""> {{ $empl_row->position_title }}</a><br>
              <a href="#" class="small dx_department_link " dx_dep_id="" dx_attr="" dx_source_id="">{{ $empl_row->department_title }}</a><br><br>
              <div class="text-left">
                <a href="mailto:{{ $empl_row->email }}">{{ $empl_row->email }}</a><br>
                {{ $empl_row->phone }}<br><br>
                <p style="margin-bottom: 0px;">
                  @if($external['flag'] && $external['flag']->flag_file_guid && file_exists(public_path('img/'.$external['flag']->flag_file_guid)))
                    <img src="/img/{{ $external['flag']->flag_file_guid }}" title="{{ $external['flag']->flag_file_name }}" style="margin-top: 1px;"/>
                  @else
                    <img src="/assets/global/flags/en.png" title="English" style="margin-top: 1px;"/>
                  @endif
                  <span title="Employee location" class="pull-right"><i class="fa fa-map-marker"></i> {{ $empl_row->location_city }}, {{ $empl_row->country_title }}</span>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <h3>About</h3>
    <p>{{ $empl_row->description }}</p>
    
    <h3>Stats</h3>
    <div class="tiles" style="margin-bottom: 20px">
      <div class="tile bg-blue-hoki double">
        <div class="tile-body">
          <i class="fa fa-briefcase"></i>
        </div>
        <div class="tile-object">
          <div class="name"> Hired</div>
          <div class="number"> {{ strftime('%x', strtotime($empl_row->join_date)) }} </div>
        </div>
      </div>
      <div class="tile double bg-blue-madison">
        <div class="tile-body">
          @if($external['manager'])
            <img src="/assets/global/tiles/manager.jpg" alt="">
            <h4>{{ $external['manager']->display_name }}</h4>
            <p> {{ $external['manager']->position_title }}<br/> {{ $external['manager']->department_title }}  </p>
          @endif
        </div>
        <div class="tile-object">
          <div class="name"> Direct supervisor</div>
          <div class="number"></div>
        </div>
      </div>
      @if ($empl_row->id == Auth::user()->id)
        <div class="tile image double selected">
          <div class="tile-body">
            <img src="/assets/global/tiles/vacation.jpg" alt=""></div>
          <div class="tile-object">
            <div class="name"> Available vacation days</div>
            <div class="number"> 14</div>
          </div>
        </div>
        <div class="tile bg-red-intense">
          <div class="tile-body">
            <i class="fa fa-calendar"></i>
          </div>
          <div class="tile-object">
            <div class="name"> Sick days</div>
            <div class="number"> 3</div>
          </div>
        </div>
        <div class="tile bg-yellow-saffron">
          <div class="tile-body">
            <i class="fa fa-gift"></i>
          </div>
          <div class="tile-object">
            <div class="name"> Bonuses</div>
            <div class="number"> 2</div>
          </div>
        </div>
      
      @endif
    </div>
    <div class="tabbable-line">
      <ul class="nav nav-tabs">
        @section('profile_tabs')
        @show
      </ul>
    </div>
    <div class="tab-content">
      @section('profile_tabs_content')
      @show
    </div>
  
  </div>
</div>