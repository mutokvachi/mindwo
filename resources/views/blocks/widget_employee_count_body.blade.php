@foreach($groups as $group)
<div class="progress">
    <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"
         aria-valuenow="{{ isset($counts[$group->id]) ? $counts[$group->id]['percent'] : '0' }}"
         style="width: {{ isset($counts[$group->id]) ? $counts[$group->id]['percent'] : '0' }}%">
        <a href="/search?searchType={{ trans('search_top.employees') }}&amp;source_id={{ $group->id }}">{{ $group->title }} ({{ isset($counts[$group->id]) ? $counts[$group->id]['count'] : '0' }})</a>
    </div>
</div>
@endforeach
@if(isset($counts['unassigned']))
<div class="progress">
    <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"
         aria-valuenow="{{ $counts['unassigned']['percent'] }}"
         style="width: {{ $counts['unassigned']['percent'] }}%">
        <a href="/search?searchType={{ trans('search_top.employees') }}&amp;source_id=-1">{{ trans('widgets.employee_count.unassigned') }} ({{ $counts['unassigned']['count'] }})</a>
    </div>
</div>
@endif