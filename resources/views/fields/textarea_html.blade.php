@if ($is_disabled)
<div dx_fld_name = '{{ $item_field }}' id='{{ $frm_uniq_id }}_{{ $item_field }}' style='padding: 10px; width: 100%; height: 200px; overflow-y: scroll; background-color: #eee; border: 1px solid #e5e6e7; border-radius: 1px;'>
    {!! $item_value !!}
</div>
@else
    <textarea {{ ($is_disabled) ? 'disabled' : '' }} id='{{ $frm_uniq_id }}_{{ $item_field }}' name='{{ $item_field }}' class='ckeditor' rows='10' style='width: 350px'>
        {{ $item_value }}
    </textarea>

    <script type="text/javascript">
        //init_textarea( '{{ $frm_uniq_id }}_{{ $item_field }}' );

        $('#list_item_view_form_{{ $frm_uniq_id }}').bind('shown', function() {
            //init_textarea();
            tinyMCE.execCommand('mceAddEditor', false, '{{ $frm_uniq_id }}_{{ $item_field }}');
        });

        $('#list_item_view_form_{{ $frm_uniq_id }}').bind('hide', function() {

            tinyMCE.execCommand('mceRemoveEditor', false, '{{ $frm_uniq_id }}_{{ $item_field }}');
        });
    </script>
@endif