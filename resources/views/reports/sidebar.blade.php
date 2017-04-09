<div class="inbox-sidebar">
  {{--<a href="{{ url('/mail/compose') }}" data-title="{{ trans('mail.compose') }}" class="btn red compose-btn btn-block">
    <i class="fa fa-edit"></i> {{ trans('mail.compose') }}</a>
  --}}
  <div class="btn default compose-btn btn-block" style="cursor: default; background: #eef4f7;">{{ trans('reports.page.lbl_categories') }}</div>
  <ul class="inbox-nav">
    @foreach($groups as $group)
      <li>
        <a href="{{ url('/reports/group/' . $group->id) }}">{{ $group->title }}
          <span class="badge badge-success" style="{{ (!$group->total_views) ? 'display: none': '' }}">{{ $group->total_views }}</span>
        </a>
      </li>
    @endforeach
  </ul>  
</div>
