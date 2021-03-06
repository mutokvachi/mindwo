<div class="dx-constructor-grid">
  @if(($grid = $grids[$tabId]))
  @endif
  @for($i = 0; $i < count($grid); $i++)
    @if(isset($grid[$i][0]) && ($first = $grid[$i][0]) && strlen($first->group_label))
      @include('constructor.fields_row_label', [
        'id' => $i,
        'field' => $first
      ])
    @endif
    @push("row_content_{$tabId}_$i")
      @for($j = 0; $j < count($grid[$i]); $j++)
        @if($field = $grid[$i][$j])
        @endif
        @include('constructor.fields_item', [
          'field' => $field,
          'class' => $field->is_hidden ? 'dx-field-hidden' : '',
          'column' => 12 / count($grid[$i]),
          'hidden' => $field->is_hidden ? 1 : 0
        ])
      @endfor
    @endpush
    @include('constructor.fields_row_columns', [ 'id' => "{$tabId}_$i" ])
  @endfor
  @if(count($grid) < 4)
    @for($i = 0; $i < 4 - count($grid); $i++)
      @include('constructor.fields_row_columns')
    @endfor
  @endif
</div>
