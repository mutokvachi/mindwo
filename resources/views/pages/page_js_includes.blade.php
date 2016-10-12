@foreach($inc_arr as $inc)
    <script src="{{Request::root()}}/{{ getIncludeVersion($inc) }}" type="text/javascript"></script>
@endforeach
