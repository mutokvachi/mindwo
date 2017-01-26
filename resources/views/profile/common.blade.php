@extends('frame')

@section('main_custom_css')
  <link href="{{Request::root()}}/plugins/tree/themes/default/style.min.css" rel="stylesheet"/>
  <link href="{{Request::root()}}/metronic/global/plugins/bootstrap-colorpicker/css/colorpicker.css" rel="stylesheet" type="text/css"/>
  <link href="{{Request::root()}}/plugins/select2/select2.css" rel="stylesheet"/>
  <link href="{{ elixir('css/elix_view.css') }}" rel="stylesheet"/>
  <link href="{{ elixir('css/elix_employee_profile.css') }}" rel="stylesheet"/>
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
    
    .dx-employee-profile.is-admin .tiles .tile.double {
      width: auto !important;
      float: none;
    }
    
    .dx-contact-info {
      margin-bottom: 8px;
      text-overflow: ellipsis;
      overflow: hidden;
    }
    
    .employee-details-1 {
      margin-top: 10px;
    }
    
    .dx-top-right-menu {
      margin-right: 0px !important;
    }
    
    .container-fluid {
      padding-right: 0px;
      padding-left: 0px;
    }
    
    .page-content {
      background-color: white !important;
    }
    
    .dx-employee-profile {
      border: none;
      box-shadow: none !important;
    }
    
    @media screen and (max-width: 767px) {
      .dx-page-container {
        padding: 20px 0 0 0 !important;
      }
    }
    
    .dx-employee-manager a:hover {
        text-decoration: none;
    }
  </style>
@endsection

@section('main_custom_javascripts')
  <script src='{{Request::root()}}/{{ getIncludeVersion('plugins/tinymce/tinymce.min.js') }}' type='text/javascript'></script>
  <script src="{{ elixir('js/elix_view.js') }}" type='text/javascript'></script>
  <script src="{{ elixir('js/elix_profile.js') }}" type='text/javascript'></script>
  <script>
    (function($)
    {
      $.fn.EmployeeProfile = function(opts)
      {
        var options = $.extend({}, $.fn.EmployeeProfile.defaults, opts);
        
        return this.each(function()
        {
          new $.EmployeeProfile(this, options);
        });
      };
      
      $.fn.EmployeeProfile.defaults = {};
      
      /**
       * Constructor
       *
       * @param root
       * @constructor
      */
      $.EmployeeProfile = function(root, opts)
      {
        $.data(root, 'EmployeeProfile', this);
        var self = this;
        this.options = opts;
        this.root = $(root);
        this.dynamicTabButtons = $('.nav-tabs a[data-dynamic="true"]', this.root);
        this.tabContent = $('.tab-content', this.root);
        this.loadedTabs = [];
        
        this.requests = {};
        this.onRequestSuccess = [];
        this.onRequestFailed = [];
        
        this.dynamicTabButtons.each(function()
        {
          $(this).click(function(event)
          {
            self.loadTab($(this), event);
          })
        });
      };
      
      $.extend($.EmployeeProfile.prototype, {
        loadTab: function(button, event)
        {
          var self = this;
          var id = button.attr('href').substring(1);
          var request = {
            tab_id: id
          };
          
          if(this.loadedTabs.indexOf(id) != -1)
            return;
          
          event.preventDefault();
  
          show_page_splash(1);
  
          $.ajax({
            type: 'GET',
            url: DX_CORE.site_url + 'employee/profile/' + this.root.data('item_id') + '/tabs',
            dataType: 'json',
            data: request,
            success: function(data)
            {
              if(typeof data.success != "undefined" && data.success == 0)
              {
                notify_err(data.error);
                hide_page_splash(1);
                return;
              }
              
              self.loadedTabs.push(id);
              self.tabContent.children('[id="' + id + '"]').append($(data.html).html());
              
              // call Bootstrap's function to show the tab
              button.tab('show');
              
              hide_page_splash(1);
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
              console.log(textStatus);
              console.log(jqXHR);
              hide_page_splash(1);
            }
          });
        }
      });
    })(jQuery);
    
    $(document).ready(function()
    {
      $(window).on('beforeunload', function()
      {
        if($(".dx-stick-footer").is(":visible"))
        {
          hide_page_splash(1);
          hide_form_splash(1);
          return 'Your changes have not been saved.';
        }
      });
      
      show_page_splash(1);
      
      $('.dx-employee-profile').EmployeeProfile();
      
      $('.freeform').FreeForm({
        names: ['description']
      });
      $('.freeform').InlineForm({
        afterSave: function()
        {
          $.ajax({
            type: 'GET',
            url: DX_CORE.site_url + 'employee/profile/' + $('.dx-employee-profile').data('item_id') + '/chunks',
            dataType: 'json',
            success: function(data)
            {
              if(typeof data.success != "undefined" && data.success == 0)
              {
                notify_err(data.error);
                return;
              }
              
              // update auxiliary info panels
              for(var selector in data.chunks)
              {
                $(selector).first().html(data.chunks[selector]);
              }
              
              var img_src = DX_CORE.site_url + "assets/global/avatars/default_avatar_big.jpg";
              
              if($('.dx-employee-panel .fileinput-preview img').length)
              {
                img_src = $('.dx-employee-panel .fileinput-preview img').attr("src");
              }
              $('.dx-employee-profile .dx-stick-footer .dx-left img').attr('src', img_src);
              $('.dx-employee-profile .dx-stick-footer .dx-left span.dx-empl-title').html($('.dx-employee-profile .employee-pic-box h4.dx-empl-title').html());
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
              console.log(textStatus);
              console.log(jqXHR);
              console.log(errorThrown);
            }
          });
        }
      });
      
      if($('.freeform').data('has_users_documents_access') == 1 && $('.freeform').data('is_edit_rights') == 1)
      {
        window.DxEmpPersDocs.init(function()
        {
          hide_page_splash(1);
        });
      }
      else
      {
        hide_page_splash(1);
      }
      
      var user_id = $('.freeform').data('item_id');
      if($('.freeform').data('has_users_notes_access') == 1 && user_id > 0){          
          window.DxEmpNotes.init(user_id);
          
          $('#dx-tab_notes-btn').click(window.DxEmpNotes.loadView);
      }
      
      if($('.freeform').data('has_users_timeoff_access') == 1 && user_id > 0){   
        window.DxEmpTimeoff.init(user_id);          
        $('#dx-tab_timeoff-btn').click(window.DxEmpTimeoff.loadView);
      }
      
      // set tabs links for sub-grids
      $('.dx-employee-profile a.dx-tab-link').click(function (e) {
        if ($('#' + this.getAttribute('tab_id')).html().trim().length == 0)
        {
            if ('{{ $mode }}' == 'create') {
                e.preventDefault();
                e.stopPropagation();
                notify_err(Lang.get('empl_profile.err_first_save_new_item'));
                return;
            }
            load_tab_grid(this.getAttribute('tab_id') ,this.getAttribute('grid_list_id'), 0, this.getAttribute('grid_list_field_id'), {{ $mode == 'create' ? 0 : $employee->id }}, $(".dx-employee-profile").attr("id"), 1, 5, 1);
        }
      });
    });
  </script>
@endsection

@section('main_content')
  <div id="form_{{ Webpatser\Uuid\Uuid::generate(4) }}"
    class="portlet light dx-employee-profile freeform {{ $is_edit_rights ? 'is-admin' : '' }}" style='padding-bottom: 100px!important;'
    data-freeform="true"
    data-model="App\User"
    data-mode="{{ $mode }}"
    data-redirect_url="/employee/profile/"
    data-form_id="{{ $form->params->form_id }}"
    data-item_id="{{ $mode == 'create' ? 0 : $employee->id }}"
    data-list_id="{{ Config::get('dx.employee_list_id') }}"
    data-is_edit_rights='{{ $is_edit_rights }}'
    data-has_users_documents_access='{{ (isset($has_users_documents_access) && $has_users_documents_access) ? 1 : 0}}'
    data-has_users_notes_access='{{ (isset($has_users_notes_access) && $has_users_notes_access) ? 1 : 0}}'
    data-has_users_timeoff_access='{{ (isset($has_users_timeoff_access) && $has_users_timeoff_access) ? 1 : 0}}'>
    <div class="portlet-body">
      <div class="row">
        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
          @include('profile.panel')
          @if($mode != 'create' && $is_edit_rights)
            <div class="tiles">
              @include('profile.tile_leave')
              @include('profile.tile_hired')
              @include('profile.tile_manager')
            </div>
          @endif
        </div>
        <div class="col-lg-10 col-md-9 col-sm-9 col-xs-12">
          <div class="actions pull-right">
            @if($is_edit_rights && $mode != 'create')
              <a href="javascript:;" class="btn btn-circle btn-default dx-edit-profile">
                <i class="fa fa-pencil"></i> {{ trans('form.btn_edit') }} </a>
              <a href="javascript:;" class="btn btn-circle btn-default dx-delete-profile">
                <i class="fa fa-trash-o"></i> {{ trans('form.btn_delete') }} </a>
            @endif
          </div>
          <div class="tabbable-line tabbable-tabdrop">
            <ul class="nav nav-tabs">
                @if($is_edit_rights)
                  {!! $form->renderTabButtons() !!}
                @endif
                
                @section('profile_tabs')
                @show

                @if($is_edit_rights)
                  <li class="dropdown dx-sub-tab">
                      <a href="javascript:;" id="myTabDrop1" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> {{ trans('empl_profile.qualif_menu') }}
                          <i class="fa fa-angle-down"></i>
                      </a>                    
                      <ul class="dropdown-menu dx-sub-menu-left" role="menu" aria-labelledby="myTabDrop1">
                          {!! $form->renderSubgridTabButtons("_qualification", [trans('empl_profile.tab_lang'), trans('empl_profile.tab_links'), trans('empl_profile.tab_educ'), trans('empl_profile.tab_cert'), trans('empl_profile.tab_cv')], 1) !!}                        
                      </ul>
                  </li>
                  
                  <li class="dropdown dx-sub-tab">
                      <a href="javascript:;" id="myTabDrop1" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> {{ trans('empl_profile.assets_menu') }}
                          <i class="fa fa-angle-down"></i>
                      </a>                    
                      <ul class="dropdown-menu dx-sub-menu-left" role="menu" aria-labelledby="myTabDrop1">
                          {!! $form->renderSubgridTabButtons("_assets", [trans('empl_profile.tab_cards'), trans('empl_profile.tab_devices')], 1) !!}                        
                      </ul>
                  </li>
                @endif
            </ul>
          </div>
          <div class="tab-content" style="padding-top: 20px;">
            @if($is_edit_rights)
              {!! $form->renderTabContents() !!}                
            @endif
            
            @section('profile_tabs_content')
            @show
            
            @if($is_edit_rights)
                {!! $form->renderSubgridTabContents("_qualification", [trans('empl_profile.tab_lang'), trans('empl_profile.tab_links'), trans('empl_profile.tab_educ'), trans('empl_profile.tab_cert'), trans('empl_profile.tab_cv')], 1) !!}
                {!! $form->renderSubgridTabContents("_assets", [trans('empl_profile.tab_cards'), trans('empl_profile.tab_devices')], 1) !!}
            @endif
          </div>
        </div>
      </div>
    </div>
    <div class="dx-stick-footer animated bounceInUp" style="{{ $mode == 'create' ? '' : 'display: none' }}">
      <div class='row'>
        <div class='col-lg-2 col-md-3 hidden-sm hidden-xs dx-left'>
          <img src="{{ $employee->getAvatar() }}" class="img-responsive img-thumbnail" style="max-height: 60px;">
          <span class='dx-empl-title'>{{ $employee->first_name }} {{ $employee->last_name }}</span>
        </div>
        <div class='col-lg-10 col-md-9 col-sm-12 col-xs-12 dx-right'>
          <a href="javascript:;" class="btn btn-primary dx-save-profile">
            <i class="fa fa-floppy-o"></i> {{ trans('form.btn_save') }} </a>
          <a href="javascript:;" class="btn btn-default dx-cancel-profile">
            <i class="fa fa-times"></i> {{ trans('form.btn_cancel') }} </a>
        </div>
      </div>
    </div>
  </div>
@endsection
