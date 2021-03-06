@if ($is_disabled)
    <input readonly class='form-control' type=text dx_fld_name = '{{ $item_field }}'   value = '{{ ($item_value) ? ($is_time != 'false') ? long_date($item_value) : short_date($item_value) : ""}}'/>
@else
    <div class='input-group dx-datetime' data-format="{{ $tm_format }}" data-locale = "{{ Lang::locale() }}" data-is-time = "{{ $is_time }}">
        <span class='input-group-btn'>
            <button {{ ($is_disabled) ? 'disabled' : '' }} type='button' class='btn btn-white dx-datetime-cal-btn' style="border: 1px solid #c2cad8!important; margin-right: -2px!important;"><i class='fa fa-calendar'></i></button>
        </span>
        <input {{ ($is_disabled) ? 'disabled' : '' }} class='form-control dx-datetime-field' type=text name = '{{ $item_field }}' value = '{{ $item_value }}' style='width: {{ $fld_width }}px;' {{ ($is_required) ? 'required' : '' }} />
    </div>
    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
@endif