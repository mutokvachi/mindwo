@extends('profile.common')

@section('profile_tabs')
  @if(!$is_edit_rights)
    <li class="active">
      <a href="#tab_info" data-toggle="tab" aria-expanded="true"> {{ trans('empl_profile.tab_info') }} </a>
    </li>    
  @else
    @if(isset($has_users_documents_access) && $has_users_documents_access)
      <li class="">
        <a href="#tab_personal_docs" data-toggle="tab" aria-expanded="false"> {{ trans('empl_profile.tab_documents') }} </a>
      </li>
    @endif
  @endif
  @if(isset($has_users_notes_access) && $has_users_notes_access)
    <li class="">
        <a id='dx-tab_notes-btn' href="#dx-tab_notes" data-toggle="tab" aria-expanded="false"> {{ trans('empl_profile.tab_notes') }} </a>
    </li>
  @endif
  @if(isset($has_users_timeoff_access) && $has_users_timeoff_access)
    <li class="">
        <a id='dx-tab_timeoff-btn' href="#dx-tab_timeoff" data-toggle="tab" aria-expanded="false"> {{ trans('empl_profile.tab_timeoff') }} </a>
    </li>
  @endif
@endsection

@section('profile_tabs_content')
  @if(!$is_edit_rights)
    <div class="tab-pane fade active in" id="tab_info">
      <!-- Info -->
      <div class="actions">
        <h3>{{ trans('empl_profile.lbl_about') }}
          @if($is_my_profile)
            <a href="javascript:;" class="btn btn-circle btn-default dx-edit-general">
              <i class="fa fa-pencil"></i> {{ trans('empl_profile.lbl_edit') }} </a>
            <a href="javascript:;" class="btn btn-circle btn-default dx-save-general" style="display: none">
              <i class="fa fa-floppy-o"></i> {{ trans('empl_profile.lbl_save') }} </a>
            <a href="javascript:;" class="btn btn-circle btn-default dx-cancel-general" style="display: none">
              <i class="fa fa-times"></i> {{ trans('empl_profile.lbl_cancel') }} </a>
          @endif
        </h3>
      </div>
      
      <p data-name="description" data-type="text">{{ $employee->description }}</p>
      
      <div class="tiles">
        @include('profile.tile_leave')
        @include('profile.tile_hired')
        @include('profile.tile_manager')
      </div>
      @include('profile.tile_direct_reporters')
      @include('profile.tab_team')
    </div>
    
  @else
    @if(isset($has_users_documents_access) && $has_users_documents_access)
      <div class="tab-pane fade" id="tab_personal_docs">
        @include('profile.personal_docs', ['user' => $employee])
      </div>
    @endif
  @endif
  @if(isset($has_users_notes_access) && $has_users_notes_access)
    <div class="tab-pane fade" id="dx-tab_notes">
    </div>
  @endif
  @if(isset($has_users_timeoff_access) && $has_users_timeoff_access)
  <div class="tab-pane fade" id="dx-tab_timeoff">
  </div>
  @endif
@endsection