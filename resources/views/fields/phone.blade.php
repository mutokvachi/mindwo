@if ($is_disabled)
<input readonly class='form-control' type='text' id='{{ $frm_uniq_id }}_{{ $item_field }}' name = '{{ $item_field }}'  maxlength='{{ $max_lenght }}' value = '{{ $item_value }}' />
@else
<div class="input-group dx-phone-field" style='width: 100%' data-country-list-id = "{{ $country_list_id }}">
    <select class='form-control dx-phone-select' style="width:30%; padding-right: 0px; ">
        <option value='0'></option>
        @foreach($countries as $country)
        <option value='{{ $country->phone_code }}' {{ ($country->phone_code == $code_part || count($countries) == 1) ? 'selected' : '' }}>{{ $country->code . ' (' . $country->phone_code . ')'}}</option>
        @endforeach
        
        @if ($is_new_rights)
            <option value='new'>{{ trans('fields.add_new_phone') }}</option>
        @endif
    </select>
    <input class='form-control dx-phone-input' autocomplete="off" type='text' style="width:70% " maxlength='{{ $max_lenght }}' value = '{{ $nr_part }}' {{ ($is_required) ? 'required' : '' }}/>
    <input class='dx-phone-hidden' type='hidden' id='{{ $frm_uniq_id }}_{{ $item_field }}' name = '{{ $item_field }}'  value = '{{ $item_value }}' />
    
</div>
@endif