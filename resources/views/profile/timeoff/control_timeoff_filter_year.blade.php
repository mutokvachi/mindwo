
@if (count($filter_all_years) > 0)
@foreach ($filter_all_years as $year)
<li>
    <a href="javascript:;" class="dx-emp-timeoff-sel-year" data-value="{{ $year->timeoffYear }}">
        {{ $year->timeoffYear }}
    </a>
</li>
@endforeach
@else
<li>
    <a href="javascript:;" class="dx-emp-timeoff-sel-year" data-value="{{ date('Y') }}">
        {{ date('Y') }}
    </a>
</li>
@endif
