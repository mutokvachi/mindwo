<th class='t_header' style="
@if ($width > 0)
    width: {{ $width }}px;
@endif
"
 data-title='{{ $fld_title }}' fld_name='{{ $fld_name }}'>{{ $fld_title }}
@if ($sort_dir)
    <i class='fa fa-caret-{{ $sort_dir }}'></i>
@endif
</th>