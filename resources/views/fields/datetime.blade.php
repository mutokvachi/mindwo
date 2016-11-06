@if ($is_disabled)
<div><div name = '{{ $item_field }}' dx_fld_name = '{{ $item_field }}' style="margin-top: 8px">{!! ($item_value) ? format_event_time($item_value) : '' !!}</div></div>
@else
    <div class='input-group dx-datetime' data-format="{{ $tm_format }}" data-locale = "{{ Lang::locale() }}" data-is-time = "{{ $is_time }}">
        <span class='input-group-btn'>
            <button {{ ($is_disabled) ? 'disabled' : '' }} type='button' class='btn btn-white dx-datetime-cal-btn'><i class='fa fa-calendar'></i></button>
        </span>
        <input {{ ($is_disabled) ? 'disabled' : '' }} class='form-control dx-datetime-field' type=text name = '{{ $item_field }}' value = '{{ $item_value }}' style='width: {{ $fld_width }}px;' {{ ($is_required) ? 'required' : '' }} />
    </div>
    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
@endif