@if(count($events))
  <div class="portlet widget-activities" dx_block_id="activities">
    <div class="portlet-title">
      <div class="caption font-grey-cascade uppercase">LATEST ACTIVITIES</span></div>
      <div class="actions">
        <div class="btn-group">
          <a class="btn btn-default btn-sm" href="javascript:;" data-toggle="dropdown" aria-expanded="false">
            <i class="fa fa-filter"></i> Filter by <i class="fa fa-angle-down"></i>
          </a>
          <ul class="dropdown-menu pull-right">
            <li>
              <a href="javascript:;" data-group="-1">Show all</a>
            </li>
            @foreach($groups as $group)
              <li>
                <a href="javascript:;" data-group="{{ $group->id }}">{{ $group->title }}</a>
              </li>
            @endforeach
            <li>
              <a href="javascript:;" data-group="0">Other</a>
            </li>
          </ul>
        </div>
      </div>
      {{--
      <div class="tools">
        <a class="collapse" href="javascript:;"> </a>
      </div>
      --}}
    </div>
    <div class="portlet-body">
      <div class="mt-actions">
        @foreach($events as $event)
          <div class="mt-action" data-group="{{ $event->lists->group_id ? $event->lists->group_id : 0 }}">
            <div class="mt-action-img">
              <img src="{{ $event->user->getAvatar() }}" alt="">
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
                  @if($event->type->id == 1)
                    <a class="btn btn-primary green btn-xs" href="{{
                    
                      $event->lists->id == Config::get('dx.employee_list_id')
                      ? route('profile', $event->item_id)
                      : 'javascript:;'
                      
                    }}" data-profile="{{
                    
                      $event->lists->id == Config::get('dx.employee_list_id')
                      ? 'true'
                      : 'false'
                      
                    }}" data-list_id="{{ $event->lists->id }}" data-item_id="{{ $event->item_id }}">View</a>
                  @else
                    <a href="javascript:;" class="btn btn-outline green btn-xs dx-button-history"
                      data-list_id="{{ App\Libraries\DBHelper::getListByTable('dx_db_events')->id }}" data-item_id="{{ $event->id }}">History</a>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
@endif