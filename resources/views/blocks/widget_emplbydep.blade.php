<div class="portlet widget-emplbydep" dx_block_id="emplbydep">
  <div class="portlet-title">
      <div class="caption font-grey-cascade uppercase">{{ trans('widgets.emplbydep.title') }} <small style="font-size: 60%;">{{ trans('widgets.emplbydep.today') }}</small> <span class="badge badge-success">{{ $totalCount }}</span></div>
    <div class="tools">
      <a class="collapse" href="javascript:;"> </a>
    </div>
  </div>
  <div class="portlet-body">
    @foreach($sources as $source)
      <div class="progress">
        <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"
          aria-valuenow="{{ isset($counts[$source->id]) ? $counts[$source->id]['percent'] : '0' }}"
          style="width: {{ isset($counts[$source->id]) ? $counts[$source->id]['percent'] : '0' }}%">
          <a href="/search?searchType={{ trans('search_top.employees') }}&amp;source_id={{ $source->id }}">{{ $source->title }} ({{ isset($counts[$source->id]) ? $counts[$source->id]['count'] : '0' }})</a>
        </div>
      </div>
    @endforeach
    @if(isset($counts['unassigned']))
      <div class="progress">
        <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"
          aria-valuenow="{{ $counts['unassigned']['percent'] }}"
          style="width: {{ $counts['unassigned']['percent'] }}%">
          <a href="/search?searchType={{ trans('search_top.employees') }}&amp;source_id=-1">{{ trans('widgets.emplbydep.unassigned') }} ({{ $counts['unassigned']['count'] }})</a>
        </div>
      </div>
    @endif
  </div>
</div>