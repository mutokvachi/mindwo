<div id="slides-container">
    @foreach($sets as $key => $set)                    
    @include('box.set', ['set' => $set, 'key' => $key])
    @endforeach										
</div>