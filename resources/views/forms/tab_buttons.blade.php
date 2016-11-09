@if($counter = 0)
@endif
@foreach($tabs as $key => $tab)
  <li class="{{ $counter == 0 ? 'active' : '' }}">
    <a data-toggle="tab"
      class="dx-tab-link"
      aria-expanded="{{ $counter == 0 ? 'true' : 'false' }}"
      href="#tabs_{{ $formUid }}_{{ $tab->id}}"
      tab_id="tabs_{{ $formUid }}_{{ $tab->id }}"
      grid_list_id="{{ $tab->grid_list_id}}"
      grid_list_field_id="{{ $tab->grid_list_field_id }}">{{ $tab->title }}</a>
  </li>
  @if($counter++)
  @endif
@endforeach
