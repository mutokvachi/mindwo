<div class="dx-group-info" 
    data-group-id={{ $group->id }} 
    data-org-id={{ $group->org_id}} 
    data-is-ajax={{ $is_ajax }} 
    data-total-avail={{ $empl_count }}
    data-total-members={{ count($members) }}
    data-empl-list-id={{ $empl_list_id }}
    data-places-quota={{ $group->places_quota}}
    >
    <div class="row">
        <div class="col-md-7">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-users"></i>
                        <span class="caption-subject bold uppercase"> Grupa G{{ $group->id }}</span>
                        
                        @if ($orgs_count > 1)
                        <span class="caption-helper">{{ $group->org_title }}</span>
                        @endif
                    </div>                    
                </div>
                <div class="portlet-body">
                        <div class="dx-group-info-cont">
                            <div class="dx-item-title">Programma
                                @if ($group->signup_due)
                                <span class="pull-right dx-signup-due">Pieteikšanās termiņš: <span style="color: red;">{{ long_date($group->signup_due) }}</span></span>
                                @endif
                            </div>
                            <div>{{ $group->programm_code}} - {{ $group->programm_title}}</div>

                            <div class="dx-item-title">Modulis</div>
                            <div>{{ $group->module_code}} - {{ $group->module_title}}</div>
                            
                            <div class="dx-item-title">Pasākums</div>
                            <div>{{ $group->subject_code}} - {{ $group->subject_title}}</div>
                        </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-calendar"></i>
                        <span class="caption-subject bold uppercase"> Nodarbības</span>&nbsp;<span class="badge badge-default">{{ count($days) }}</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="dx-group-info-cont">
                        @foreach($days as $day)
                        <div style="padding-bottom: 8px;">
                                {{ short_date($day->lesson_date)}} {{ substr($day->time_from, 0, 5) }} - {{ substr($day->time_to, 0, 5) }}
                                <br>
                                <span style="font-size: 10px; font-color: gray;">{{ $day->room_address}}, telpa nr. {{ $day->room_nr}}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="portlet light">        
        <div class="portlet-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="row" style="margin-bottom: 8px; margin-top: 15px;">
                        <div class="col-md-3 dx-title">
                            Darbinieki                            
                        </div>
                        <div class="col-md-9">                                        
                            <div class="input-group pull-right">                                            
                                <input type="text" class="form-control dx-search-data" placeholder="Meklēt darbinieku..." data-obj="avail">                                            
                            </div>
                        </div>
                    </div>
                    <div class="dx-count-section">
                        @if ($empl_count > Config::get('education.empl_load_limit', 300))
                            <span class="dx-empl-count dx-empl-count-avail">{{ trans('calendar.complect.lbl_avail_cnt') }} {{ count($avail_empl) }} {{ trans('calendar.complect.lbl_cnt_from') }} {{ $empl_count}}</span>
                        @else
                            <span class="dx-empl-count dx-empl-count-avail">{{ trans('calendar.complect.lbl_avail_cnt') }}: {{ $empl_count}}</span>
                        @endif
                    </div>
                    <div class="ext-cont">
                        <div id="dx-avail-box">
                            @include('calendar.complect.empl_avail')
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row" style="margin-bottom: 8px; margin-top: 15px;">
                        <div class="col-md-3 dx-title">
                            Dalībnieki
                        </div>
                        <div class="col-md-9">                                        
                            <div class="input-group pull-right">                                            
                                <input type="text" class="form-control dx-search-data" placeholder="Meklēt dalībnieku..." data-obj="members">                                            
                            </div>
                        </div>
                    </div>
                    <div class="dx-count-section">                       
                            <span class="dx-empl-count">Limits: <b>{{ $group->places_quota }}</b> | </span><span class="dx-empl-count dx-empl-count-members">{{ trans('calendar.complect.lbl_members_cnt') }}: {{ count($members) }}</span>                       
                    </div>
                    <div class="ext-cont">
                        <div id="dx-members-box">
                            @include('calendar.complect.empl_members')
                        </div>
                    </div>
                </div>
            </div>    
                                
             
        </div>
    </div>
</div>