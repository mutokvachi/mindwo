<div class="employee-panel">
  <div class="well">
    <div class="row">
      <div class="col-sm-12 col-md-12 employee-pic-box" style="text-align: center;">
        <img src="{{ $employee->getAvatar() }}" class="img-responsive img-thumbnail" style="max-height: 178px;">
        <h4><span>{{ $employee->first_name }}</span> <span>{{ $employee->last_name }}</span></h4>
        <span><a href="#" class="dx_position_link" data-name="position_title">{{ $employee->position_title }}</a></span><br>
        @if($employee->department)
          <span><a href="#" class="small dx_department_link">{{ $employee->department->title }}</a></span><br><br>
        @endif
        @if($mode != 'create')
          <a href="javascript:;" class="btn btn-default {{ $avail['class'] }}" title="{{ $avail['title'] }}" style="font-size: 10px; "> {{ $avail['button'] }} </a>
        @endif
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="employee-details-1">
          <hr>
          <div class="text-left">
            @if($employee->email)
              <div class="dx-contact-info"><i class="fa fa-envelope-o"></i> <a href="mailto:{{ $employee->email }}">{{ $employee->email }}</a></div>
            @endif
            @if($employee->phone)
              <div class="dx-contact-info"><i class="fa fa-phone"></i> {{ $employee->phone }}</div>
            @endif
            @if($employee->location_city || $employee->country)
              <div class="dx-contact-info"><i class="fa fa-map-marker"></i>
                {{ $employee->location_city ? $employee->location_city : '' }}{{ $employee->location_city && $employee->country ? ',' : '' }}
                {{ $employee->country ? $employee->country->title : '' }}</div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
