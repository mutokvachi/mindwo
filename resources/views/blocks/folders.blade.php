<div class="portlet light dx-folders-portlet" dx_is_init="0" dx_menu_id="{{ $menu_id }}">	
	<div class="portlet-body">
		@foreach($sets as $set)                    
                    @include('blocks.folders_set', ['set' => $set])
                @endforeach										
	</div>
</div>