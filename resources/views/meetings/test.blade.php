@extends('frame')

@section ('main_custom_css')
  
  @include('pages.view_css_includes')

@stop

@section('main_content')
  <div id='view_editor' class="dx-block-container-view dx-popup-modal"
    dx_tab_id=""
    dx_view_id="{{ $view_id }}"
  >
    <div id="view_editor_popup">
      {!! $htm !!}
      <div>
        <button type='button' class='btn btn-primary dx-view-btn-save'>{{ trans('form.btn_save') }}</button>
      </div>
    </div>
    @include('blocks.view.field_settings', ['block_id' => 'view_editor'])
  </div>
@stop

@section('main_custom_javascripts')
  
  @include('pages.view_js_includes')
  
  <script>
	  $(document).ready(function()
	  {
		  
		  var save_callback = function(view_id)
		  {
			  alert("View saved with ID " + view_id);
		  };
		  
		  $(".dx-view-edit-form").ViewEditor({
			  view_container: $("#view_editor"),
			  reloadBlockGrid: null,
			  root_url: getBaseUrl(),
			  load_tab_grid: null,
			  onsave: save_callback,
		  });
	  });
  
  
  </script>
@stop