@if ($group_label)
    <div style="border-bottom: solid 1px #c1c1c1; margin-bottom: 20px;" class="dx-group-label">
        <b>{{ $group_label }}</b>
    </div>
@endif

<div class='form-group has-feedback dx-form-field-line' dx_fld_name_form="{{ $fld_name }}" style="margin-left: 15px; margin-right: 15px;">
    <label for="{{ $frm_uniq_id }}_{{ $fld_name }}" style="vertical-align: top; margin-right: 10px;">
        @if ($hint)
        <i class='fa fa-question-circle dx-form-help-popup' title='{{ $hint }}' style='cursor: help;'></i>&nbsp;
        @endif
        
        {{ $label_title }}
        
        @if ($is_required)
            <span style="color: red"> *</span>
        @endif
    </label>
    
    {!! $item_htm !!}     
    <div class="help-block with-errors"></div>
        
</div>