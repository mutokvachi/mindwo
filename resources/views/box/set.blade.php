<div class="row" id="slide-{{ $set["parent_id"] }}" data-parent-id="{{ $set["parent_id"] }}">
    @foreach($set["items"] as $item)
    @include('box.item', ['item' => $item])
    @endforeach
</div>
