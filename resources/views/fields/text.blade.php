<input {{ ($is_disabled) ? 'readonly' : '' }} class='form-control' type=text id='{{ $frm_uniq_id }}_{{ $item_field }}' name = '{{ $item_field }}'  maxlength='{{ $max_lenght }}' value = '{{ $item_value }}' {{ ($is_required) ? 'required' : '' }}/>
<span class="glyphicon form-control-feedback" aria-hidden="true"></span>