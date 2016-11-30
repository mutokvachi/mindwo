<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
    <!-- BEGIN WIDGET THUMB -->
    <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
        <h4 class="widget-thumb-heading">{{ $timeoff->title }}</h4>
        @if ($has_hr_access)
            <div class="actions" style="position: absolute; top: 10px; right: 25px;">
                <div class="btn-group">
                    <a class="btn green-haze btn-outline btn-circle btn-sm" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false"> Actions
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="javascript:;"> Accrual Policy </a>
                        </li>
                        <li class="divider"> </li>
                        <li>
                            <a href="javascript:;" class='dx-accrual-calc' data-timeoff='{{ $timeoff->id }}'> Calculate </a>
                        </li>
                    </ul>
                </div>
            </div>
        @endif
        <div class="widget-thumb-wrap">
            <i class="widget-thumb-icon {{ $timeoff->icon or 'fa fa-briefcase' }}" style="background-color:{{ $timeoff->color or '#3598dc' }}"></i>
            <div class="widget-thumb-body">
                <span class="widget-thumb-subtitle">
                    {{ $timeoff->is_accrual_hours ? trans('calendar.hours') : trans('calendar.days') }}
                </span>
                <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $timeoff->balance }}">
                    {{ $timeoff->balance }}
                </span>
            </div>
        </div>
    </div>
    <!-- END WIDGET THUMB -->
</div>
