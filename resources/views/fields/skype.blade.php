@if ($is_disabled && $item_value)
    <div class="input-group">
        <a class="input-group-addon dx-field-btn" id="basic-addon_{{ $frm_uniq_id }}_{{ $item_field }}" href="skype:{{ $item_value }}?chat" title="{{ trans('fields.hint_skype') }}"><i class="fa fa-skype"></i></a>
        <input readonly class='form-control' type=text id='{{ $frm_uniq_id }}_{{ $item_field }}' name = '{{ $item_field }}' value = '{{ $item_value }}' aria-describedby="basic-addon_{{ $frm_uniq_id }}_{{ $item_field }}"/>   
    </div>
@else
    <input {{ ($is_disabled) ? 'readonly' : '' }} class='form-control' autocomplete="off" type=text id='{{ $frm_uniq_id }}_{{ $item_field }}' name = '{{ $item_field }}'  maxlength='{{ $max_lenght }}' value = '{{ $item_value }}' {{ ($is_required) ? 'required' : '' }}/>
    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
@endif