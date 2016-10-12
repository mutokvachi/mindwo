@if (count($js_code) > 0)
    <div dx_attr="list_item_view_form_{{ $frm_uniq_id }}">
        <script type='text/javascript'>	
            @foreach ($js_code as $item)            
                function custom_JavaScript_{{ $js_form_id }}_{{ $item->id }}()
                {
                    var form_object = $('#list_item_view_form_{{ $frm_uniq_id }}');

                    {!! $item->js_code !!}                
                }
                custom_JavaScript_{{ $js_form_id }}_{{ $item->id }}();
            @endforeach
        </script>
    </div>
@endif