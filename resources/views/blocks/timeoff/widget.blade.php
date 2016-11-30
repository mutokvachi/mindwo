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
            @foreach ($self->getTimeoffs() as $timeoff)
                <li>
                    <span class="sale-info" style="text-transform: uppercase;"> <i class="{{ $timeoff->icon }}"></i> {{ $timeoff->title }} </span>
                    <span class="sale-num"> {{ $timeoff->balance }}{{ $timeoff->unit}} </span>
                </li>
            @endforeach            
        </ul>
        <div style="text-align: center;">
            <button type="button" class="btn btn-primary btn-sm">Request time off</button>
        </div>
    </div>
</div>