<div class="portlet dx-widget-employeecount" 
     data-dx_block_init='0' 
     data-date_format="{{ config('dx.txt_date_format') }}" 
     data-widget_name="{{ $widgetName }}">
    <div class="portlet-title">
        <div class="caption font-grey-cascade uppercase">
            {{ trans('widgets.employee_count.title_' . $widgetName) }} <small style="font-size: 60%;" class="dx-widget-employeecount-filter-label">{{ trans('date_range.flt_today') }}</small> 
            <span class="badge badge-success dx-widget-employeecount-total">{{ $totalCount }}</span>  
            <a class="btn btn-sm btn-circle btn-default dx-widget-employeecount-filter" href="javascript:;" >
                <i class='fa fa-calendar'></i> {{ trans('widgets.employee_count.filter') }}
            </a>
        </div>
        <div class="tools">        
            <a class="collapse" href="javascript:;"> </a>           
        </div>

    </div>
    <div class="portlet-body dx-widget-employeecount-body">
        @include('blocks/widget_employee_count_body')
    </div>
</div>