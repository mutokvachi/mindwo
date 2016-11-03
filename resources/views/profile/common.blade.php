@extends('frame')

@section('main_custom_css')
  <link href="{{Request::root()}}/plugins/tree/themes/default/style.min.css" rel="stylesheet" />
  <link href="{{Request::root()}}/metronic/global/plugins/bootstrap-colorpicker/css/colorpicker.css" rel="stylesheet" type="text/css" />
  <link href="{{Request::root()}}/plugins/select2/select2.css" rel="stylesheet" />
  <link href= "{{ elixir('css/elix_view.css') }}" rel="stylesheet" />
  <style type="text/css">
    .freeform .inline .form-control {
      display: inline-block;
      width: auto;
    }
    .freeform .inline .input-group {
      display: inline-block;
      width: auto !important;
    }
    .freeform .inline .input-group-btn {
      display: inline-block;
      width: auto !important;
    }
  </style>
  <style type="text/css">
      .dx-contact-info {
          margin-bottom: 8px;
          text-overflow: ellipsis;
          overflow: hidden;
      }
      
      .employee-details-1 {
          margin-top: 10px;
      }
  </style>
@endsection

@section('main_custom_javascripts')
  <script src='{{Request::root()}}/{{ getIncludeVersion('plugins/tinymce/tinymce.min.js') }}' type='text/javascript'></script>
  <script src="{{ elixir('js/elix_view.js') }}" type='text/javascript'></script>
  <script src="{{ elixir('js/elix_freeform.js') }}" type='text/javascript'></script>
  <script>
    /**
     * InlineForm - a jQuery plugin that provides a way to work with AJAX form embedded into a page
     *
     * @param root
     * @returns {*}
     * @constructor
    */
    $.fn.InlineForm = function(root)
    {
      return this.each(function(){
        new $.InlineForm(this);
      });
    };
    /**
     * InlineForm constructor
     *
     * @param root
     * @constructor
    */
    $.InlineForm = function(root)
    {
      $.data(root, 'InlineForm', this);
      var self = this;
      this.root = $(root);
      this.tabs = $('.tab-pane', this.root);
      this.originalTabs = {};
      this.editButton = $('.dx-edit-profile', this.root);
      this.saveButton = $('.dx-save-profile', this.root);
      this.cancelButton = $('.dx-cancel-profile', this.root);
    
      // Bind callbacks to buttons
      this.editButton.click(function() {
        self.edit();
      });
      this.saveButton.click(function() {
        self.save();
      });
      this.cancelButton.click(function() {
        self.cancel();
      });
    };
  
    /**
     * InlineForm methods
     */
    $.extend($.InlineForm.prototype, {
      /**
       * Replace HTML with form input fields
       */
      edit: function() {
        var self = this;
      
        // a structure for JSON request
        var request = {
          listId: this.root.data('list_id'),
          tabList: []
        };
      
        this.tabs.each(function() {
          self.originalTabs[$(this).data('tabTitle')] = $(this).html();
        });
        
        show_page_splash(1);
        
        // perform a request to the server
        $.ajax({
          type: 'POST',
          url: DX_CORE.site_url + 'inlineform/' + this.root.data('item_id') + '/edit',
          dataType: 'json',
          data: request,
          success: function(data)
          {
            self.editButton.hide();
            self.saveButton.show();
            self.cancelButton.show();
            
            var tabs = $($.parseHTML('<div>' + data.tabs + '</div>')).find('.tab-pane');
          
            // replace original html content of marked elements with input fields
            for(var i = 0; i < tabs.length; i++)
            {
              var tab = $(tabs[i]);
              var elem = $('[data-tab-title="' + tab.data('tabTitle') + '"]', self.root);
              if(elem.length)
                elem.html(tab.html());
            }
            
            hide_page_splash(1);
          },
          error: function(jqXHR, textStatus, errorThrown)
          {
            console.log(textStatus);
            console.log(jqXHR);
            hide_page_splash(1);
          }
        });
      },
    
      /**
       * Submit input field values to the server
       */
      save: function() {
        var self = this;
  
        var formData = process_data_fields(this.root.attr('id'));
        
        formData.append('item_id', this.root.data('item_id'));
        formData.append('list_id', this.root.data('list_id'));
        formData.append('edit_form_id', this.root.data('form_id'));
        
        show_page_splash(1);
        // submit a request
        $.ajax({
          type: 'POST',
          url: DX_CORE.site_url + 'inlineform/' + this.root.data('item_id') + '?_method=PUT',
          dataType: 'json',
          processData: false,
          contentType: false,
          data: formData,
          success: function(data)
          {
            self.editButton.show();
            self.saveButton.hide();
            self.cancelButton.hide();
  
            var tabs = $($.parseHTML('<div>' + data.tabs + '</div>')).find('.tab-pane');
  
            // replace original html content of marked elements with input fields
            for(var i = 0; i < tabs.length; i++)
            {
              var tab = $(tabs[i]);
              var elem = $('[data-tab-title="' + tab.data('tabTitle') + '"]', self.root);
              if(elem.length)
                elem.html(tab.html());
            }
            hide_page_splash(1);
          },
          error: function(jqXHR, textStatus, errorThrown)
          {
            console.log(textStatus);
            console.log(jqXHR);
            hide_page_splash(1);
          }
        });
      },
    
      /**
       * Remove input fields and display original HTML
       */
      cancel: function() {
        this.editButton.show();
        this.saveButton.hide();
        this.cancelButton.hide();
        for(var k in this.originalTabs)
        {
          this.tabs.filter('[data-tab-title="' + k + '"]').html(this.originalTabs[k]);
        }
      }
    });
    $(document).ready(function() {
      $('.freeform').FreeForm();
      $('.freeform').InlineForm();
    });
  </script>
@endsection

@section('main_content')
<div id="form_{{ Webpatser\Uuid\Uuid::generate(4) }}"
  class="portlet light dx-employee-profile freeform"
  data-freeform="true"
  data-model="App\User"
  data-form_id="{{ $form->params->form_id }}"
  data-item_id="{{ $employee->id }}"
  data-list_id="{{ Config::get('dx.employee_list_id') }}">
  <div class="portlet-body">
      <div class="row">
          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
               <div class="employee-panel">
                    <div class="well">
                      <div class="row">
                        <div class="col-sm-12 col-md-12 employee-pic-box" style="text-align: center;">                            
                            <img src="{{ $employee->getAvatar() }}" class="img-responsive img-thumbnail" style="max-height: 178px;">
                            <h4><span>{{ $employee->first_name }}</span> <span>{{ $employee->last_name }}</span></h4>
                            <span><a href="#" class="dx_position_link"> {{ $employee->position_title }}</a></span><br>
                            @if ($employee->department)
                                <span><a href="#" class="small dx_department_link">{{ $employee->department->title }}</a></span><br><br>
                            @endif
                            <a href="javascript:;" class="btn btn-default {{ $avail['class'] }}" title="{{ $avail['title'] }}" style="font-size: 10px; margin-top: 10px;"> {{ $avail['button'] }} </a>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                          <div class="employee-details-1">
                            <hr>                            
                            <div class="text-left">
                                <div class="dx-contact-info"><i class="fa fa-envelope-o"></i> <a href="mailto:{{ $employee->email }}">{{ $employee->email }}</a></div>
                                <div class="dx-contact-info"><i class="fa fa-phone"></i> {{ $employee->phone }}</div>
                                <div class="dx-contact-info"><i class="fa fa-map-marker"></i> {{ $employee->location_city }}, {{ $employee->country ? $employee->country->title : 'N/A' }}</div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                </div>
                
                @if($is_edit_rights)
                    <div class="tiles">
                        @include('profile.tile_hired')
                        @include('profile.tile_manager')                 
                    </div>
                @endif
                
          </div>
          <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12"> 
                <div class="actions pull-right">
                    @if($is_edit_rights)
                      <a href="javascript:;" class="btn btn-circle btn-default dx-edit-profile">
                        <i class="fa fa-pencil"></i> Edit </a>
                      <a href="javascript:;" class="btn btn-circle btn-default dx-save-profile" style="display: none">
                        <i class="fa fa-floppy-o"></i> Save </a>
                      <a href="javascript:;" class="btn btn-circle btn-default dx-cancel-profile" style="display: none">
                        <i class="fa fa-times"></i> Cancel </a>
                    @endif
                </div>
                <div class="tabbable-line">
                  <ul class="nav nav-tabs">
                    @if($is_edit_rights)
                      {!! $form->renderTabButtons() !!}
                    @endif
                    @section('profile_tabs')
                    @show
                  </ul>
                </div>
                <div class="tab-content" style="padding-top: 20px;">
                  @if($is_edit_rights)
                    {!! $form->renderTabContents() !!}
                  @endif
                  @section('profile_tabs_content')
                  @show
                </div>
          </div>
      </div>
   

  
  </div>
</div>
@endsection