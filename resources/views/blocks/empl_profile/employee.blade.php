@extends('blocks.empl_profile.common')

@section('profile_tabs')
  @if($is_my_profile)
    <li class="active">
      <a href="#tab_leaves" data-toggle="tab" aria-expanded="true"> Leaves </a>
    </li>
    <li class="">
      <a href="#tab_bonuses" data-toggle="tab" aria-expanded="false"> Bonuses </a>
    </li>
  @endif
  
  <li class="{{ $is_my_profile ? "" : "active" }}">
    <a href="#tab_team" data-toggle="tab" aria-expanded="false"> Team </a>
  </li>
  <li class="">
    <a href="#tab_achievements" data-toggle="tab" aria-expanded="false"> Achievements </a>
  </li>
  <li class="">
    <a href="#tab_skills" data-toggle="tab" aria-expanded="false"> Skills </a>
  </li>
@endsection

@section('profile_tabs_content')
  @if ($is_my_profile)
    <div class="tab-pane fade active in" id="tab_leaves">
      <a class="btn btn-primary pull-right btn-sm dx-employee-leave-add-btn" style="margin-bottom: 20px; margin-top: 10px;"><i class="fa fa-plus"></i>
        Add leave </a>
      @include('blocks.empl_profile.leaves')
    </div>
    <div class="tab-pane fade" id="tab_bonuses">
      @if ($is_empl_edit_rights && 1 == 2)
        <a class="btn btn-primary pull-right btn-sm dx-employee-bonus-add-btn" style="margin-bottom: 20px; margin-top: 10px;"><i class="fa fa-plus"></i>
          Add bonus </a>
      @endif
      @include('blocks.empl_profile.bonuses')
    </div>
  @endif
  
  <div class="tab-pane fade {{ $is_my_profile ? "" : " active in" }}" id="tab_team">
    @include('blocks.empl_profile.team')
  </div>
  <div class="tab-pane fade" id="tab_achievements">
    @include('blocks.empl_profile.achieve')
  </div>
  <div class="tab-pane fade" id="tab_skills">
    @include('blocks.empl_profile.skill')
  </div>
@endsection