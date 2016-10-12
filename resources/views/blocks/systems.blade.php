<div class="dx-systems-page">
    <h3 class="page-title">
        <span>Informācijas sistēmas</span>
        @if ($warning)
        <span id="sy-inc" class="badge bg-red-soft" style="letter-spacing:0;">{{ $warning }}</span>
        @endif
    </h3>
    <!-- Tools -->
    <div class="well">
        <div class="form">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Nosaukums</label>
                        <input id="sy-name" type="text" class="form-control" placeholder="Meklēt" autofocus="">
                    </div>
                </div>
                <!--
                <div class="col-md-4">
                    <div class="form-group">
                        <select class="form-control" id="sy-source">
                            <option value="0" selected>Visi datu avoti</option>
                            <option disabled>──────────</option>
                            @foreach ($sources as $source)
                            <option value="{{ $source->id }}">{{ $source->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                -->
            </div>

            <div class="row">
                <div class="col-md-12">
                    <button id="sy-exp" type="button" class="btn btn-sm blue-soft" title="Izvērst visu"><i class="fa fa-arrow-down"></i></button>
                    <button id="sy-col" type="button" class="btn btn-sm blue-soft" title="Sakļaut visu"><i class="fa fa-arrow-up"></i></button>
                    <button id="sy-all" type="button" class="btn btn-sm green-soft" disabled="">Rādīt visas sistēmas</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Systems -->
    <div class="panel-group accordion">
        @foreach ($systems as $system)
            @if ($system->is_incident)
            <div class="panel panel-system" data-sy-name="{{ strtolower($system->name) }}" data-sy-inc="1" data-sy-src="{{ $system->source_id }}">
                <div class="panel-heading bg-red-soft bg-font-red-soft">
            @else
            <div class="panel panel-primary panel-system" data-sy-name="{{ strtolower($system->name) }}" data-sy-inc="0" data-sy-src="{{ $system->source_id }}">
                <div class="panel-heading">
            @endif
                    <h4 class="panel-title" style="font-weight: 400">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="" href="#sid_{{ $system->id }}">
                            <span>{{ $system->name }}</span>
                            @if ($system->is_incident)
                            <i class="fa fa-exclamation-circle pull-right"></i>
                            @else
                            <i class="fa fa-check-circle pull-right"></i>
                            @endif
                        </a>
                    </h4>
                </div>

                <div id="sid_{{ $system->id }}" class="panel-collapse collapse">
                    <div class="panel-body bg-default">
                        @if ($system->is_incident)
                            <div class="alert alert-danger">
                                <p>{{ $system->info }}</p>
                            </div>
                        @endif
                        <h4 class="block" style="margin-top:0;padding-top:0;">Saite</h4>
                        <a href="{{ $system->url }}" class="btn btn-xs white" style="text-transform:lowercase;">{{ $system->url }}</a> 
                        <a href="http://webdialer.latvenergo.lv/webdialer/Webdialer?cmd=doMakeCall&sub=true&&destination=28888" title="Palīdzības dienests: 28888" class="btn yellow-gold pull-right tooltipstered" target="_blank"><i class="fa fa-phone"></i>&nbsp;Zvanīt Palīdzības dienestam</a> 
                        <div class="clearfix"></div>

                        @if ($system->employee_id)
                        <h4 class="block">Atbildīgais darbinieks</h4>
                        <div class="panel panel-default employee-panel">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="hidden-xs col-sm-2 col-md-2 employee-pic-box">
                                        <img src="{{Request::root()}}/img/avatar/120x160/{{ ($system->emp_picture_guid) ? $system->emp_picture_guid : $avatar }}.jpg" class="img-responsive">
                                    </div>

                                    <div class="col-xs-12 col-sm-10 col-md-6">
                                        <div class="employee-details-1">
                                            <div class="well">
                                                <h4>{{ $system->emp_employee_name }}
                                                    @if ($system->emp_is_today && $system->emp_email)                        
                                                        <a href="mailto: {{ $system->emp_email }}?subject=Apsveicu dzimšanas dienā!" title='Šodien dzimšanas diena!' style='color: #E87E04;'><i class="fa fa-gift"></i></a>
                                                    @endif
                                                </h4>
                                                <a href="#" class='dx_position_link' title="Rādīt visus darbiniekus ar tādu pašu amatu" dx_attr="{{ $system->emp_position }}" dx_source_id="{{ $system->emp_source_id }}">{{ $system->emp_position }}</a><br>
                                                <a href="#" class="small dx_department_link" title="Rādīt visus darbiniekus no šīs struktūrvienības" dx_attr="{{ $system->emp_department }}" dx_source_id="{{ $system->emp_source_id }}">{{ $system->emp_department }}</a><br><br><br>
                                                <div class="text-right">
                                                    @if ($system->emp_source_icon)
                                                    <i class="{{ $system->emp_source_icon }} fa-2x font-grey-salt pull-right" title='{{ $system->emp_source_title }}'></i>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="employee-details-2">
                                            <strong>Adrese</strong>
                                            <br>{{ $system->emp_office_address }} | kab. <a href="#" class='dx_cabinet_link' title='Rādīt visus darbiniekus no šī kabineta' dx_attr="{{ $system->emp_office_cabinet }}" dx_office_address="{{ $system->emp_office_address }}">{{ $system->emp_office_cabinet }}</a>

                                            @if ($system->emp_manager_id)
                                                <br><br>
                                                <strong>Tiešais vadītājs</strong>
                                                <br><a href="#" class="dx_manager_link" dx_manager_id="{{ $system->emp_manager_id }}" title="Meklēt tiešo vadītāju un visus tam pakļautos darbiniekus">{{ $system->emp_manager_name }}</a><br>
                                            @endif

                                            @if ($system->emp_left_to_date)
                                                <br><span class="label label-danger" style="display:block;margin-bottom:2px;width:90px;padding: 4px 6px 4px;">Prombūtnē</span>
                                                <div class="font-red">
                                                    <br><small>{{ $system->emp_left_reason }} līdz {{ short_date($system->emp_left_to_date) }}</small>

                                                    @if ($system->emp_substit_employee_name)
                                                        <br><small>Aizvieto <a href="#" class="dx_substit_link" dx_subst_empl_id="{{ $system->emp_substit_id }}" title="Meklēt aizvietotāju">{{ $system->emp_substit_employee_name }}</a></small>
                                                    @endif

                                                </div>
                                            @endif
                                        </div>  
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    @include('search_tools.employee_links_form')
</div>