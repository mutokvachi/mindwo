@if(count($external['team']))
  <div class="row">
    <div class="col-md-6 col-sm-12">
      <div class="general-item-list">
        @foreach($external['team']->slice(0, ceil(count($external['team']) / 2)) as $item)
          @include('blocks.empl_profile.team_item')
        @endforeach
      </div>
    </div>
    <div class="col-md-6 col-sm-12">
      <div class="general-item-list">
        @foreach($external['team']->slice(ceil(count($external['team']) / 2)) as $item)
          @include('blocks.empl_profile.team_item')
        @endforeach
      </div>
    </div>
  </div>
@endif