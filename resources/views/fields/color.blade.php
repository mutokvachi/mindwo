@if (!$is_disabled)
<div class="input-group dx-color-field-container">   
@endif

    <input type="color" class="form-control" value="{{ ($item_value) ? $item_value : (($is_disabled) ? '#dcdcdc' : '#fafafa') }}" {{ ($is_disabled) ? 'disabled' : '' }} {{ ($is_required) ? 'required' : '' }} 
           @if ($is_disabled)
                style="padding-right: 15px;"
           @else
                style="cursor: pointer;"
           @endif
           >
    <input type="hidden" value="{{ $item_value }}" name = '{{ $item_field }}' />
@if (!$is_disabled) 
    
    <span class="input-group-btn">            
        <button class='btn btn-white dx-color-del-btn' type='button'><i class='fa fa-trash-o'></i></button>             
    </span>
    
</div>
<span class="glyphicon form-control-feedback" aria-hidden="true" style="margin-right: 45px;"></span>
@endif
