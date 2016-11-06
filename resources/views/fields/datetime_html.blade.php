@if ($is_disabled)
    <div dx_fld_name = '{{ $item_field }}' style="margin-top: 8px">{!! ($item_value) ? format_event_time($item_value) : '' !!}</div>
@else
    <div class='input-group'>
        <span class='input-group-btn'>
            <button {{ ($is_disabled) ? 'disabled' : '' }} type='button' class='btn btn-white' id='{{ $frm_uniq_id }}_{{ $item_field }}_cal'><i class='fa fa-calendar'></i></button>
        </span>
        <input {{ ($is_disabled) ? 'disabled' : '' }} class='form-control' type=text id='{{ $frm_uniq_id }}_{{ $item_field }}' name = '{{ $item_field }}' value = '{{ $item_value }}' {{ ($is_required) ? 'required' : '' }} />
    </div>
  <!--  <span class="glyphicon form-control-feedback" aria-hidden="true"></span> -->
@endif