<div class='panel blank-panel' id = '{{ $tab_id }}' dx_attr='tab'>
    <div class='panel-heading' style='background: transparent;'>
        <div class='panel-options'>
            <ul class='nav nav-tabs'>
                @foreach($tabs_items as $key => $item)
                <li 
                    @if ($key == 0)
                        class='active'
                    @endif
                >
                <a data-toggle='tab' class="dx-tab-link"
                    @if ($key == 0)
                        aria-expanded='true' 
                    @else
                        aria-expanded='false' 
                    @endif
                    href='#tabs_{{ $frm_uniq_id }}_{{ $item->id}}' tab_id='tabs_{{ $frm_uniq_id }}_{{ $item->id }}' grid_list_id='{{ $item->grid_list_id}}' grid_list_field_id='{{ $item->grid_list_field_id }}'>{{ $item->title }}</a></li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class='panel-body'>
        <div class='tab-content'>
            @foreach ($tabs_items as $key => $item)
                <div class='tab-pane 
                    @if ($key == 0)
                       active
                    @endif
                    ' id='tabs_{{ $frm_uniq_id }}_{{ $item->id }}'>
                    @if ($item->is_custom_data)
                        <div dx_tab_id="{{ $item->id }}" id="tab_frm_{{ $frm_uniq_id }}_{{ $item->id }}" class="form-horizontal {{ ($item->is_custom_data) ? 'dx-custom-tab-data' : ''}}" style='z-index: 50;'>
                            {!! $item->data_htm !!}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>            
    </div>        
</div>
<script type='text/javascript'>
    @if (!head($tabs_items)->is_custom_data)
        load_tab_grid('tabs_{{ $frm_uniq_id }}_{{ head($tabs_items)->id }}', {{ head($tabs_items)->grid_list_id }}, 0, {{ head($tabs_items)->grid_list_field_id }}, {{ $item_id }},'list_item_view_form_{{ $frm_uniq_id }}', 1, 5, 0);
    @endif
    
    $('#{{ $tab_id }} a.dx-tab-link').click(function () {
      if ($('#' + this.getAttribute('tab_id')).html().trim().length == 0)
      {
            load_tab_grid(this.getAttribute('tab_id') ,this.getAttribute('grid_list_id'), 0, this.getAttribute('grid_list_field_id'), {{ $item_id }},'list_item_view_form_{{ $frm_uniq_id }}', 1, 5, 1);
      }
    });
</script>