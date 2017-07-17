@extends('constructor.common')

@section('constructor_content')
  <div id='view_editor' class="dx-block-container-view dx-popup-modal" dx_tab_id="" dx_view_id="{{ $view_id }}">
    <div id="view_editor_popup">
      {!! $htm !!}
    </div>
    @include('blocks.view.field_settings', ['block_id' => 'view_editor'])
  </div>
@endsection