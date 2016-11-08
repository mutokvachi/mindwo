<div class="dx-employee-manager">
  @if($employee->manager)
    <div class="tile double bg-blue-madison employee-manager-tile" data-empl-id='{{ $employee->manager->id }}'>
      <div class="tile-body">
        <img src="{{ $employee->manager->getAvatar() }}" alt="" style='max-width: 60px;'>
        <h4>{{ $employee->manager->display_name }}</h4>
        <p> {{ $employee->manager->position_title }}<br/> {{ $employee->manager->department_title }}  </p>
      </div>
      <div class="tile-object">
        <div class="name">Direct supervisor</div>
        <div class="number"></div>
      </div>
    </div>
  @endif
</div>