@if(count($events))
  @if(!Request::ajax())
    <div class="portlet widget-activities" dx_block_id="activities">
      <div class="portlet-title">
        <div class="caption font-grey-cascade uppercase">{{ trans('activities.widget_title') }}</span></div>
        <div class="actions">
          <div class="btn-group">
            <a class="btn green btn-outline btn-circle btn-sm" style="border-width: 1px !important;" href="javascript:;" data-toggle="dropdown" data-close-others="true" aria-expanded="false">
              {{ trans('activities.lbl_filter') }} <i class="fa fa-angle-down"></i>
            </a>
            <div class="dropdown-menu pull-right form-group">
              <div class="mt-checkbox-list">
                @foreach($groups as $group)
                  <label class="mt-checkbox mt-checkbox-outline">
                    <input type="checkbox" value="{{ $group->id }}"> {{ $group->title }}
                    <span></span>
                  </label>
                @endforeach
                <label class="mt-checkbox mt-checkbox-outline">
                  <input type="checkbox" value=""> {{ trans('activities.lbl_other') }}
                  <span></span>
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="portlet-body">
    @endif
      <div class="mt-actions">
        @foreach($events as $event)
          <div class="mt-action" data-id="{{ $event->id }}" data-group="{{ $event->lists->group_id ? $event->lists->group_id : 0 }}">
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
                        @if ($self->is_profile)
                            <a href="{{ route('profile', $event->user->id) }}">
                              {{ $event->user->first_name }} {{ $event->user->last_name }}
                            </a>
                        @else
                            {{ $event->user->first_name }} {{ $event->user->last_name }}
                        @endif
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
                      
                    }}" data-list_id="{{ $event->lists->id }}" data-item_id="{{ $event->item_id }}">{{ trans('activities.btn_view') }}</a>
                  @else
                    <a href="javascript:;" class="btn btn-outline green btn-xs dx-button-history"
                      data-list_id="{{ App\Libraries\DBHelper::getListByTable('dx_db_events')->id }}" data-item_id="{{ $event->id }}">{{ trans('activities.btn_history') }}</a>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
        @if(!Request::ajax())
    </div>
  </div>
    @endif
@endif