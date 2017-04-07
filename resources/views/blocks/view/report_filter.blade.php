<span style="padding-left: 20px;">{{ trans('grid.report_interval') }}: </span>
<div class='input-group dx-datetime' data-format="{{ Config::get('dx.txt_date_format', 'd.m.Y') }}" data-locale = "{{ Lang::locale() }}" data-is-time = "0" style="display: inline-table;">
    <span class='input-group-btn' style="display: inline-table;">
        <button type='button' class='btn btn-white dx-datetime-cal-btn' style="height: 34px;"><i class='fa fa-calendar'></i></button>
    </span>
    <input class='form-control dx-datetime-field' type=text name = 'dx_filter_date_from' value = '{{ Request::input('dx_filter_date_from', '') }}' style='width: 100px;'/>
</div>
<span> - </span>
<div class='input-group dx-datetime' data-format="{{ Config::get('dx.txt_date_format', 'd.m.Y') }}" data-locale = "{{ Lang::locale() }}" data-is-time = "0" style="display: inline-table;">
    <span class='input-group-btn' style="display: inline-table;">
        <button type='button' class='btn btn-white dx-datetime-cal-btn' style="height: 34px;"><i class='fa fa-calendar'></i></button>
    </span>
    <input class='form-control dx-datetime-field' type=text name = 'dx_filter_date_to' value = '{{ Request::input('dx_filter_date_to', '') }}' style='width: 100px;'/>
</div>
<button type="submit" class="btn btn-primary dx-report-filter-btn">{{ trans('grid.btn_prepare_report') }}</button>