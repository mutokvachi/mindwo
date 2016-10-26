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
  <script>
    /**
     * FreeForm - a jQuery plugin for working with arbitrary forms
     *
     * @param root
     * @returns {*}
     * @constructor
    */
    $.fn.FreeForm = function(root)
    {
      return this.each(function(){
        new $.FreeForm(this);
      });
    };

    /**
     * FreeForm constructor
     *
     * @param root
     * @constructor
    */
    $.FreeForm = function(root)
    {
      $.data(root, 'FreeForm', this);
      var self = this;
      this.root = $(root);
      this.fields = $('[data-name]', this.root);
      this.originalData = {};
      this.editButton = $('.dx-edit-general', this.root);
      this.saveButton = $('.dx-save-general', this.root);
      this.cancelButton = $('.dx-cancel-general', this.root);
      
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
     * FreeForm methods
     */
    $.extend($.FreeForm.prototype, {
      /**
       * Replace HTML with form input fields
       */
      edit: function() {
        var self = this;
        
        // a structure for JSON request
        var request = {
          model: this.root.data('model'),
          item_id: this.root.data('item_id'),
          list_id: this.root.data('list_id'),
          fields: []
        };
        
        // collect names of input fields marked with data-name attribute
        this.fields.each(function() {
          self.originalData[$(this).data('name')] = $(this).html();
          request.fields.push({
            name: $(this).data('name')
          });
        });
  
        // perform a request to the server
        $.ajax({
          type: 'POST',
          url: DX_CORE.site_url + 'freeform/' + request.item_id + '/edit',
          dataType: 'json',
          data: request,
          success: function(data)
          {
            self.editButton.hide();
            self.saveButton.show();
            self.cancelButton.show();
            
            // replace original html content of marked elements with input fields
            for(var i = 0; i < data.fields.length; i++)
            {
              var name = data.fields[i].name;
              var input = data.fields[i].input;
              var elem = $('[data-name="' + name + '"]', self.root);
              if(elem.length)
                elem.html(input);
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
        
        // JSON structure
        var request = {
          model: this.root.data('model'),
          item_id: this.root.data('item_id'),
          list_id: this.root.data('list_id'),
          fields: []
        };
        
        // collect values of input fields
        this.fields.each(function() {
          request.fields.push({
            name: $(this).data('name'),
            data: $(this).find('[name]').val()
          });
        });
        
        // submit a request
        $.ajax({
          type: 'POST',
          url: DX_CORE.site_url + 'freeform/' + request.item_id + '?_method=PUT',
          dataType: 'json',
          data: request,
          success: function(data)
          {
            self.editButton.show();
            self.saveButton.hide();
            self.cancelButton.hide();
            
            // replace input fields with html data from server response
            for(var i = 0; i < data.fields.length; i++)
            {
              var name = data.fields[i].name;
              var html = data.fields[i].html;
              var elem = $('[data-name="' + name + '"]', self.root);
              if(elem.length)
                elem.html(html);
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
        var self = this;
        this.editButton.show();
        this.saveButton.hide();
        this.cancelButton.hide();
        this.fields.each(function() {
          $(this).html(self.originalData[$(this).data('name')]);
        });
      }
    });
    
    $(document).ready(function() {
      $('[data-freeform]').FreeForm();
    });
  </script>
@endsection

@section('main_content')
<div class="portlet light dx-employee-profile freeform" data-freeform="true" data-model="App\User" data-item_id="{{ $employee->id }}" data-list_id="{{ Config::get('dx.employee_list_id') }}">
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
              <h4><span class="inline" data-name="first_name">{{ $employee->first_name }}</span> <span class="inline" data-name="last_name">{{ $employee->last_name }}</span></h4>
              <span><a href="#" class="dx_position_link"> {{ $employee->position_title }}</a></span><br>
              <span><a href="#" class="small dx_department_link">{{ $employee->department->title }}</a></span><br><br>
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
    <div class="tabbable-line">
      <ul class="nav nav-tabs">
        @section('profile_tabs')
        @show
      </ul>
    </div>
    <div class="tab-content">
      @section('profile_tabs_content')
      @show
    </div>
  
  </div>
</div>
@endsection