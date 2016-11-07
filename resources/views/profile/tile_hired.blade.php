<div class="dx-employee-hired">
  @if($employee->join_date)
    <div class="tile bg-blue-hoki">
      <div class="tile-body">
        <i class="fa fa-briefcase"></i>
      </div>
      <div class="tile-object">
        <div class="name"> Hired</div>
        <div class="number"> {{ strftime('%x', strtotime($employee->join_date)) }} </div>
      </div>
    </div>
  @endif
</div>