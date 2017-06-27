@extends('constructor.common')

@section('constructor_content')
<div style="width: 50%; margin: 0 auto;">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="form-group">
        <label for="list_name">
          <span>{{ trans('constructor.register_name') }}</span>
        </label>
        <input class="form-control" type="text" id="list_name" name="list_name" maxlength="100"
          value="{{ $list ? $list->list_title : '' }}">
      </div>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="form-group">
        <label for="item_name">
          <span>{{ trans('constructor.item_name') }}</span>
        </label>
        <input class="form-control" type="text" id="item_name" name="item_name" maxlength="100"
          value="{{ $item_title }}">
      </div>
    </div>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="form-group">
        <label for="item_name">
          <span>{{ trans('constructor.register_menu_parent') }}</span>
        </label>
          {!! $register_menu_field_htm !!}
      </div>
  </div>
</div>
@endsection