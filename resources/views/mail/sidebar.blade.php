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
      <a href="javascript:;" class="inbox-shortcut" data-id="0:0">
        <i class="fa fa-envelope-o"></i><span class="contact-name"> {{ trans('mail.all_company') }}</span>
      </a>
    </li>
    <li class="divider"></li>
    @foreach($sources as $source)
      <li>
        <a href="javascript:;" class="inbox-shortcut" data-id="dept:{{ $source->id }}">
          <i class="fa fa-envelope-o"></i><span class="contact-name"> {{ $source->title }}</span>
        </a>
      </li>
    @endforeach
    <li class="divider"></li>
    @foreach($teams as $team)
      <li>
        <a href="javascript:;" class="inbox-shortcut" data-id="team:{{ $team->id }}">
          <i class="fa fa-envelope-o"></i><span class="contact-name"> {{ $team->title }}</span>
        </a>
      </li>
    @endforeach
  </ul>
</div>
