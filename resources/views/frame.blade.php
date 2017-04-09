@if (config('dx.is_horizontal_menu'))
  @if(config('dx.is_cssonly_ui', false))
    @include('frame_horizontal_cssonly')
  @else
    @include('frame_horizontal')
  @endif
@else
    @include('frame_metronic')    
@endif