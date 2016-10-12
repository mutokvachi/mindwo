<textarea {{ ($is_disabled) ? 'disabled' : '' }} id='{{ $frm_uniq_id }}_{{ $item_field }}' name='{{ $item_field }}' rows='10' style='width: 100%'>{{ $item_value }}</textarea>

@if (!$is_disabled)
    <script type="text/javascript">
        init_soft_code( '{{ $frm_uniq_id }}_{{ $item_field }}' );
    </script>
@endif