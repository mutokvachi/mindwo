@if ($group_label)
    <div style="border-bottom: solid 1px #c1c1c1; margin-bottom: 20px;" class="dx-group-label">
        <b>{{ $group_label }}</b>
    </div>
@endif

<div class='form-group has-feedback dx-form-field-line' dx_fld_name_form="{{ $fld_name }}">
    <label class='col-lg-4 control-label'>
        @if ($hint)
        <i class='fa fa-question-circle dx-form-help-popup' title='{{ $hint }}' style='cursor: help;'></i>&nbsp;
        @endif
        
        {{ $label_title }}
        
        @if ($is_required)
            <span style="color: red"> *</span>
        @endif
    </label>
    <div class='col-lg-8'>
        {!! $item_htm !!}     
        <div class="help-block with-errors"></div>
    </div>    
</div>