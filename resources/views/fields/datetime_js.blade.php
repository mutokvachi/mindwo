@if (!$is_disabled)
    <script type="text/javascript">
        $(function() {
            $( '#{{ $frm_uniq_id }}_{{ $item_field }}' ).datetimepicker({
                lang: '{{ Lang::locale() }}',
                format:'{{ $tm_format }}',
                timepicker:{{ $is_time }},
                dayOfWeekStart: 1,
                closeOnDateSelect: true
            });
        });
        
        $( '#{{ $frm_uniq_id }}_{{ $item_field }}_cal' ).click(function(){            
            jQuery('#{{ $frm_uniq_id }}_{{ $item_field }}').datetimepicker('show');
        });
    </script>
@endif