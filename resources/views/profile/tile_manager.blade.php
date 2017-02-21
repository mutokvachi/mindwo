<div class="dx-employee-manager">
  @if($employee->manager)
  <a href='{{ url(Config::get('dx.employee_profile_page_url'))}}/{{ $employee->manager->id }}'>
    <div class="tile double bg-blue-madison employee-manager-tile dx-employee-widget-tile" data-empl-id='{{ $employee->manager->id }}'>
      <div class="tile-body tile-body dx-employee-widget-tile-body">
        <img src="{{ $employee->manager->getAvatar() }}" alt="">
        <h4>{{ $employee->manager->display_name }}</h4>
        <p> {{ $employee->manager->position_title }}<br/> {{ $employee->manager->department_title }}  </p>
      </div>
      <div class="tile-object">
        <div class="dx-employee-widget-tile-object">Direct supervisor</div>
        <div class="number"></div>
      </div>
    </div>
  </a>
  @endif
</div>