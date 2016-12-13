@extends('profile.common')

@section('profile_tabs')
  @if(!$is_edit_rights)
    <li class="active">
      <a href="#tab_info" data-toggle="tab" aria-expanded="true"> Info </a>
    </li>
    
    @if($is_my_profile)
      <li class="">
        <a href="#tab_leaves" data-toggle="tab" aria-expanded="true" data-dynamic="true"> Leaves </a>
      </li>
      <li class="">
        <a href="#tab_bonuses" data-toggle="tab" aria-expanded="false" data-dynamic="true"> Bonuses </a>
      </li>
    @endif
    
    <li class="">
      <a href="#tab_team" data-toggle="tab" aria-expanded="false" data-dynamic="true"> Team </a>
    </li>
    <li class="">
      <a href="#tab_achievements" data-toggle="tab" aria-expanded="false" data-dynamic="true"> Achievements </a>
    </li>
    <li class="">
      <a href="#tab_skills" data-toggle="tab" aria-expanded="false" data-dynamic="true"> Skills </a>
    </li>
  @else
    @if(isset($has_users_documents_access) && $has_users_documents_access)
      <li class="">
        <a href="#tab_personal_docs" data-toggle="tab" aria-expanded="false"> Documents </a>
      </li>
    @endif
  @endif
  @if(isset($has_users_notes_access) && $has_users_notes_access)
    <li class="">
        <a id='dx-tab_notes-btn' href="#dx-tab_notes" data-toggle="tab" aria-expanded="false"> Notes </a>
    </li>
  @endif
  @if(isset($has_users_timeoff_access) && $has_users_timeoff_access)
    <li class="">
        <a id='dx-tab_timeoff-btn' href="#dx-tab_timeoff" data-toggle="tab" aria-expanded="false"> Time off </a>
    </li>
  @endif
@endsection

@section('profile_tabs_content')
  @if(!$is_edit_rights)
    <div class="tab-pane fade active in" id="tab_info">
      <!-- Info -->
      <div class="actions">
        <h3>About
          @if($is_my_profile)
            <a href="javascript:;" class="btn btn-circle btn-default dx-edit-general">
              <i class="fa fa-pencil"></i> Edit </a>
            <a href="javascript:;" class="btn btn-circle btn-default dx-save-general" style="display: none">
              <i class="fa fa-floppy-o"></i> Save </a>
            <a href="javascript:;" class="btn btn-circle btn-default dx-cancel-general" style="display: none">
              <i class="fa fa-times"></i> Cancel </a>
          @endif
        </h3>
      </div>
      
      <p data-name="description" data-type="text">{{ $employee->description }}</p>
      
      <div class="tiles">
        @include('profile.tile_hired')
        @include('profile.tile_manager')
        @if($is_my_profile)                   
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
    </div>
    
    @if ($is_my_profile)
        <div class="tab-pane fade" id="tab_leaves">
        </div>
        <div class="tab-pane fade" id="tab_bonuses">
        </div>
    @endif

    <div class="tab-pane fade" id="tab_team">
    </div>
    <div class="tab-pane fade" id="tab_achievements">
    </div>
    <div class="tab-pane fade" id="tab_skills">
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