<div class='fileinput fileinput-{{ $class_exist }} input-group' data-provides='fileinput' style="width: 100%;" dx_file_field_id="{{ $field_id }}">
    <div class='form-control'>
            <i class='glyphicon glyphicon-file fileinput-exists'></i> 
            <span class='fileinput-filename truncate' style="max-width: 300px;">
                @if ($item_value)
                    <a href='JavaScript: download_file({{ $item_id }}, {{ $list_id }}, {{ $field_id }});'>{{ $item_value }}</a>
                @endif
            </span>
    </div>
    @if (!$is_disabled)
        <span class='input-group-addon btn btn-default btn-file'>
                <span class='fileinput-new'>{{ trans('fields.btn_set') }}</span>
                <span class='fileinput-exists'>{{ trans('fields.btn_change') }}</span>
                <input type='file' name='{{ $item_field }}' {{ ($is_required) ? 'required' : '' }}/>
                <input class='fileinput-remove-mark' type='hidden' value='0' name = '{{ $item_field_remove }}' />
        </span>
        <a href='#' class='input-group-addon btn btn-default fileinput-exists' data-dismiss='fileinput'>{{ trans('fields.btn_remove') }}</a>
        <input type="hidden" name='{{ $item_field }}_is_set' value="{{ ($item_value) ? 1: '' }}" {{ ($is_required) ? 'required' : '' }} />
    @endif
</div>