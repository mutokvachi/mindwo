<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
    <!-- BEGIN WIDGET THUMB -->
    <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
        <h4 class="widget-thumb-heading">{{ $timeoff->title }}</h4>
        <div class="widget-thumb-wrap">
            <i class="widget-thumb-icon {{ $timeoff->icon or 'fa fa-briefcase' }}" style="background-color:{{ $timeoff->color or '#3598dc' }}"></i>
            <div class="widget-thumb-body">
                <span class="widget-thumb-subtitle">
                    {{ $timeoff->is_accrual_hours ? trans('calendar.hours') : trans('calendar.days') }}
                </span>
                <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ $timeoff->balance ? round(($timeoff->is_accrual_hours ? $timeoff->balance : ($timeoff->balance / 8)), 0) : 0 }}">
                    {{ $timeoff->balance ? round(($timeoff->is_accrual_hours ? $timeoff->balance : ($timeoff->balance / 8)), 0) : 0 }}
                </span>
            </div>
        </div>
    </div>
    <!-- END WIDGET THUMB -->
</div>
