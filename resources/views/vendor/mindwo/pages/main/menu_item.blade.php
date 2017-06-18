@if (Config::get('dx.is_horizontal_menu'))
    <li class="{{ ($level ==0) ? $active : '' }} {{($level > 0 && $sub_items_htm) ? 'dropdown-submenu' : '' }}" data-level="{{ $level }}">
        <a href="{{ $href }}" data-list-id="{{ $list_id }}" data-view-id="{{ $view_id }}" data-level="{{ $level }}" data-toggle="{{ ($sub_items_htm) ? 'dropdown' : '' }}" class="{{ ($sub_items_htm) ? 'dropdown-toggle' : ''}}" {{ $target }} {!! ($color) ? 'style="color: ' . $color . ';"' : '' !!}>
            
            @if ($icon_class)
            <i class="{{ $icon_class }}" {!! ($color) ? 'style="color: ' . $color . ';"' : '' !!}></i>
            @endif

            {{ $title }}
            
            @if ($sub_items_htm && $level == 0)
                <b class="caret"></b>
            @endif

        </a>
        @if ($sub_items_htm)
            <ul class="dropdown-menu">
                {!! $sub_items_htm !!}
            </ul>
        @endif
    </li>
@else
    <li class="nav-item {{ $active }} {{ $open }}" data-level="{{ $level}}">
        <a href="{{ $href }}" data-list-id="{{ $list_id }}" data-view-id="{{ $view_id }}" data-level="{{ $level }}" class="nav-link {{ ($sub_items_htm) ? 'nav-toggle' : ''}}" {{ $target }} {!! ($color) ? 'style="color: ' . $color . ';"' : '' !!}>

            @if ($icon_class)
            <i class="{{ $icon_class }}" {!! ($color) ? 'style="color: ' . $color . ';"' : '' !!}></i>
            @endif

            <span class="title">{{ $title }}</span>

            @if ($selected)
            <span class="selected"></span>
            @endif

            @if ($sub_items_htm)
                <span class="arrow {{ ($open) ? 'nav-toggle open' : ''  }}"></span>
            @endif
        </a>
        @if ($sub_items_htm)
            <ul class="sub-menu">
                {!! $sub_items_htm !!}
            </ul>
        @endif
    </li>   
@endif
