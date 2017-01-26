<div class="dx-employee-hired">
    @if($employee->join_date)
    <div class="tile double bg-blue-hoki" style='cursor: default!important;'>
        <div class="tile-body">
          <i class="fa fa-briefcase" style='position: absolute; margin-top: 0px!important;'></i>
          <div style='margin-left: 70px;'>
            <h4>{{ trans('employee.lbl_hired') }}</h4>
            <p>{{ short_date($employee->join_date) }}</p>
            @if ($employee->termination_date)
                <br>
                <p>{{ trans('employee.lbl_term_date') }} {{ short_date($employee->termination_date) }}</p>
            @endif
          </div>
        </div> 
        <div class="tile-object">
            <div class="name">{{ $employee->getJoinTime() }}</div>
        </div>
    </div> 
    @endif
</div>

        