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
    .profile-sticky {
      padding: 10px 0;
      border-top: 1px solid #ddd;
      z-index: 10;
      background-color: white;
    }
    .profile-sticky a:first-child {
      margin-left: 20px;
    }
    
    .stuck {
      position: fixed;
      bottom: 0;
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
    $.fn.Sticky = function(opts)
    {
      var options = $.extend({}, $.Sticky.defaults, opts);
      return this.each(function(){
        new $.Sticky(this, options);
      });
    };

    $.Sticky = function(root, opts)
    {
      $.data(root, 'Sticky', this);
      var self = this;
      this.options = $.extend({}, $.Sticky.defaults, opts);
      this.root = $(root);
      this.placeholder = this.root.after('<div/>').next();
      
      this.root.width(this.root.parent().width());
    
      this.placeholder.css({
        position: this.root.css('position'),
        'float': this.root.css('float'),
        display: 'none'
      });
    
      this.init();
      this.update();

      $(window).resize(function()
      {
        self.init();
        self.update();
      });
    
      $(window).scroll(function()
      {
        self.init();
        self.update();
      });
    };
  
    $.Sticky.defaults = {
      side: 'top'
    };
  
    $.extend($.Sticky.prototype, {
      init: function()
      {
        this.placeholder.css({
            width: this.root.outerWidth(),
            height: this.root.outerHeight()
        });
        
        if(this.options.side == 'top')
        {
          if(this.root.hasClass('stuck'))
            this.top = this.placeholder.offset().top;

          else
            this.top = this.root.offset().top;
        }
        else
        {
          if(this.root.hasClass('stuck'))
            this.top = this.placeholder.offset().top - this.placeholder.height();

          else
            this.top = this.root.offset().top;
        }
        
        this.height = this.root.outerHeight();
      },
    
      stick: function()
      {
        this.root.addClass('stuck');
        this.placeholder.css('display', this.root.css('display'));
      },
    
      unstick: function()
      {
        this.root.removeClass('stuck');
        this.placeholder.css('display', 'none');
      },
      
      update: function()
      {
        var self = this;
        if(self.options.side == 'top')
        {
          if($(window).scrollTop() > self.top)
            self.stick();
  
          else
            self.unstick();
        }
        else
        {
          if($(window).scrollTop() + $(window).height() < self.top + self.height)
            self.stick();
  
          else
            self.unstick();
        }
      }
    });
    
    /**
     * InlineForm - a jQuery plugin that provides a way to work with AJAX form embedded into a page
     *
     * @param root
     * @returns {*}
     * @constructor
    */
    $.fn.InlineForm = function(opts)
    {
      var options = $.extend({}, $.fn.InlineForm.defaults, opts);
      return this.each(function(){
        new $.InlineForm(this, options);
      });
    };

    $.fn.InlineForm.defaults = {
      beforeSave: null,
      afterSave: null
    };

    /**
     * InlineForm constructor
     *
     * @param root
     * @constructor
    */
    $.InlineForm = function(root, opts)
    {
      $.data(root, 'InlineForm', this);
      var self = this;
      this.options = opts;
      this.root = $(root);
      this.tabs = $('.tab-pane', this.root);
      this.originalTabs = {};
      this.editButton = $('.dx-edit-profile', this.root);
      this.saveButton = $('.dx-save-profile', this.root);
      this.cancelButton = $('.dx-cancel-profile', this.root);
      this.deleteButton = $('.dx-delete-profile', this.root);
      this.stickyPanel = $('.profile-sticky', this.root);
    
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
      this.deleteButton.click(function() {
        self.delete();
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
            self.stickyPanel.show(function(){
              self.stickyPanel.data('Sticky').init();
              self.stickyPanel.data('Sticky').update();
            });
            
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
        formData.append('redirect_url', this.root.data('redirect_url'));
        
        show_page_splash(1);
        
        var url = DX_CORE.site_url + 'inlineform';
        
        if(this.root.data('mode') != 'create')
        {
          url += '/' + this.root.data('item_id') + '?_method=PUT';
        }
        
        // submit a request
        $.ajax({
          type: 'POST',
          url: url,
          dataType: 'json',
          processData: false,
          contentType: false,
          data: formData,
          success: function(data)
          {
            if(self.root.data('mode') == 'create')
            {
              window.location = data.redirect;
              return;
            }
            
            self.editButton.show();
            self.stickyPanel.hide();
  
            var tabs = $($.parseHTML('<div>' + data.tabs + '</div>')).find('.tab-pane');
  
            // replace original html content of marked elements with input fields
            for(var i = 0; i < tabs.length; i++)
            {
              var tab = $(tabs[i]);
              var elem = $('[data-tab-title="' + tab.data('tabTitle') + '"]', self.root);
              if(elem.length)
                elem.html(tab.html());
            }
            
            console.log(self.options);
            
            if(self.options.afterSave)
            {
              self.options.afterSave();
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
        this.stickyPanel.hide();
        for(var k in this.originalTabs)
        {
          this.tabs.filter('[data-tab-title="' + k + '"]').html(this.originalTabs[k]);
        }
      }
    });
    $(document).ready(function() {
      $('.freeform').FreeForm({
        names: ['description']
      });
      $('.freeform').InlineForm({
        afterSave: function(){
          $.ajax({
            type: 'GET',
            url: DX_CORE.site_url + 'employee/profile/' + $('.dx-employee-profile').data('item_id') + '/chunks',
            dataType: 'json',
            success: function(data)
            {
              $('.employee-panel').html(data.panel);
              $('.employee-manager').html(data.manager);
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
              console.log(textStatus);
              console.log(jqXHR);
            }
          });
        }
      });
      $('.profile-sticky').Sticky({ side: 'bottom' });
      $('.dx-tab-link').click(function(){
        $('.profile-sticky').data('Sticky').init();
        $('.profile-sticky').data('Sticky').update();
      });
    });
  </script>
@endsection

@section('main_content')
<div id="form_{{ Webpatser\Uuid\Uuid::generate(4) }}"
  class="portlet light dx-employee-profile freeform"
  data-freeform="true"
  data-model="App\User"
  data-mode="{{ $mode }}"
  data-redirect_url="/employee/profile/"
  data-form_id="{{ $form->params->form_id }}"
  data-item_id="{{ $mode == 'create' ? 0 : $employee->id }}"
  data-list_id="{{ Config::get('dx.employee_list_id') }}">
  <div class="portlet-body">
    <div class="row">
      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
        @include('profile.panel')
        @if($mode != 'create' && $is_edit_rights)
          <div class="tiles">
            @include('profile.tile_hired')
            @include('profile.tile_manager')
          </div>
        @endif
      </div>
      <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
        <div class="actions pull-right">
          @if($is_edit_rights && $mode != 'create')
            <a href="javascript:;" class="btn btn-circle btn-default dx-edit-profile">
              <i class="fa fa-pencil"></i> Edit </a>
            <a href="javascript:;" class="btn btn-circle btn-default dx-delete-profile">
              <i class="fa fa-pencil"></i> Delete </a>
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
        <div class="profile-sticky" style="{{ $mode == 'create' ? '' : 'display: none' }}">
          <a href="javascript:;" class="btn btn-circle btn-default dx-save-profile">
            <i class="fa fa-floppy-o"></i> Save </a>
          <a href="javascript:;" class="btn btn-circle btn-default dx-cancel-profile">
            <i class="fa fa-times"></i> Cancel </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
