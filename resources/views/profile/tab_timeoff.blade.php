<?php
$timeoffs = $user->timeoff();

$filter_timeoff_id = 0;
$filter_timeoff_title = 0;
foreach ($timeoffs as $timeoff) {
    $filter_timeoff_id = $timeoff->id;
    $filter_timeoff_title = $timeoff->title;
    break;
}

$filter_all_years = $user->timeoffYears()->get();
$filter_year = $user->timeoffYears()->first()->timeoffYear;
?>
<div id="dx-emp-timeoff-panel" 
     data-year="{{ $filter_year }}"
     data-timeoff="{{ $filter_timeoff_id }}"
     data-timeoff_title="{{ $filter_timeoff_title }}">   
    <div class="dx-emp-timeoff-tiles row">
        @foreach ($timeoffs as $timeoff)
        @include('profile.control_timeoff_tile', ['timeoff' => $timeoff, 'has_access' => $has_access])
        @endforeach
    </div>

    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption font-green-sharp">
                <i class="icon-speech font-green-sharp"></i>
                <span class="caption-subject"> History </span>
            </div>
            <div class="actions">
                <div class="btn-group">
                    <a class="btn btn-circle btn-default " href="javascript:;" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-calendar"></i> Year - <span class="dx-emp-timeoff-curr-year">{{ $filter_year }}</span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        @foreach ($filter_all_years as $year)
                        <li>
                            <a href="javascript:;" class="dx-emp-timeoff-sel-year" data-value="{{ $year->timeoffYear }}">
                                {{ $year->timeoffYear }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="btn-group">
                    <a class="btn btn-circle btn-default " href="javascript:;" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-briefcase"></i> Time off - <span class="dx-emp-timeoff-curr-timeoff">{{ $filter_timeoff_title }}</span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        @foreach ($timeoffs as $timeoff)
                        <li>
                            <a href="javascript:;" class="dx-emp-timeoff-sel-timeoff" data-value="{{ $timeoff->id }}" data-title="{{ $timeoff->title }}">
                                <i class="{{ $timeoff->icon or 'fa fa-briefcase' }}"></i> {{ $timeoff->title }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <table id="dx-empt-datatable-timeoff" class="table table-condensed table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>From date</th>
                        <th>To date</th>
                        <th>Type</th>
                        <th class="sorting_disabled">Notes</th>
                        <th>Used / Accrued (hours)</th>
                        <th>Balance (hours)</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>