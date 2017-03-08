<td align='{{ $align }}'>
    @if (isset($is_val_html) && $is_val_html)
        {!! $cell_value !!}
    @else
        {{ $cell_value }}
    @endif
</td>

