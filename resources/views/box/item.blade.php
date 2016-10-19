<div class="col-md-3 col-sm-4 col-xs-6 folder">
    <div class="folder-content {{ ($item->is_register) ? "bg-grey-steel" : "bg-grey-cararra"}}"
         data-dx-id="{{ $item->id }}"
         @if ($item->item_count > 0)
         name="actionSlide"
         @else
         name="actionUrl"
         data-url="/{{ $item->view_url }}"
         @endif
         >

         <div class="folder-icon">
            @if ($item->fa_icon)
            <i class="{{ $item->fa_icon }}"></i>
            @elseif ($item->is_register)
            <i class="fa fa-th"></i>
            @else
            <i class="fa fa-folder-o"></i>
            @endif
        </div>
        <div class="folder-object">
            <h4>{{ $item->title }}</h4>
        </div>

    </div>
</div>
