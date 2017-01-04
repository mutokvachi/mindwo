<div class="portlet widget-emplbyteam" dx_block_id="emplbyteam">
  <div class="portlet-title">
    <div class="caption font-grey-cascade uppercase">{{ trans('widgets.emplbyteam.title') }} <small style="font-size: 60%;">{{ trans('widgets.emplbydep.today') }}</small> <span class="badge badge-success">{{ $totalCount }}</span></div>
    <div class="tools">
      <a class="collapse" href="javascript:;"> </a>
    </div>
  </div>
  <div class="portlet-body">
    @foreach($teams as $team)
      <div class="progress">
        <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"
          aria-valuenow="{{ isset($counts[$team->id]) ? $counts[$team->id]['percent'] : '0' }}"
          style="width: {{ isset($counts[$team->id]) ? $counts[$team->id]['percent'] : '0' }}%">
          <a href="/search?searchType={{ trans('search_top.employees') }}&amp;team_id={{ $team->id }}">{{ $team->title }} ({{ isset($counts[$team->id]) ? $counts[$team->id]['count'] : '0' }})</a>
        </div>
      </div>
    @endforeach
    @if(isset($counts['unassigned']))
      <div class="progress">
        <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"
          aria-valuenow="{{ $counts['unassigned']['percent'] }}"
          style="width: {{ $counts['unassigned']['percent'] }}%">
          <a href="/search?searchType={{ trans('search_top.employees') }}&amp;team_id=-1">{{ trans('widgets.emplbyteam.unassigned') }} ({{ $counts['unassigned']['count'] }})</a>
        </div>
      </div>
    @endif
  </div>
</div>