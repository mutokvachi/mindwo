<div class="input-group">
    <span class="input-group-addon" id="basic-addon_{{ $frm_uniq_id }}_{{ $item_field }}" style="background-color: {{ $item_value }};">&nbsp;</span>
    <input type="text" class="form-control colorpicker-rgba" value="{{ $item_value }}" {{ ($is_disabled) ? 'disabled' : '' }} {{ ($is_required) ? 'required' : '' }} id='{{ $frm_uniq_id }}_{{ $item_field }}' name = '{{ $item_field }}' aria-describedby="basic-addon_{{ $frm_uniq_id }}_{{ $item_field }}">    
</div>
<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
<script type="text/javascript">
    if (jQuery().colorpicker)
    {
        $('#{{ $frm_uniq_id }}_{{ $item_field }}').colorpicker({
            format: 'rgba'
        }).on('hide', function(ev) {
            $('#basic-addon_{{ $frm_uniq_id }}_{{ $item_field }}').css('background-color', $('#{{ $frm_uniq_id }}_{{ $item_field }}').val());
        });
    }
    
    $('#{{ $frm_uniq_id }}_{{ $item_field }}').change(function() {
        $('#basic-addon_{{ $frm_uniq_id }}_{{ $item_field }}').css('background-color', $(this).val());
    });
</script>