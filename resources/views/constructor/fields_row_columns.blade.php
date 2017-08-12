@section('fields_row_content'.(isset($id) ? '_'.$id : ''))
  <div class="row columns dd-list">
    @if(isset($id))
      @stack('row_content_'.$id)
    @endif
  </div>
@endsection

@include('constructor.fields_row')
