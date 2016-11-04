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
  <script src="{{ elixir('js/elix_profile.js') }}" type='text/javascript'></script>
  <script>
    $(document).ready(function()
    {
      $('.freeform').FreeForm();
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
      $('.profile-sticky').Sticky({side: 'bottom'});
      $('.dx-tab-link').click(function()
      {
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
