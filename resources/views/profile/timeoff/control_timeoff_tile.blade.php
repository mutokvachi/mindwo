<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
    <!-- BEGIN WIDGET THUMB -->
    <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">  
        <h4 class="widget-thumb-heading font-grey-mint">{{ $timeoff->title }}</h4>
        <div class="widget-thumb-wrap">
            <i class="widget-thumb-icon {{ $timeoff->icon or 'fa fa-briefcase' }}" style="background-color:{{ $timeoff->color or '#3598dc' }}"></i>
            <div class="widget-thumb-body">
                <span class="widget-thumb-subtitle">
                    {{ $timeoff->unit }} <span style="font-size: 10px">{{ trans('empl_profile.timeoff.available') }}</span>
                </span>
                <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ floor($timeoff->balance) }}">
                    {{ floor($timeoff->balance) }}
                </span>
            </div>
        </div>
        @if ($has_hr_access)
        <div class="actions dx-emp-timeoff-tile-settings">
            <div class="btn-group pull-right">
                <a class="btn green-seagreen btn-outline btn-circle btn-xs" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false"> {{ trans('empl_profile.timeoff.menu_actions') }}
                    <i class="fa fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu pull-right">
                    <li>
                        <a href="javascript:;" class='dx-accrual-policy'
                           data-policy-id = "{{ $timeoff->user_policy_id }}"
                           data-policy-list-id = "{{ $timeoff->user_policy_list_id }}"
                           data-policy-user-field-id = "{{ $timeoff->user_policy_field_id }}"
                           > {{ trans('empl_profile.timeoff.accrual_policy') }} </a>
                    </li>
                    <li>
                        <a href="javascript:;" class='dx-accrual-calc' data-timeoff='{{ $timeoff->id }}'> {{ trans('empl_profile.timeoff.calculate') }} </a>
                    </li>
                    <li>
                        <a href="javascript:;" class='dx-accrual-delete' data-timeoff='{{ $timeoff->id }}'> {{ trans('empl_profile.timeoff.delete_accrual') }} </a>
                    </li>
                </ul>
            </div>
        </div>
        @endif
    </div>
    <!-- END WIDGET THUMB -->
</div>