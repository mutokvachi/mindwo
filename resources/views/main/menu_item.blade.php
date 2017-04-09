<li class="nav-item {{ $active }} {{ $open }}">
  <a href="{{ $href }}" data-level="{{ $level }}" class="nav-link {{ ($sub_items_htm) ? 'nav-toggle' : ''}}" {{ $target }} {!! ($color) ? 'style="color: ' . $color . ';"' : '' !!}>
    @if($icon_class)
      <i class="{{ $icon_class }}" {!! ($color) ? 'style="color: ' . $color . ';"' : '' !!}></i>
    @endif
    <span class="title">{{ $title }}</span>
    @if($selected)
      <span class="selected"></span>
    @endif
    @if($sub_items_htm)
      <span class="arrow {{ ($open) ? 'nav-toggle open' : ''  }}"></span>
    @endif
  </a>
  @if($sub_items_htm)
    <ul class="sub-menu">
      {!! $sub_items_htm !!}
    </ul>
  @endif
</li>