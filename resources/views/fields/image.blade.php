<div class='fileinput fileinput-{{ $class_exist }}' data-provides='fileinput'>
    <table border=0>
        <tr>
            <td valign=top>									
                <div class='fileinput-preview thumbnail' style='width: 200px; height: 150px;'>
                    @if ($item_value)
                        <a href='JavaScript: download_file({{ $item_id }}, {{ $list_id }}, {{ $field_id }});'><img src='{{Request::root()}}/img/{{ $file_guid }}'  alt='{{ $item_value }}' style='max-height: 140px;'></a>
                    @endif
                </div>
            </td>
            <td valign = top style='padding-left:10px;'>
                @if (!$is_disabled)
                    <span class='btn btn-primary btn-file'>

                        <span class='fileinput-new'>{{ trans('fields.btn_set') }}</span>
                        <span class='fileinput-exists'>{{ trans('fields.btn_change') }}</span>                                
                        <input type='file' name='{{ $item_field }}' />
                        <input class='fileinput-remove-mark' type='hidden' value='0' name = '{{ $item_field_remove }}' />                        
                    </span>                        
                    <a href='#' class='btn btn-white fileinput-exists' data-dismiss='fileinput'>{{ trans('fields.btn_remove') }}</a>
                @else
                    <div>
                        <span name='{{ $item_field }}'></span>
                    </div>
                @endif
            </td>
        </tr>							
    </table>
    <input type="hidden" name='{{ $item_field }}_is_set' value="{{ ($item_value) ? 1: '' }}" {{ ($is_required) ? 'required' : '' }} />
</div>