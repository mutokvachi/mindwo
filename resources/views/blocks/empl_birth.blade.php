<div class="dx-employees-birth-page">
    <h3 class="page-title">{{ trans('empl_birth.widget_title') }}
        <small>
            <a href="{{ Request::root() }}/dzimsanas_dienas_sodien">{{ trans('empl_birth.lbl_today') }}
            <span class="badge badge-success" style='margin-top: -15px;'> {{ $empl_cnt_day }} </span>
            </a>
        </small>
    </h3>

    <div class="dx-employees-birth-block">
        @include('search_tools.search_form', [
                    'criteria_title' => trans('empl_birth.criteria_title'),
                    'fields_view' => 'search_tools.birthday_fields',
                    'form_url' => 'dzimsanas_dienas_sodien'
                ])
    </div>

    @if (count($employees))
        <div class="employee-list">
            @foreach($employees as $item)
                @include('elements.employee', ['item' => $item, 'show_date' => 1])
            @endforeach
        </div>
    @else
        @if ($is_post)
        <div class="alert alert-danger" role="alert">{{ trans('empl_birth.nothing_found') }}</div>
        @endif
    @endif
</div>