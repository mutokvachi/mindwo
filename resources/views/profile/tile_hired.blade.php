<div class="dx-employee-hired">
    @if($employee->join_date)
    <div class="tile double bg-blue-hoki dx-employee-widget-tile" style='cursor: default!important;'>
        <div class="tile-body dx-employee-widget-tile-body">
            <i class="fa fa-briefcase " style='position: absolute; margin-top: 0px!important;'></i>
            <div class="dx-employee-widget-tile-label">
                <h4>{{ trans('employee.lbl_hired') }}</h4>
                <p>{{ short_date($employee->join_date) }}</p>

            </div>
        </div> 
        <div class="tile-object">
            <div class="dx-employee-widget-tile-object">
                @if ($employee->termination_date)
                {{ trans('employee.lbl_term_date') }} {{ short_date($employee->termination_date) }}</br>
                @endif
                {{ $employee->getJoinTime() }}
            </div>
        </div>
    </div> 
    @endif
</div>

