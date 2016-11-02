@if($members = $employee->team_members()->orderBy('display_name')->get())
  @if($count = count($members))
    <div class="row">
      <div class="col-md-6 col-sm-12">
        <div class="general-item-list">
          @foreach($members->slice(0, ceil($count / 2)) as $item)
            @include('profile.team_item')
          @endforeach
        </div>
      </div>
      <div class="col-md-6 col-sm-12">
        <div class="general-item-list">
          @foreach($members->slice(ceil($count / 2)) as $item)
            @include('profile.team_item')
          @endforeach
        </div>
      </div>
    </div>
  @endif
@endif