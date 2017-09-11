@foreach($avail_empl as $empl)
    @include('calendar.complect.empl_row', ['empl' => $empl, 'dont_tool' => 'dx-dont-tooltipster'])
@endforeach