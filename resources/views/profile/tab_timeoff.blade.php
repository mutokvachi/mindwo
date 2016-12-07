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
     data-working_day_h="{{ Config::get('dx.working_day_h', 8) }}"
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
                <span class="caption-subject font-lg bold"> {{ trans('empl_profile.timeoff.history') }} </span>
            </div>
            <div class="actions pull-left dx-emp-timeoff-filter-panel">
                <input type="text" id="dx-emp-timeoff-filter-year-input" value="" style="visibility:hidden; width: 1px;" class="pull-left">
                <div class="btn-group dx-emp-timeoff-filter-year">                    
                    <a class="btn green-seagreen btn-outline btn-circle btn-xs dx-emp-timeoff-filter-year-btn" href="javascript:;" aria-expanded="false">
                        <i class="fa fa-calendar"></i> {{ trans('empl_profile.timeoff.date_interval') }}: <span class="dx-emp-timeoff-curr-year">
                            {{ date_format(new DateTime('first day of january'), config('dx.txt_date_format')) }} - {{ date_format(new DateTime('last day of december'), config('dx.txt_date_format')) }}
                        </span>
                        <i class="fa fa-angle-down"></i>
                    </a>                    
                </div>
                <div class="btn-group">
                    <a class="btn green-seagreen btn-outline btn-circle" href="javascript:;" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-filter"></i> {{ trans('empl_profile.timeoff.timeoff') }}: <span class="dx-emp-timeoff-curr-timeoff">{{ $filter_timeoff_title }}</span>
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
            <div class="tabbable-line tabs-below">   
                <div class="tab-content dx-emp-timeoff-tab-content">
                    <div class="tab-pane active" id="dx-emp-timeoff-tab-chart">       
                        <div class="row dx-emp-timeoff-period">
                            <div class="col-md-3 col-sm-4 col-xs-12 text-stat">
                                <span class="label label-sm bg-blue"> {{ trans('empl_profile.timeoff.balance') }}: </span>
                                <h3 id="dx-emp-timeoff-period-balance">0</h3>
                            </div>
                            <div class="col-md-3 col-sm-4 col-xs-12 text-stat">
                                <span class="label label-sm bg-green-jungle"> {{ trans('empl_profile.timeoff.total_accrued') }}: </span>
                                <h3 id="dx-emp-timeoff-period-accrued">0</h3>
                            </div>
                            <div class="col-md-3 col-sm-4 col-xs-12 text-stat">
                                <span class="label label-sm bg-red"> {{ trans('empl_profile.timeoff.total_used') }}: </span>
                                <h3 id="dx-emp-timeoff-period-used">0</h3>
                            </div>
                        </div>
                        <div id="dx-emp-timeoff-chart" style="width: 100%; height: 400px;"></div>
                    </div>
                    <div class="tab-pane" id="dx-emp-timeoff-tab-table">
                        <table id="dx-empt-datatable-timeoff" class="table table-condensed table-hover">
                            <thead>
                                <tr>
                                    <th>{{ trans('empl_profile.timeoff.date') }}</th>
                                    <th>{{ trans('empl_profile.timeoff.from_date') }}</th>
                                    <th>{{ trans('empl_profile.timeoff.to_date') }}</th>
                                    <th>{{ trans('empl_profile.timeoff.type') }}</th>
                                    <th class="sorting_disabled">{{ trans('empl_profile.timeoff.notes') }}</th>
                                    <th>{{ trans('empl_profile.timeoff.used_accrued') }}</th>
                                    <th>{{ trans('empl_profile.timeoff.balance_hours') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <ul class="nav nav-tabs ">
                    <li class="active">
                        <a class="dx-emp-timeoff-tab-chart-btn" href="#dx-emp-timeoff-tab-chart" data-toggle="tab"> {{ trans('empl_profile.timeoff.chart') }} </a>
                    </li>
                    <li>
                        <a class="dx-emp-timeoff-tab-table-btn" href="#dx-emp-timeoff-tab-table" data-toggle="tab"> {{ trans('empl_profile.timeoff.table') }} </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>