<div class="dx-employee-panel">
  <div class="well">
    <div class="row">
      <div class="col-sm-12 col-md-12 employee-pic-box" style="text-align: center;">
        <div data-name="picture_name" data-display="form-field">
          {!! $form->renderField('picture_name') !!}
        </div>
        
        <h4 class='dx-empl-title'>{{ $employee->first_name }} {{ $employee->last_name }}</h4>
        <span><a href="#" class="dx_position_link">{{ $employee->position_title }}</a></span><br>
        @if($employee->department)
          <span><a href="#" class="small dx_department_link">{{ $employee->department->title }}</a></span><br><br>
        @endif
        @if($mode != 'create')
            @include('profile.status_info', ['avail' => $avail])
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

