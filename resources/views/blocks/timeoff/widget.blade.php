<div class="portlet sale-summary dx-timeoff-balance" dx_block_id="timeoff_widget">
    <div class="portlet-title">
        <div class="caption">{{ trans('widgets.timeoff.title') }}</div>
    </div>
    <div class="portlet-body">
        <ul class="list-unstyled">
            @foreach ($user->timeoff() as $timeoff)
                <li>
                    <span class="sale-info" style="text-transform: uppercase;"> <i class="{{ $timeoff->icon }}" style="padding: 5px; width: 24px; height: 24px; color: white; background-color:{{ $timeoff->color or '#3598dc' }}"></i> {{ $timeoff->title }} </span>
                    <span class="sale-num"> {{ $timeoff->balance }} {{ $timeoff->unit}} </span>
                </li>
            @endforeach            
        </ul>
        <div style="text-align: center;">
            <button type="button" class="btn btn-primary btn-sm dx-btn-leave-request" data-leaves-list-id = "{{ $self->leaves_list_id }}" data-user-field-id = "{{ $self->user_field_id }}" data-user-id = "{{ Auth::user()->id }}">{{ trans('widgets.timeoff.btn_request') }}</button>
        </div>
        <div style="text-align: center; margin-top: 15px;">
            <a href="{{ $self->leaves_view_url }}">{{ trans('widgets.timeoff.link_history') }}</a>
        </div>
    </div>
</div>