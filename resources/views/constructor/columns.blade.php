@extends('constructor.common')

@section('constructor_content')
  <div id='view_editor' class="dx-block-container-view dx-popup-modal" dx_tab_id="" dx_view_id="{{ $view_id }}">
    <div id="view_editor_popup">
      {!! $htm !!}
    </div>
    @include('blocks.view.field_settings', ['block_id' => 'view_editor'])
  </div>
    <div class="col-md-6">
      <a href="javascript:;" data-title="Add new field to default view and form" class="btn red dx-new-field btn-block">
        <i class="fa fa-plus-square"></i> Add a new field </a>
    </div>
@endsection