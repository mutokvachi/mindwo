<div id="dx-emp-timeoff-panel" 
     data-year="{{ $user->timeoffYears()->first()->timeoffYear }}"
     data-timeoff="{{ $user->timeoff()->first()->title }}">   
    <div class="dx-emp-timeoff-tiles row">
        @foreach ($user->timeoff()->get() as $timeoff)
        @include('profile.control_timeoff_tile', ['timeoff' => $timeoff])
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
                        <th>Uses / Accrued</th>
                        <th>Balance</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>