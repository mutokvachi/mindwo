<div class="panel panel-default employee-panel">
    @if ($show_date)
        <div style='background-color: #4c87b9!important; color: white;'>
            <div style='padding: 12px;'>
                {!! format_birth_time($item->birth_date) !!}
            </div>            
        </div>
    @endif
    <div class="panel-body">
        
        <div class="row">
            <div class="hidden-xs col-sm-2 col-md-2 employee-pic-box">
                <img src="{{Request::root()}}/{{ \App\Libraries\Helper::getEmployeeAvatarBig($item->picture_guid) }}" class="img-responsive">
                
                    <div style="text-align: center; margin-top: 10px; max-width: 120px;">
                        @include('profile.status_info', ['avail' => \App\Libraries\Helper::getEmployeeStatus($item->valid_from, $item->termination_date)])
                    </div>
                
            </div>

            <div class="col-xs-12 col-sm-10 col-md-6">
                <div class="employee-details-1">
                    <div class="well">
                        <h4>
                            @if ($profile_url)
                                <a href='{{Request::root()}}{{ $profile_url}}{{ $item->id }}'>{{ $item->employee_name }}</a>
                            @else
                                {{ $item->employee_name }}
                            @endif
                            
                            @if ($item->is_today && $item->email)                        
                                <a href="mailto: {{ $item->email }}?subject={{ trans('employee.happy_birthday') }}" title='{{ trans('employee.today_birthday') }}' style='color: #E87E04;'><i class="fa fa-gift"></i></a>
                            @endif
                        </h4>
                        <a href="#" class='dx_position_link' title="{{ trans('employee.show_same_job') }}" dx_attr="{{ $item->position }}" dx_source_id="{{ $item->source_id }}">{{ $item->position }}</a><br>
                        <a href="#" class="small dx_department_link" title="{{ trans('employee.show_same_department') }}" dx_dep_id="{{ $item->department_id }}" dx_attr="{{ $item->department }}" dx_source_id="{{ $item->source_id }}">{{ $item->department }}</a><br><br>
                        <div class="text-left">
                            <a href="mailto:{{ $item->email }}">{{ $item->email }}</a><br>
                            {!! phoneClick2Call($item->phone, $click2call_url, $fixed_phone_part) !!}
                            @if ($item->source_icon && ! isset($no_source_icon))
                            <i class="{{ $item->source_icon }} fa-2x font-grey-salt pull-right" title='{{ $item->source_title }}'></i>
                            @endif
                        </div>                        
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-4">
                <div class="employee-details-2">                    
                    @if ($item->office_address)                    
                        <strong>{{ trans('employee.lbl_address') }}</strong>
                        <br />
                        {{ $item->office_address }} 
                    @endif
                    
                    @if ($item->office_cabinet) 
                    | {{ trans('employee.lbl_cabinet') }} <a href="#" class='dx_cabinet_link' title='{{ trans('employee.show_same_cabinet') }}' dx_attr="{{ $item->office_cabinet }}" dx_office_address="{{ $item->office_address }}">{{ $item->office_cabinet }}</a>
                    @endif
                    
                    @if ($item->manager_id)
                        @if ($item->office_address)
                            <br /><br />
                        @endif
                        <strong>{{ trans('employee.lbl_supervisor') }}</strong>
                        <br />
                        <a href="#" class="dx_manager_link" dx_manager_id="{{ $item->manager_id }}" title="{{ trans('employee.show_supervisor') }}">{{ $item->manager_name }}</a>
                        <br /><br />
                    @endif

                    @if ($item->left_to_date)
                        <span class="label label-danger" style="display:block;margin-bottom:2px;width:90px;padding: 4px 6px 4px;">{{ trans('employee.lbl_absent') }}</span>
                        <div class="font-red">
                            
                            <small>{{ ($item->left_reason) ? $item->left_reason : trans('employee.lbl_holiday') }} {{ trans('employee.lbl_to') }} {{ short_date($item->left_to_date) }}</small>

                            @if ($item->subst_empl_name)
                                <br />
                                <small>{{ trans('employee.lbl_substititutes') }} <a href="#" class="dx_substit_link" dx_subst_empl_id="{{ $item->substit_empl_id }}" title="{{ trans('employee.show_substitute') }}">{{ $item->subst_empl_name }}</a></small>
                            @endif

                        </div>
                    @endif
                </div> 
            </div>
        </div>
        @if ($profile_url || (isset($is_list_rights) && $is_list_rights))
            <div>
                @if ($profile_url)
                    <a class="btn btn-primary pull-right btn-sm"  href='{{Request::root()}}{{ $profile_url}}{{ $item->id }}'>
                @else
                    <a class="btn btn-primary pull-right btn-sm dx-open-profile"  href='JavaScript:;' data-empl-id = "{{ $item->id }}">
                @endif
                    <i class="fa fa-user"></i> {{ trans('employee.lbl_open_profile') }} 
                </a>
            </div>
        @endif
    </div>
</div>
