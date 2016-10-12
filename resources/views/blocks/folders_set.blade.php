<div class="tiles" dx_id="{{ $set["parent_id"] }}">
    @foreach($set["items"] as $item)
        @include('blocks.folders_item', ['item' => $item])
    @endforeach
</div>
