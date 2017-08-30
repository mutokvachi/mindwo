@foreach($members as $empl)
    @include('calendar.complect.empl_row', ['empl' => $empl])
@endforeach