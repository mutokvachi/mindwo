<div class="input-group dx-autocompleate-field" id = "{{ $frm_uniq_id }}_{{ $item_field }}_auto_field" style="width: 100%;"
        data-is-init = "0"
        data-form-url = "{{ $form_url }}"
        data-rel-list-id = "{{ $rel_list_id }}"
        data-rel-field-id = "{{ $rel_field_id }}"
        data-item-field = "{{ $item_field }}"
        data-trans-search = "{{ trans("fields.search_record") }}"
        data-rel-view_id = "{{ $rel_view_id }}"
        data-rel-formula-field = "{{ $rel_display_formula_field }}"
        data-item-value = "{{ $item_value }}"
        data-item-text = "{{ $txt_display }}"
        data-is-profile = '{{ ($rel_list_id == Config::get('dx.employee_list_id',0) && Config::get('dx.employee_profile_page_url', '')) }}'
        data-profile-url = '{{ Request::root() }}{{ Config::get('dx.employee_profile_page_url', '') }}'
>
    @if ($is_disabled)

            <input class='form-control dx-auto-input-txt' readonly value='{{ $txt_display }}' id="{{ $frm_uniq_id }}_{{ $item_field }}_auto_display" dx_fld_name = '{{ $item_field }}'/>
            <input class="dx-auto-input-id" type=hidden id='{{ $frm_uniq_id }}_{{ $item_field }}' value='{{ $item_value }}' name = '{{ $item_field }}' />

            @if ($item_value > 0)
                <span class="input-group-btn">
                    <button class="btn btn-white dx-rel-id-add-btn" type="button" title="{{ ($rel_list_id == Config::get('dx.employee_list_id',0) && Config::get('dx.employee_profile_page_url', '')) ? trans('employee.lbl_open_profile') : trans('fields.view_record') }}"><i class='fa fa-external-link'></i></button>                    
                </span>
            @endif
    @else    
        <input class="dx-auto-input-id" type=hidden id='{{ $frm_uniq_id }}_{{ $item_field }}' name = '{{ $item_field }}' value = '{{ $item_value }}' />
        <input class="dx-auto-input-select2" type='text' id='{{ $frm_uniq_id }}_{{ $item_field }}_txt' name = '{{ $item_field }}_txt' value = '{{ $txt_display }}' class='form-control select2-remote' {{ ($is_required) ? 'required' : '' }} dx_fld_name = '{{ $item_field }}' style="width: 100%;"/>
        <span class="input-group-btn">
            <button class="btn btn-white dx-rel-id-del-btn" type="button" title="{{ trans('fields.remove_field_value') }}" style='margin-left: 2px; margin-right: 2px;'><i class='fa fa-trash-o'></i></button>
            <button class="btn btn-white dx-rel-id-add-btn" type="button" title="{{ ($rel_list_id == Config::get('dx.employee_list_id',0) && Config::get('dx.employee_profile_page_url', '')) ? trans('employee.lbl_open_profile') : trans('fields.set_field_value') }}"><i class='fa fa-external-link'></i></button>
        </span>    
    @endif
</div>
						