<input {{ ($is_disabled) ? 'disabled' : '' }} class='form-control' type=text id='{{ $frm_uniq_id }}_{{ $item_field }}' name = '{{ $item_field }}'  maxlength='5' value = '{{ $item_value }}' style='width: 100px;' {{ ($is_required) ? 'required' : '' }}/> 
<span class="glyphicon form-control-feedback" aria-hidden="true"></span>