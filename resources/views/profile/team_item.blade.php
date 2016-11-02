<div class="item">
  <div class="item-head">
    <div class="item-details">
      <img class="item-pic" src="{{ $item->getAvatar() }}">
      <a href="{{ route('profile', ['id' => $item->id]) }}" class="item-name primary-link">{{ $item->display_name }}</a>
      <span class="item-label">{{ $item->position_title }}</span>
    </div>
  </div>
  <div class="item-body" style="padding-left: 50px;">
    <span class="item-status"><span class="badge badge-empty badge-success"></span> Available</span><br>
    <i class="fa fa-map-marker"></i> {{ $item->location_city }}, {{ $item->country ? $item->country->code : 'N/A' }}<br>
    <i class='fa fa-phone'></i> {{ $item->phone }} <a href="mailto:{{ $item->email }}">{{ $item->email }}</a>
  </div>
</div>
