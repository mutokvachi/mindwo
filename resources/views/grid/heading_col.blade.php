<th class='t_header' style="white-space: nowrap; cursor: pointer;
@if ($width > 0)
  width: {{ $width }}px;
@endif
  "
  data-title='{{ $fld_title }}' fld_name='{{ $fld_name }}'>
  <div>
    @if ($fld_name)
      <a class="header-filter" style='color: silver; float:left; width: 20px; height: 20px; padding-left: 8px; margin-right: 4px;'><i class='fa fa-ellipsis-v'></i></a>
    @endif
    <span style='padding-right: 20px;'>{{ $fld_title }}
      @if ($sort_dir)
        <i class='fa fa-caret-{{ $sort_dir }}'></i>
      @endif
    </span>
  </div>
</th>