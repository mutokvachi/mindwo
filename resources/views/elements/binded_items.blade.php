<option value=0></option>
@foreach ($data as $item)
    <option value='{{ $item->id }}'>{{ $item->txt }}</option>
@endforeach