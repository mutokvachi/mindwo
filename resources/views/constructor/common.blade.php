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
  <script>
	  var list_id = {{ $list_id }};
	  var step = '{{ $step }}';
	  var url = {
		  base: '/constructor/register'
	  };
	  
	  $(document).ready(function()
	  {
		  $('.dd-item').draggable({
			  handle: '.dd-handle',
			  revert: 'invalid',
			  helper: 'clone'
		  });
		  
		  $('.droppable-grid td').droppable({
			  accept: '.dd-item',
			  drop: function(event, ui)
			  {
				  $(this).append(ui.draggable);
				  ui.draggable.addClass('dropped');
			  }
		  });
		  
		  $('.dx-cms-field-remove').click(function()
		  {
			  $(this)
				  .closest('.dropped')
				  .removeClass('dropped')
				  .appendTo('.dd-list');
		  });
		  
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
		  
		  $('#submit_step').click(function()
		  {
			  if(step == 'names')
			  {
				  var listName = $('#list_name');
				  var itemName = $('#item_name');
				  
				  if(listName.length && !listName.val())
				  {
					  toastr.error('Please enter register name.');
					  return false;
				  }
				  
				  if(itemName.length && !itemName.val())
				  {
					  toastr.error('Please enter item name.');
					  return false;
				  }
				  
				  show_page_splash(1);
				  
				  var request = {
					  list_name: listName.val(),
					  item_name: itemName.val()
				  };
				  
				  if(list_id)
				  {
					  request._method = 'put';
				  }
				  
				  $.ajax({
					  type: 'post',
					  url: url.base + (list_id ? '/' + list_id : ''),
					  dataType: 'json',
					  data: request,
					  success: function(data)
					  {
						  hide_page_splash(1);
						  window.location = url.base + '/' + data.list_id + '/fields';
					  },
					  error: function(jqXHR, textStatus, errorThrown)
					  {
						  console.log(textStatus);
						  console.log(jqXHR);
						  hide_page_splash(1);
					  }
				  });
			  }
			  
			  else if(step == 'fields')
			  {
				  show_page_splash(1);
				  
				  var request = {
					  _method: 'put',
					  items: []
				  };
				  
				  $('.droppable-grid td').each(function()
				  {
					  var dd = $(this).children('.dd-item');
					  
					  if(dd.length)
					  {
						  var item = {
							  id: dd.data('id'),
							  row: $(this).parent().prevAll().length + 1,
							  col: $(this).prevAll().length + 1
						  };
						  request.items.push(item);
					  }
				  });
				  
				  $.ajax({
					  type: 'post',
					  url: url.base + '/' + list_id + '/fields',
					  dataType: 'json',
					  data: request,
					  success: function(data)
					  {
						  hide_page_splash(1);
						  window.location = url.base + '/' + list_id + '/rights';
					  },
					  error: function(jqXHR, textStatus, errorThrown)
					  {
						  console.log(textStatus);
						  console.log(jqXHR);
						  hide_page_splash(1);
					  }
				  });
			  }
			  
			  else if(step == 'rights')
			  {
					window.location = url.base + '/' + list_id + '/menu';
			  }
			  
			  else
			  {
				 
			  }
			  
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
    <div class="portlet-body">
      <div class="row">
        @include('constructor.steps')
      </div>
      <div class="row">
        @section('constructor_content')
        @show
      </div>
      <div class="row">
        <div class="col-md-12" style="text-align: center">
          <button id="submit_step" type="button" class="btn btn-primary dx-wizard-btn">
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

