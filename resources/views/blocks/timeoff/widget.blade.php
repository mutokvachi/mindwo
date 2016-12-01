<div class="portlet sale-summary" dx_block_id="timeoff_widget">
    <div class="portlet-title">
        <div class="caption">My time off available</div>
        {{--
        <div class="tools">
            <a class="collapse" href="javascript:;"> </a>
        </div>
        --}}
    </div>
    <div class="portlet-body">
        <ul class="list-unstyled">
            @foreach ($user->timeoff() as $timeoff)
                <li>
                    <span class="sale-info" style="text-transform: uppercase;"> <i class="{{ $timeoff->icon }}" style="padding: 5px; width: 24px; height: 24px; color: white; background-color:{{ $timeoff->color or '#3598dc' }}"></i> {{ $timeoff->title }} </span>
                    <span class="sale-num"> {{ $timeoff->balance }}{{ $timeoff->unit}} </span>
                </li>
            @endforeach            
        </ul>
        <div style="text-align: center;">
            <button type="button" class="btn btn-primary btn-sm">Request time off</button>
        </div>
    </div>
</div>