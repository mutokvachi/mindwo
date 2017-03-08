@if (!Request::ajax())
<div class="row dx-tasks-total-widget">
@endif

    @foreach($arr_info as $arr_data)
    <div class="col-lg-{{ $panels_class }} col-md-{{ $panels_class }} col-sm-6 col-xs-12">
            <div class="dashboard-stat2 ">
                    <div class="display">
                            <div class="number">
                                    <h3 class="font-{{ $arr_data['color_class'] }}">
                                            <span data-counter="counterup" data-value="{{ $arr_data['total_all'] }}">{{ $arr_data['total_all'] }}</span>
                                    </h3>					
                            </div>
                            <div class="icon">
                                    <i class="{{ $arr_data['icon_class'] }}"></i>
                            </div>
                            <div class="number" style="width: 100%;">
                                <small><a href="{{ $arr_data['url_all'] }}">{{ $arr_data['title'] }}</a></small>
                            </div>
                    </div>
                    <div class="progress-info">
                            <div class="progress">
                                    <span style="width: {{ $arr_data["percent_today"] }}%;" class="progress-bar progress-bar-success {{ $arr_data['color_class'] }}">
                                            <span class="sr-only">{{ $arr_data["percent_today"] }}% {{ trans('task_widget.compleated') }}</span>
                                    </span>
                            </div>
                            <div class="status">
                                <div class="status-title"> 
                                    @if ($arr_data['due_today_undone'] > 0)
                                        <a href="{{ $arr_data['url_today'] }}">{{ trans('task_widget.due_today') }}</a> <span class="badge badge-warning"> {{ $arr_data['due_today_undone'] }} </span>
                                    @else
                                        {{ trans('task_widget.due_today') }}
                                    @endif
                                </div>
                                <div class="status-number"> 
                                    @if ($arr_data["percent_today"] >0)
                                    <span title="{{ trans('task_widget.compleated_today') }}">{{ $arr_data["percent_today"] }}%</span>
                                    @endif
                                </div>
                            </div>
                    </div>
                    <div class="progress-info" style="margin-top: 40px;">
                            <div class="progress">
                                    <span style="width: {{ $arr_data["percent_fail"] }}%;" class="progress-bar progress-bar-success green-sharp">
                                            <span class="sr-only">{{ $arr_data["percent_fail"] }}% {{ trans('task_widget.compleated') }}</span>
                                    </span>
                            </div>
                            <div class="status">
                                <div class="status-title">
                                    @if ($arr_data["failed_todo"] > 0)
                                        <a href="{{ $arr_data['url_fail'] }}">{{ trans('task_widget.overdue') }}</a> <span class="badge badge-danger"> {{ $arr_data["failed_todo"] }} </span>
                                    @else
                                        {{ trans('task_widget.overdue') }}
                                    @endif                                      
                                </div>
                                    <div class="status-number"> 
                                        @if ($arr_data["percent_fail"] > 0)
                                        <span title="{{ trans('task_widget.compleated_today') }} {{ $arr_data['failed_solved'] }} {{ trans('task_widget.from') }} {{ $arr_data['total_failed'] }}">{{ $arr_data["percent_fail"] }}%</span>
                                        @endif
                                    </div>
                            </div>
                    </div>
            </div>
    </div>
    @endforeach
    
@if (!Request::ajax())
</div>
@endif