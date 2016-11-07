@extends('profile.common')

@section('profile_tabs')
    @if(!$is_edit_rights)
          <li class="active">
            <a href="#tab_info" data-toggle="tab" aria-expanded="true"> Info </a>
          </li>


          @if($is_my_profile)
            <li class="">
              <a href="#tab_leaves" data-toggle="tab" aria-expanded="true"> Leaves </a>
            </li>
            <li class="">
              <a href="#tab_bonuses" data-toggle="tab" aria-expanded="false"> Bonuses </a>
            </li>
          @endif

          <li class="">
            <a href="#tab_team" data-toggle="tab" aria-expanded="false"> Team </a>
          </li>
          <li class="">
            <a href="#tab_achievements" data-toggle="tab" aria-expanded="false"> Achievements </a>
          </li>
          <li class="">
            <a href="#tab_skills" data-toggle="tab" aria-expanded="false"> Skills </a>
          </li>          
    @else
        <li class="">
            <a href="#tab_personal_docs" data-toggle="tab" aria-expanded="false"> Documents </a>
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
        </div>
    
        @if ($is_my_profile)
            <div class="tab-pane fade" id="tab_leaves">
              <a class="btn btn-primary pull-right btn-sm dx-employee-leave-add-btn" style="margin-bottom: 20px; margin-top: 10px;"><i class="fa fa-plus"></i>
                Add leave </a>
              @include('profile.leaves')
            </div>
            <div class="tab-pane fade" id="tab_bonuses">
              @if (Auth::user()->id == 1)
                <a class="btn btn-primary pull-right btn-sm dx-employee-bonus-add-btn" style="margin-bottom: 20px; margin-top: 10px;"><i class="fa fa-plus"></i>
                  Add bonus </a>
              @endif
              @include('profile.bonuses')
            </div>
        @endif
  
        <div class="tab-pane fade" id="tab_team">
          @include('profile.team')
        </div>
        <div class="tab-pane fade" id="tab_achievements">
          @include('profile.achieve')
        </div>
        <div class="tab-pane fade" id="tab_skills">
          @include('profile.skill')
        </div>
    @else
        <div class="tab-pane fade" id="tab_personal_docs"> 
          @include('profile.personal_docs', ['user' => $employee])
        </div>
    @endif
@endsection