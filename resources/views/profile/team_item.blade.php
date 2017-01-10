<div class="item">
  <div class="item-head">
    <div class="item-details">
      <img class="item-pic" src="{{ $item->getAvatar() }}">
      <a href="{{ route('profile', ['id' => $item->id]) }}" class="item-name primary-link">{{ $item->display_name }}</a>
      <span class="item-label">{{ $item->position_title }}</span>
    </div>
  </div>
  <div class="item-body" style="padding-left: 50px;">
    <!--
      <span class="item-status"><span class="badge badge-empty badge-success"></span> Available</span><br>
    -->
    @if ($item->location_city || $item->country)
        <i class="fa fa-map-marker"></i> {{ $item->location_city }}{{ $item->country ? ", " . $item->country->code : '' }}<br>
    @endif
    
    @if ($item->phone)
        <i class='fa fa-phone'></i> {{ $item->phone }}<br>
    @endif
    
    @if ($item->email)
        <i class='fa fa-envelope-o'></i> <a href="mailto:{{ $item->email }}">{{ $item->email }}</a>
    @endif
  </div>
</div>
