<li class="dd-item dd3-item" 
    data-id="{{ $menu_id }}" 
    data-list-id="{{ $list_id }}" 
    data-url="{{ $href }}" 
    data-parent-id="{{ $parent_id}}" 
    data-order-index ="{{ $order_index }}"
    data-icon="{{ $icon_class }}"
    data-title="{{ $title }}"
    data-color="{{$color}}"
    >
    <div class="dd-handle dd3-handle"> </div>
    <div class="dd3-content">
        
        <i class="{{ $icon_class }} dx-icon" {!! ($color) ? 'style="color: ' . $color . ';"' : '' !!}></i>
        

        <span class="dx-title" {!! ($color) ? 'style="color: ' . $color . ';"' : '' !!}>{{ $title }}</span>
        <a href="javascript:;" class="dx-edit-menu-link pull-right"><i class="fa fa-cog"></i></a>
    </div>
    @if ($sub_items_htm)      
        <ol class="dd-list">
        {!! $sub_items_htm !!} 
        </ol>
    @endif
</li>

