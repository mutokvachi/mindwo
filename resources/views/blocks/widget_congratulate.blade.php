@if(count($employees))
  <div class="portlet widget-congratulate" dx_block_id="congratulate">
    <div class="portlet-title">
      <div class="caption font-grey-cascade uppercase">{{ trans('congratulate.widget_title') }} <span class="badge badge-success">{{ count($employees) }}</span></div>
      {{--
      <div class="tools">
        <a class="collapse" href="javascript:;"> </a>
      </div>
      --}}
    </div>
    <div class="portlet-body">
      <div class="mt-actions">
        @foreach($employees as $employee)
          <div class="mt-action">
            <div class="mt-action-img">
              <img src="{{ $employee->getAvatar() }}" alt="">
            </div>
            <div class="mt-action-body">
              <div class="mt-action-row">
                <div class="mt-action-info">
                  <div class="mt-action-details">
                    <span class="mt-action-author">
                        @if ($self->is_profile)
                            <a href="{{ route('profile', $employee->id) }}">
                              {{ $employee->first_name }} {{ $employee->last_name }}
                            </a>
                        @else
                            {{ $employee->first_name }} {{ $employee->last_name }}
                        @endif
                    </span>
                    <div class="mt-action-desc">
                      {!! $self->getTypeOfEvent($employee) !!}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
@endif