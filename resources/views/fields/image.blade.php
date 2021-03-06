<div class='fileinput fileinput-{{ $class_exist }} dx-image-fld' data-provides='fileinput'>
    <div class="dx-fileinput-thumbnail">
        <div class='fileinput-preview thumbnail'
             style='width: 200px; height: 150px; display:table-cell; vertical-align:middle; text-align:center'>
            @if ($item_value)
                <a href='JavaScript: download_file({{ $item_id }}, {{ $list_id }}, {{ $field_id }});'>
                    <img src='{{Request::root()}}/formated_img/medium/{{ $file_guid }}' alt='{{ $item_value }}' style='max-height: 140px;'/>
                </a>
            @endif
        </div>
    </div>
    <div class="dx-fileinput-buttons" style="margin-top: 10px; cursor: pointer;">
        @if (!$is_disabled)
            <span class='btn btn-primary btn-file' title="{{ trans('fields.btn_set_change') }}">
                <span class='fileinput-new'>{{ trans('fields.btn_set') }}</span>
                <span class='fileinput-exists'><i class="fa fa-pencil"></i></span>
                <input type='file' name='{{ $item_field }}' accept="{{ $ext }}" class="dx-img-file-input"/>
                <input class='fileinput-remove-mark' type='hidden' value='0' name='{{ $item_field_remove }}'/>
            </span>
            <div class="btn btn-default dx-rotate-btn" title="{{ trans('fields.btn_rotate_right') }}">
                <span><i class="fa fa-rotate-right"></i></span>
            </div>
            <input type="hidden" name="{{ $item_field }}_rotate_angle" class="dx-rotate-angle-input" value="0" />
            <a href='javascript:;' class='btn btn-white fileinput-exists' data-dismiss='fileinput'
               title="{{ trans('fields.btn_remove') }}"><i class="fa fa-trash-o"></i></a>
        @else
            <div>
                <span name='{{ $item_field }}'></span>
            </div>
        @endif
    </div>
    <input type="hidden" name='{{ $item_field }}_is_set'
           value="{{ ($item_value) ? 1: '' }}" {{ ($is_required) ? 'required' : '' }} />
</div>