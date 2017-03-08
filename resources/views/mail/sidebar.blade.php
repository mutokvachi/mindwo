<div class="inbox-sidebar">
  <a href="{{ url('/mail/compose') }}" data-title="{{ trans('mail.compose') }}" class="btn red compose-btn btn-block">
    <i class="fa fa-edit"></i> {{ trans('mail.compose') }}</a>
  <ul class="inbox-nav">
    @foreach($folders as $id)
      <li>
        <a href="{{ url('/mail/'.$id) }}" class="folder-{{ $id }}" data-type="{{ $id }}" data-title="{{ trans('mail.'.$id) }}">{{ trans('mail.'.$id) }}
          <span class="badge badge-success" style="{{ $counts[$id] == 0 ? 'display: none': '' }}">{{ $counts[$id] }}</span>
        </a>
      </li>
    @endforeach
  </ul>
  <ul class="inbox-contacts">
    <li class="divider margin-bottom-30"></li>
    <li>
      <a href="javascript:;" class="inbox-shortcut" data-id="0:0" title="{{ trans('mail.hint_all_company') }}">
        <span class="contact-name"><i class="fa fa-envelope-o"></i> {{ trans('mail.all_company') }}</span>
      </a>
    </li>
    <li class="divider"></li>
    @foreach($sources as $source)
      <li>
        <a href="javascript:;" class="inbox-shortcut" data-id="dept:{{ $source->id }}" title="{{ trans('mail.hint_department') }}">
          <span class="contact-name"><i class="fa fa-envelope-o"></i> {{ $source->title }}</span>
        </a>
      </li>
    @endforeach
    <li class="divider"></li>
    @foreach($teams as $team)
      <li>
        <a href="javascript:;" class="inbox-shortcut" data-id="team:{{ $team->id }}" title="{{ trans('mail.hint_team') }}">
          <span class="contact-name"><i class="fa fa-envelope-o"></i> {{ $team->title }}</span>
        </a>
      </li>
    @endforeach
  </ul>
</div>
