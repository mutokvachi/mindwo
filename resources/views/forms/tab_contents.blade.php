@if($counter = 0)
@endif
  @foreach($tabs as $key => $tab)
    <div class="tab-pane fade {{ $counter == 0 ? 'active in' : '' }}" id='tabs_{{ $formUid }}_{{ $tab->id }}' data-tab-title="{{ $tab->title }}">
      @if ($tab->is_custom_data)
        <div
          dx_tab_id="{{ $tab->id }}"
          id="tab_frm_{{ $formUid }}_{{ $tab->id }}"
          class="form-horizontal {{ ($tab->is_custom_data) ? 'dx-custom-tab-data' : '' }}"
          style='z-index: 50;'>
          {!! $tab->data_htm !!}
        </div>
      @endif
    </div>
    @if($counter++)
    @endif
  @endforeach