<div class="input-group dx-rel-id-field" id = "{{ $frm_uniq_id }}_{{ $item_field }}_rel_field" style="width: 100%;" 
        data-is-init = "0"
        data-form-url = "{{ $form_url }}"
        data-rel-list-id = "{{ $rel_list_id }}"
        data-rel-field-id = "{{ $rel_field_id }}"
        data-item-field = "{{ $item_field }}"
        data-binded-field-name = "{{ $binded_field_name }}"
        data-binded-field-id = "{{ $binded_field_id }}"
        data-binded-rel-field-id = "{{ $binded_rel_field_id }}"
        data-item-value = "{{ $item_value }}"
        data-frm-uniq-id = "{{ $frm_uniq_id }}"
        data-trans-must-choose = "{{ trans('fields.must_choose') }}"
> 
    @if ($is_disabled)
     
        <input type=hidden id='{{ $frm_uniq_id }}_{{ $item_field }}' value='{{ $item_value }}' name = '{{ $item_field }}' />
        <input class='form-control dx-rel-id-text' readonly 

        @foreach($items as $item)
            @if ($item->id == $item_value)
                value='{{ $item->txt }}'
            @endif
        @endforeach
        dx_fld_name = '{{ $item_field }}' dx_binded_field_id = '{{ $binded_field_id }}' dx_binded_rel_field_id = '{{ $binded_rel_field_id }}' />          
    @else   
        <select class='form-control dx-not-focus' id='{{ $frm_uniq_id }}_{{ $item_field }}' dx_fld_name = '{{ $item_field }}' name = '{{ $item_field }}' {{ ($is_required) ? 'required' : '' }}  
            @if ($binded_field_name)
                onchange="load_binded_field('{{ $frm_uniq_id }}_{{ $item_field }}', '{{ $frm_uniq_id }}_{{ $binded_field_name }}', {{ $binded_field_id }}, {{ $binded_rel_field_id }})"
            @endif
             data-foo="bar" dx_binded_field_id = '{{ $binded_field_id }}' dx_binded_rel_field_id = '{{ $binded_rel_field_id }}' 
            >@if (!((count($items) == 1 && $is_required) || ($item_value > 0 && $is_required)))
                <option value=0></option>
            @endif
            @foreach($items as $item)
                <option {{ ($item->id == $item_value) ? 'selected' : '' }} value='{{ $item->id }}'>{{ $item->txt }}</option>
            @endforeach
        </select>
        <span class="glyphicon form-control-feedback" aria-hidden="true" style="margin-right: 40px;"></span>
    @endif
        <span class="input-group-btn" style="padding-left: 1px;">
            @if ($is_disabled && $item_value)
                <button class="btn btn-white dx-rel-id-view-btn" type="button" title="{{ trans('fields.view_value') }}" data-item-id="{{ $item_value }}" style="border: 1px solid #c2cad8!important; margin-left: -2px!important;"><i class='fa fa-external-link'></i></button>
            @endif
            
            @if (!$is_disabled)
                <button class="btn btn-white dx-rel-id-edit-btn" type="button" title="{{ trans('fields.edit_value') }}" style="border: 1px solid #c2cad8!important; margin-left: -2px!important;" data-readmode="{{ $is_disabled}}" data-item-id="{{ $item_value }}"><i class='fa fa-pencil-square-o'></i></button>
                <button class="btn btn-white dx-rel-id-add-btn" type="button" title="{{ trans('fields.add_value') }}" style="border: 1px solid #c2cad8!important; margin-left: -2px!important;"><i class='fa fa-plus'></i></button>
            @endif
        </span>
    
    

</div>