<input {{ ($is_disabled) ? 'disabled' : '' }} class='form-control' type=password id='{{ $frm_uniq_id }}_{{ $item_field }}' name = '{{ $item_field }}'  maxlength='{{ $max_lenght }}' value = 'BLANK' {{ ($is_required) ? 'required' : '' }}/>
<span class="glyphicon form-control-feedback" aria-hidden="true"></span>