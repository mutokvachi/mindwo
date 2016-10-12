<div class="tile double bg-{{ ($item->is_register) ? "grey-cararra" : "grey-steel"}}" 
     dx_id="{{ $item->id }}" 
     dx_is_register = "{{ $item->is_register }}"
     dx_url = "{{ $item->view_url }}"
     >
        <div class="tile-body">                                    
                <h4>{{ $item->title }}</h4>
                @if ($item->fa_icon)
                    <i class="{{ $item->fa_icon }}"></i>
                @else
                    @if ($item->is_register)
                        <i class="fa fa-th"></i>
                    @else
                        <i class="fa fa-folder-o"></i>
                    @endif
                @endif
        </div>
        <div class="tile-object">
                <div class="name"> {{ ($item->is_register) ? "Ieraksti" : "Katalogi"}} </div>
                <div class="number"> {{ $item->item_count }} </div>
        </div>
</div>

