<div class="mt-actions" style='padding-left: 20px; padding-right: 20px;'>
    @foreach($events as $event)
      <div class="mt-action" data-id="{{ $event->id }}">
        <div class="mt-action-img">
          <img src="{{ $event->user->getAvatar() }}" alt="" style='width: 45px; height: 45px;'>
        </div>
        <div class="mt-action-body">
          <div class="mt-action-row">
            <div class="mt-action-info">
              <div class="mt-action-icon">
                @if($icons = [ 1 => 'file-o', 'edit', 'trash-o' ])
                @endif
                <i class="fa fa-{{ $icons[$event->type->id] }}"></i>
              </div>
              <div class="mt-action-details">
                <span class="mt-action-author">
                    @if ($is_profile)
                        <a href="{{ route('profile', $event->user->id) }}" target='_blank'>
                          {{ $event->user->first_name }} {{ $event->user->last_name }}
                        </a>
                    @else
                        {{ $event->user->first_name }} {{ $event->user->last_name }}
                    @endif
                </span>
                <div class="mt-action-desc">
                  {{ $event->type->title }}
                </div>
              </div>
            </div>
            <div class="mt-action-datetime">
              @if($date = Carbon\Carbon::parse($event->event_time))
              @endif
              <span class="mt-action-date">{{ $date->format('d M, Y') }}</span>
              <span class="mt-action-time">{{ $date->format('H:i') }}</span>
            </div>
              <div class="mt-action-buttons" style='padding-top: 13px;'>   
                @if($event->type->id > 1)                          
                      <a href="javascript:;" class="btn btn-outline green btn-xs dx-cms-history_details"
                        data-list_id="{{ $events_list_id }}" 
                        data-item_id="{{ $event->id }}"
                      >{{ trans('form.btn_changes') }}</a>
                @endif
              </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>