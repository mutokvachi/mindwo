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
          },
          error: function(jqXHR, textStatus, errorThrown)
          {
            console.log(textStatus);
            console.log(jqXHR);
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
          },
          error: function(jqXHR, textStatus, errorThrown)
          {
            console.log(textStatus);
            console.log(jqXHR);
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
  <div class="portlet-title">
    <div class="caption">
      <i class="fa fa-user"></i>
      <span class="caption-subject bold uppercase">{{ $is_my_profile ? 'My profile' : 'Employee profile' }}</span>
      <span class="caption-helper">{{ $is_my_profile ? "" : "profile" }}</span>
    </div>
    <div class="actions">
      @if($employee->id == Auth::user()->id || Auth::user()->id == 1)
        <a href="javascript:;" class="btn btn-circle btn-default dx-edit-general">
          <i class="fa fa-pencil"></i> Edit </a>
        <a href="javascript:;" class="btn btn-circle btn-default dx-save-general" style="display: none">
          <i class="fa fa-floppy-o"></i> Save </a>
        <a href="javascript:;" class="btn btn-circle btn-default dx-cancel-general" style="display: none">
          <i class="fa fa-times"></i> Cancel </a>
      @endif
    </div>
  </div>
  <div class="portlet-body">
    <div class="employee-panel">
      <div class="well">
        <div class="row">
          <div class="hidden-xs col-sm-3 col-md-3 employee-pic-box">
            <img src="{{ $employee->getAvatar() }}" class="img-responsive img-thumbnail" style="max-height: 178px;">
          </div>
          <div class="col-xs-12 col-sm-9 col-md-9">
            <div class="employee-details-1">
              <a href="javascript:;" class="btn btn-circle btn-default {{ $avail['class'] }} pull-right" title="{{ $avail['title'] }}"> {{ $avail['button'] }} </a>
              <h4><span>{{ $employee->first_name }}</span> <span>{{ $employee->last_name }}</span></h4>
              <span><a href="#" class="dx_position_link"> {{ $employee->position_title }}</a></span><br>
              @if ($employee->department)
              <span><a href="#" class="small dx_department_link">{{ $employee->department->title }}</a></span><br><br>
              @endif
              <div class="text-left">
                <span><a href="mailto:{{ $employee->email }}">{{ $employee->email }}</a></span><br>
                <span>{{ $employee->phone }}</span><br><br>
                <p style="margin-bottom: 0px;">
                  @if($employee->country && $employee->country->flag_file_guid)
                    <img src="{{ $employee->country->getFlag() }}" title="{{ $employee->country->flag_file_name }}" style="margin-top: 1px;"/>
                  @else
                    <img src="/assets/global/flags/en.png" title="English" style="margin-top: 1px;"/>
                  @endif
                  <span title="Employee location" class="pull-right"><i class="fa fa-map-marker"></i> <span>{{ $employee->location_city }}</span>, <span>{{ $employee->country ? $employee->country->title : 'N/A' }}</span></span>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <h3>About</h3>
    <p data-name="description" data-type="text">{{ $employee->description }}</p>
    
    <h3>Stats</h3>
    <div class="tiles" style="margin-bottom: 20px">
      <div class="tile bg-blue-hoki double">
        <div class="tile-body">
          <i class="fa fa-briefcase"></i>
        </div>
        <div class="tile-object">
          <div class="name"> Hired</div>
          <div class="number"> {{ strftime('%x', strtotime($employee->join_date)) }} </div>
        </div>
      </div>
      <div class="tile double bg-blue-madison">
        <div class="tile-body">
          @if($employee->manager)
            <img src="/assets/global/tiles/manager.jpg" alt="">
            <h4>{{ $employee->manager->display_name }}</h4>
            <p> {{ $employee->manager->position_title }}<br/> {{ $employee->manager->department_title }}  </p>
          @endif
        </div>
        <div class="tile-object">
          <div class="name"> Direct supervisor</div>
          <div class="number"></div>
        </div>
      </div>
      @if($is_my_profile)
        <div class="tile image double selected">
          <div class="tile-body">
            <img src="/assets/global/tiles/vacation.jpg" alt=""></div>
          <div class="tile-object">
            <div class="name"> Available vacation days</div>
            <div class="number"> 14</div>
          </div>
        </div>
        <div class="tile bg-red-intense">
          <div class="tile-body">
            <i class="fa fa-calendar"></i>
          </div>
          <div class="tile-object">
            <div class="name"> Sick days</div>
            <div class="number"> 3</div>
          </div>
        </div>
        <div class="tile bg-yellow-saffron">
          <div class="tile-body">
            <i class="fa fa-gift"></i>
          </div>
          <div class="tile-object">
            <div class="name"> Bonuses</div>
            <div class="number"> 2</div>
          </div>
        </div>
      
      @endif
    </div>
    <div class="actions" style="text-align: right">
      @if(Auth::user()->id == 1)
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
        @if(!$is_my_profile && (Auth::user()->id == 1))
          {!! $form->renderTabButtons() !!}
        @endif
        @section('profile_tabs')
        @show
      </ul>
    </div>
    <div class="tab-content" style="padding-top: 20px;">
      @if(!$is_my_profile && (Auth::user()->id == 1))
        {!! $form->renderTabContents() !!}
      @endif
      @section('profile_tabs_content')
      @show
    </div>
  
  </div>
</div>
@endsection