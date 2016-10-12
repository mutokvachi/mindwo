<th class='t_header' 
@if ($width > 0)
    style='width: {{ $width }}px' 
@endif
 data-title='{{ $fld_title }}' fld_name='{{ $fld_name }}'>{{ $fld_title }}
@if ($sort_dir)
    <i class='fa fa-caret-{{ $sort_dir }}'></i>
@endif
</th>