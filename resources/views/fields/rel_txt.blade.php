<div class="input-group dx-rel-id-field" id = "{{ $frm_uniq_id }}_{{ $item_field }}_rel_field" style="width: 100%;"> 
    @if ($is_disabled)     
        <input type=hidden id='{{ $frm_uniq_id }}_{{ $item_field }}' value='{{ $item_value }}' name = '{{ $item_field }}' />
        <input class='form-control' readonly value='{{ $item_value }}' />          
    @else   
        <select class='form-control dx-not-focus' id='{{ $frm_uniq_id }}_{{ $item_field }}' name = '{{ $item_field }}' {{ ($is_required) ? 'required' : '' }} >
            @if (!((count($items) == 1 && $is_required) || ($item_value > 0 && $is_required)))
                <option value=''></option>
            @endif
            @foreach($items as $item)
                <option {{ (trim($item) == $item_value) ? 'selected' : '' }} value='{{ trim($item) }}'>{{ trim($item) }}</option>
            @endforeach
        </select>
        <span class="glyphicon form-control-feedback" aria-hidden="true" style="margin-right: 40px;"></span>
    @endif        
</div>