<div id="dx-emp-timeoff-panel" 
     data-year="2016"
     data-timeoff="">   
    <div class="dx-emp-timeoff-tiles row">
        @foreach ($user->timeoff() as $timeoff)
        @include('profile.control_timeoff_tile', ['timeoff' => $timeoff, 'has_hr_access' => $has_hr_access])
        @endforeach
    </div>

    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption font-green-sharp">
                <i class="icon-speech font-green-sharp"></i>
                <span class="caption-subject"> History </span>
                
                
        </div>
        <div class="portlet-body">
            <table id="dx-empt-datatable-timeoff" class="table table-condensed table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th class="sorting_disabled">Notes</th>
                        <th>Used / Accrued (hours)</th>
                        <th>Balance (hours)</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>