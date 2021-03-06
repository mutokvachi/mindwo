@section('fields_row_content'.(isset($id) ? '_'.$id : ''))
  <div class="form-group has-success">
    <div class="input-group">
      <span class="input-group-addon">
        <i class="fa fa-tag font-blue"></i>
      </span>
      <input class="form-control dx-constructor-label" name="group_label" autocomplete="off"
        placeholder="{{ trans('constructor.group_label') }}"
        value="{{ isset($field) ? $field->group_label : '' }}">
    </div>
  </div>
@endsection

@include('constructor.fields_row')