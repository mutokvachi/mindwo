@if(count($employees))
  <div class="portlet widget-absences" dx_block_id="absences">
    <div class="portlet-title">
      <div class="caption font-grey-cascade uppercase">{{ trans('absences.widget_title') }} <span class="badge badge-danger">{{ count($employees) }}</span></div>
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
                      {{ short_date($employee->left_from) }} - {{ short_date($employee->left_to) }}
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