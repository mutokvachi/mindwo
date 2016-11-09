@if (Config::get('dx.is_horizontal_menu'))
    @include('frame_horizontal')
@else
    @include('frame_metronic')    
@endif