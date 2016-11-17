@if(count($events))
  <div class="portlet widget-activities" dx_block_id="activities">
    <div class="portlet-title">
      <div class="caption font-grey-cascade uppercase">ACTIVITIES</span></div>
      <div class="tools">
        <a class="collapse" href="javascript:;"> </a>
      </div>
    </div>
    <div class="portlet-body">
      <div class="mt-actions">
        @foreach($events as $event)
          <div class="mt-action">
            <div class="mt-action-img">
              <img src="{{ $event->user->getAvatar() }}" alt="">
            </div>
            <div class="mt-action-body">
              <div class="mt-action-row">
                <div class="mt-action-info">
                  <div class="mt-action-icon">
                    <i class="fa fa-edit"></i>
                  </div>
                  <div class="mt-action-details">
                    <span class="mt-action-author">
                      <a href="{{ route('profile', $event->user->id) }}">
                        {{ $event->user->first_name }} {{ $event->user->last_name }}
                      </a>
                    </span>
                    <div class="mt-action-desc">
                      {{ $event->lists->list_title }}: {{ $event->type->title }}
                    </div>
                  </div>
                </div>
                <div class="mt-action-datetime">
                  @if($date = Carbon\Carbon::parse($event->event_time))
                  @endif
                  <span class="mt-action-date">{{ $date->format('d M') }}</span>
                  <span class="mt-action-time">{{ $date->format('H:i') }}</span>
                </div>
                <div class="mt-action-buttons">
                  <a class="btn btn-outline green btn-xs">History</a>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
@endif