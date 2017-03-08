 @if (!(isset($is_pdf) && $is_pdf))
    <a href = 'JavaScript: download_file({{ $item_id }}, {{ $list_id }}, {{ $field_id }});'><i class='glyphicon glyphicon-file'></i> {{ $cell_value }}</a>
 @else
    <a href='{{Request::root()}}/web/viewer.html?file={{Request::root()}}/download_file_{{ $item_id }}_{{ $list_id }}_{{ $field_id }}.pdf' target="_blank">{{ $cell_value }}</a>
 @endif