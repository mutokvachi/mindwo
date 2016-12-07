<?php
$timeoffs = $user->timeoff();

$filter_timeoff_id = 0;
$filter_timeoff_title = 0;

if ((count($timeoffs) > 0)) {
    $filter_timeoff_id = $timeoffs[0]->id;
    $filter_timeoff_title = $timeoffs[0]->title;
}
?>
<div id="dx-emp-timeoff-panel" 
     data-date_format="{{ config('dx.date_format') }}" 
     data-date_from="{{ (new DateTime('first day of january'))->getTimestamp() * 1000 }}"
     data-date_To="{{ (new DateTime('last day of december'))->getTimestamp() * 1000 }}"
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
            <div class="actions pull-left dx-emp-timeoff-filter-panel">
                <input type="text" id="dx-emp-timeoff-filter-year-input" value="" style="visibility:hidden; width: 1px;" class="pull-left">
                <div class="btn-group dx-emp-timeoff-filter-year">                    
                    <a class="btn green-seagreen btn-outline btn-circle btn-xs dx-emp-timeoff-filter-year-btn" href="javascript:;" aria-expanded="false">
                        <i class="fa fa-calendar"></i> Date interval: <span class="dx-emp-timeoff-curr-year">
                            {{ date_format(new DateTime('first day of january'), config('dx.txt_date_format')) }} - {{ date_format(new DateTime('last day of december'), config('dx.txt_date_format')) }}
                        </span>
                        <i class="fa fa-angle-down"></i>
                    </a>                    
                </div>
                <div class="btn-group">
                    <a class="btn green-seagreen btn-outline btn-circle" href="javascript:;" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-filter"></i> Time off: <span class="dx-emp-timeoff-curr-timeoff">{{ $filter_timeoff_title }}</span>
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
            <div class="tabbable-line">    
                <ul class="nav nav-tabs ">
                    <li class="active">
                        <a class="dx-emp-timeoff-tab-chart-btn" href="#dx-emp-timeoff-tab-chart" data-toggle="tab"> Chart </a>
                    </li>
                    <li>
                        <a class="dx-emp-timeoff-tab-table-btn" href="#dx-emp-timeoff-tab-table" data-toggle="tab"> Table </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="dx-emp-timeoff-tab-chart">
                        <div id="dx-emp-timeoff-chart" style="width: 100%; height: 500px;"></div>
                    </div>
                    <div class="tab-pane" id="dx-emp-timeoff-tab-table">
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
        </div>
    </div>
</div>