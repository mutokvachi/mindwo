<div class="dx-employee-leave">
    @if (count($employee->getLeaveInfo()) > 0)
    <div class="tile double bg-red-pink dx-employee-widget-tile" style='cursor: default!important;'>
        <div class="tile-body dx-employee-widget-tile-body">
            <i class="fa fa-calendar-times-o dx-employee-widget-tile-icon" style='position: absolute; margin-top: 0px!important;'></i>
            <div class="dx-employee-widget-tile-label" style=''>
                <h4>{{ trans('employee.lbl_absent') }}</h4>
                <p>{{ $employee->getLeaveInfo()['reason_title'] }}
                    @if ($employee->getLeaveInfo()['reason_details'])
                    &nbsp;<i class='fa fa-question-circle' title='{{ $employee->getLeaveInfo()['reason_details'] }}'></i>
                    @endif
                </p>
                <br>
                <p>{{ trans('employee.lbl_absent') }} {{ trans('employee.lbl_to') }} {{ $employee->getLeaveInfo()['left_to'] }}</p>                        
            </div>
        </div>
        @if ($employee->getLeaveInfo()['substitute_id'])
        <div class="tile-object">
            <div class="name">Substitute: <a href='{{ url(Config::get('dx.employee_profile_page_url'))}}/{{ $employee->getLeaveInfo()['substitute_id'] }}' style='color: #fff;' title='Open profile'>{{ $employee->getLeaveInfo()['substitute_name'] }} <i class='fa fa-external-link'></i></a></div>
        </div>                   
        @endif          
    </div> 
    @endif
</div>