<div class="input-group dx-reg-nr-field" style="width: 100%;" dx_list_id="{{ $list_id }}" dx_reg_nr_field_id="{{ $reg_nr_field_id }}">
    <input {{ ($is_disabled) ? 'disabled' : '' }} class='form-control' type=text id='{{ $frm_uniq_id }}_{{ $item_field }}' name = '{{ $item_field }}'  maxlength='{{ $max_lenght }}' value = '{{ $item_value }}'/>
    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
    
    @if ($is_reg_btn_shown)
        <span class="input-group-btn">
            <button class="btn btn-primary" type="button" title="Reģistrēšana"><i class='fa fa-pencil'></i> Reģistrēšana</button>
        </span>
    @endif
</div>