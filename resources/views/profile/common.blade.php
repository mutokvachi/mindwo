@extends('frame')

@section('main_custom_css')
  <link href="{{Request::root()}}/plugins/tree/themes/default/style.min.css" rel="stylesheet"/>
  <link href="{{Request::root()}}/metronic/global/plugins/bootstrap-colorpicker/css/colorpicker.css" rel="stylesheet" type="text/css"/>
  <link href="{{Request::root()}}/plugins/select2/select2.css" rel="stylesheet"/>
  <link href="{{ elixir('css/elix_view.css') }}" rel="stylesheet"/>
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
    
    .dx-page-container {
      padding: 0px !important;
    }
    
    .page-content {
      background-color: white !important;
    }
    
    .dx-employee-profile {
      border: none;
      box-shadow: none !important;
    }
  </style>
@endsection

@section('main_custom_javascripts')
  <script src='{{Request::root()}}/{{ getIncludeVersion('plugins/tinymce/tinymce.min.js') }}' type='text/javascript'></script>
  <script src="{{ elixir('js/elix_view.js') }}" type='text/javascript'></script>
  <script src="{{ elixir('js/elix_profile.js') }}" type='text/javascript'></script>
  <script>
    $(document).ready(function()
    {
      $(window).on('beforeunload', function()
      {
        if($(".dx-stick-footer").is(":visible"))
        {
          return 'Your changes have not been saved.';
        }
      });
      
      show_page_splash(1);
      
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
              
              $('.dx-employee-profile .dx-stick-footer .dx-left img').attr('src', $('.dx-employee-profile .employee-pic-box img').attr("src"));
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
      
      if($('.freeform').data('has_users_documents_access') == 1)
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
    data-has_users_documents_access='{{ $has_users_documents_access ? 1 : 0}}'>
    <div class="portlet-body">
      <div class="row">
        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
          @include('profile.panel')
          @if($mode != 'create' && $is_edit_rights)
            <div class="tiles">
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
