<div class="dd-item dx-constructor-tab-button
  @if($tab)
    {{ 'tab-id-'.$tab->id }}
    {{ $tab->is_custom_data ? 'custom-data' : 'related-grid' }}
  @endif
  "
  data-id="{{ $tab ? $tab->id : ''}}"
  data-order="{{ $tab ? $tab->order_index : '' }}"
  data-hidden="">
  <div class="dd-handle dd3-handle"></div>
  <div class="dd3-content">
    <a class="title">{{ $tab ? $tab->title : '' }}</a>
  </div>
</div>