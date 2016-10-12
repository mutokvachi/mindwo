@if ($is_disabled)
<div><div name = '{{ $item_field }}' dx_fld_name = '{{ $item_field }}' style="margin-top: 8px">{!! ($item_value) ? format_event_time($item_value) : '' !!}</div></div>
@else
    <div class='input-group'>
        <span class='input-group-btn'>
            <button {{ ($is_disabled) ? 'disabled' : '' }} type='button' class='btn btn-white' id='{{ $frm_uniq_id }}_{{ $item_field }}_cal'><i class='fa fa-calendar'></i></button>
        </span>
        <input {{ ($is_disabled) ? 'disabled' : '' }} class='form-control' type=text id='{{ $frm_uniq_id }}_{{ $item_field }}' name = '{{ $item_field }}' value = '{{ $item_value }}' style='width: {{ $fld_width }}px;' {{ ($is_required) ? 'required' : '' }} />
    </div>
    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
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