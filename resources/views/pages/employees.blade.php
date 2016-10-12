@extends('frame')

@section('main_custom_css')        
    @include('elements.employee_css', ['is_advanced_filter' => 1])
    
    @if ($profile_url != "/")
        @include('pages.view_css_includes')
    @endif
@stop

@section('main_content')
    
    <div class="dx-employees-page"
        dx_employee_count = '{{ count($employees) }}'
        dx_empl_list_id = "{{ Config::get('dx.employee_list_id') }}"
        trans_unit_hint = "{{ trans('employee_fields.unit_hint') }}"
        trans_choosing_unit = "{{ trans('employee_fields.choosing_unit') }}"
        data-profile-url = "{{ $profile_url }}"
        data-employees-list-id = "{{ Config::get('dx.employee_list_id') }}"
        >    

        <h3 class="page-title">{{ trans('employee.page_title') }}
            <small>{{ trans('employee.page_sub_title') }}</small>
            @if ($is_list_rights)
                <a class="btn btn-primary dx-employee-new-add-btn" ><i class="fa fa-plus"></i> {{ trans('employee.new_employee') }} </a> 
            @endif
        </h3>
        
        @include('search_tools.search_form', [
                'criteria_title' => trans('employee.lbl_search_placeholder'),
                'fields_view' => 'search_tools.employee_fields',
                'form_url' => 'search'
            ])

        @if (count($employees))         

            <div class="employee-list">           
                @foreach($employees as $item)
                    @include('elements.employee', ['item' => $item, 'show_date' => 0, 'profile_url' => $profile_url])
                @endforeach
            </div>
            <div class="text-center">
                {!! 
                    $employees->appends([
                        'criteria' => urlencode($criteria), 
                        'department' => urlencode($department_pg), 
                        'department_id' => $department_id,
                        'phone' => urlencode($phone),
                        'manager_id' => $manager_id, 
                        'subst_empl_id' => $subst_empl_id,
                        'cabinet' => urlencode($cabinet),
                        'office_address' => urlencode($office_address),
                        'source_id' => $source_id_pg, 
                        'searchType' => trans('search_top.employees'),
                        'position' => urlencode($position),
                        'is_from_link' => $is_from_link
                    ])->render() 
                !!}
                <div style="color: silver; margin-top: 10px; margin-bottom: 20px;">{{ trans('employee.lbl_count') }} <b>{{ $total_count }}</b></span></div>
            </div>
        @else
            <div class="alert alert-info" role="alert" style='margin-top: 20px;'>{{ trans('employee.lbl_nothing_found') }}</div>
        @endif
    
    </div>
@stop

@section('main_custom_javascripts') 
    
    @if ($profile_url != "/")
        @include('pages.view_js_includes')
    @endif
    
    <script src = "{{ elixir('js/elix_employees.js') }}" type='text/javascript'></script>
@stop