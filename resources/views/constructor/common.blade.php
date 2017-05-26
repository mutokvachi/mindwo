@extends('frame')

@section('main_custom_css')
  <link href="{{ elixir('css/elix_mail.css') }}" rel="stylesheet"/>
  @include('pages.view_css_includes')
  <style>
    .mt-element-step .step-line .mt-step-title::after {
      top: -32px;
    }
    
    .mt-element-step .step-line .mt-step-title::before {
      top: -32px;
    }
    
    .dd-item.ui-draggable {
      z-index: 100;
    }
    
    .dd-item.ui-draggable.ui-draggable-dragging {
      z-index: 1000;
    }
    
    .droppable-grid {
      border-collapse: collapse;
      width: 100%;
    }
    
    .droppable-grid td {
      width: 25%;
      padding: 2px;
      height: 36px;
      border: 1px dashed #ccc;
    }
    
    .dx-cms-field-remove {
      display: none;
    }
    
    .dropped .dx-cms-field-remove {
      display: block;
    }
  </style>
@endsection

@section('main_custom_javascripts')
  @include('pages.view_js_includes')
	<script src="{{ elixir('js/elix_constructor_wizard.js') }}" type='text/javascript'></script>
  <script>
	  $(document).ready(function()
	  {
		  $('.dx-constructor-wizard').ConstructorWizard({
			  list_id: {{ $list_id }},
			  view_id: {{ $view_id }},
			  step: '{{ $step }}'
		  });
	  });
	  
	  $(document).ready(function()
	  {
		  $('.dx-adv-btn').click(function()
		  {
			  var settings_closed = function()
			  {
				  // reload all page - there could be changes made very deep in related objects..
			  };
			  
			  // if list_id = 0 then try to save with AJAX (must be register title provided)
			  // for new registers user object_id = 140
			  
			  view_list_item('form', list_id, 3, 0, 0, "", "", {after_close: settings_closed});
		  });
		  
		  $('.dx-preview-btn').click(function()
		  {
			  // if list_id = 0 then try to save with AJAX (must be register title provided)
			  // for new registers user object_id = 140
			  
			  new_list_item(list_id, 0, 0, "", "");
		  });
		  
		  $('.dx-new-field').click(function()
		  {
			  var field_closed = function(frm)
			  {
				  // update here fields list
				  // add in form in new row as last item too
				  
				  // get meta data from frm with jquery find
				  
				  // all cms forms have field item_id if it is 0 then item is not saved
				  alert(frm.html());
			  };
			  
			  // if list_id = 0 then save list first with ajax then continue
			  new_list_item(7, 17, list_id, "", "", {after_close: field_closed});
		  });
	  });
  </script>
@endsection

@section('main_content')
  <div class="portlet light">
    <div class="portlet-title">
      <div class="caption font-grey-cascade uppercase">
        <i class="fa fa-list"></i> Register
        @if($list)
          - {{ $list->list_title }}
        @endif
      </div>
      <div class="btn-group dx-register-tools pull-right">
        <button type="button" class="btn btn-white dx-adv-btn">
          <i class="fa fa-cog"></i> Advanced settings
        </button>
      </div>
    </div>
    <div class="portlet-body dx-constructor-wizard">
      <div class="row">
        @include('constructor.steps')
      </div>
      <div class="row" style="margin-bottom: 20px">
        @section('constructor_content')
        @show
      </div>
      <div class="row">
        <div class="col-md-12" style="text-align: center">
          @if($step != 'names')
            <button id="prev_step" type="button" class="btn btn-primary dx-wizard-btn pull-left">
              <i class="fa fa-arrow-left"></i> Back
            </button>
          @endif
          <button id="submit_step" type="button" class="btn btn-primary dx-wizard-btn pull-right">
            Save
            @if($step != 'menu')
              & next <i class="fa fa-arrow-right"></i>
            @endif
          </button>
        </div>
      </div>
    </div>
  </div>
@endsection

