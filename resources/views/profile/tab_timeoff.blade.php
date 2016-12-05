<?php
$timeoffs = $user->timeoff();

$filter_timeoff_id = 0;
$filter_timeoff_title = 0;

if ((count($timeoffs) > 0)) {
    $filter_timeoff_id = $timeoffs[0]->id;
    $filter_timeoff_title = $timeoffs[0]->title;
}

$filter_all_years = $user->timeoffYears()->get();
?>
<div id="dx-emp-timeoff-panel" 
     data-year="{{ (count($filter_all_years) > 0) ? $filter_all_years[0]->timeoffYear : date('Y') }}"
     data-timeoff="{{ $filter_timeoff_id }}"
     data-timeoff_title="{{ $filter_timeoff_title }}">   
    <div class="dx-emp-timeoff-tiles row">
        @foreach ($timeoffs as $timeoff)
        @include('profile.timeoff.control_timeoff_tile')
        @endforeach
    </div>
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption font-green-seagreen">
                <i class="fa fa-history font-green-seagreen"></i>
                <span class="caption-subject font-lg bold"> History </span>
            </div>
            <div class="actions">
                <div class="btn-group dx-emp-timeoff-filter-year">
                    <a class="btn green-seagreen btn-outline btn-circle" href="javascript:;" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-calendar"></i> Year - <span class="dx-emp-timeoff-curr-year">{{ (count($filter_all_years) > 0) ? $filter_all_years[0]->timeoffYear : date('Y') }}</span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right dx-emp-timeoff-filter-year-list">
                        @include('profile.timeoff.control_timeoff_filter_year')
                    </ul>
                </div>
                <div class="btn-group">
                    <a class="btn green-seagreen btn-outline btn-circle" href="javascript:;" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-filter"></i> Time off - <span class="dx-emp-timeoff-curr-timeoff">{{ $filter_timeoff_title }}</span>
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