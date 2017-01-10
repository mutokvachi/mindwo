<div id="dx-widget-report-panel-{{ $uid }}" class="dx-widget-report-panel"
     data-date_format="{{ config('dx.date_format') }}" 
     data-working_day_h="{{ Config::get('dx.working_day_h', 8) }}"
     data-date_from="{{ (new DateTime('first day of january'))->getTimestamp() * 1000 }}"
     data-date_To="{{ (new DateTime('last day of december'))->getTimestamp() * 1000 }}"
     data-report_name="{{ $report_name }}"
     data-uid="{{ $uid }}">   
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption font-green-seagreen">
                <i class="fa fa-history font-green-seagreen"></i>
                <span class="caption-subject font-lg bold"> {{ trans('reports.' . $report_name . '.title') }} </span>
            </div>
            <div class="actions pull-left dx-widget-report-filter-panel">
                <input type="text" class="dx-widget-report-filter-year-input pull-left" value="" style="visibility:hidden; width: 1px;">
                <div class="btn-group dx-widget-report-filter-year">                    
                    <a class="btn green-seagreen btn-outline btn-circle btn-xs dx-widget-report-filter-year-btn" href="javascript:;" aria-expanded="false">
                        <i class="fa fa-calendar"></i> {{ trans('reports.' . $report_name . '.date_interval') }}: <span class="dx-widget-report-curr-year">
                            {{ date_format(new DateTime('first day of january'), config('dx.txt_date_format')) }} - {{ date_format(new DateTime('last day of december'), config('dx.txt_date_format')) }}
                        </span>
                        <i class="fa fa-angle-down"></i>
                    </a>                    
                </div>
                <div class="btn-group">
                    <a class="btn green-seagreen btn-outline btn-circle" href="javascript:;" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-filter"></i> {{ trans('reports.' . $report_name . '.group') }}: <span class="dx-widget-report-curr-group">{{ trans('reports.' . $report_name . '.all') }}</span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right dx-widget-report-scrollable-menu">
                        <li>
                            <a href="javascript:;" class="dx-widget-report-sel-group" data-value="0" data-title="{{ trans('reports.' . $report_name . '.all') }}">
                                {{ trans('reports.' . $report_name . '.all') }}
                            </a>
                        </li>
                        @foreach (\App\Models\Source::orderBy('title')->get() as $source)
                        <li>
                            <a href="javascript:;" class="dx-widget-report-sel-group" data-value="{{ $source->id }}" data-title="{{ $source->title }}">
                                {{ $source->title }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="portlet-body">            
            <div class="row dx-widget-report-period">
                <div class="col-md-3 col-sm-4 col-xs-12 text-stat">
                    <span class="label label-sm bg-blue"> {{ trans('reports.' . $report_name . '.total') }}: </span>
                    <h3 class="dx-widget-report-period-balance">0</h3>
                </div>
                <div class="col-md-3 col-sm-4 col-xs-12 text-stat">
                    <span class="label label-sm bg-green-jungle"> {{ trans('reports.' . $report_name . '.gain') }}: </span>
                    <h3 class="dx-widget-report-period-accrued">0</h3>
                </div>
                <div class="col-md-3 col-sm-4 col-xs-12 text-stat">
                    <span class="label label-sm bg-red"> {{ trans('reports.' . $report_name . '.loss') }}: </span>
                    <h3 class="dx-widget-report-period-used">0</h3>
                </div>
            </div>
            <div id="dx-widget-report-chart-{{ $uid }}" class='dx-widget-report-chart' style="width: 100%; height: 400px;"></div>
        </div>
    </div>
</div>  