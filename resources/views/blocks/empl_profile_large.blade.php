<div class="portlet light dx-employee-profile" dx_is_init="0">
	<div class="portlet-title">
		<div class="caption">
			<i class="fa fa-user"></i>
			<span class="caption-subject bold uppercase"> {{ ($is_my_profile) ? "My profile" : "Employee" }}</span>
			<span class="caption-helper">
                            {{ ($is_my_profile) ? "" : "profile" }}
                        </span>
		</div>
		<div class="actions">
                    @if ($is_empl_edit_rights)
			<a href="javascript:;" class="btn btn-circle btn-default dx-edit-general" dx_employee_id="{{ $empl_row->id }}" dx_list_id="{{ $empl_list_id }}">
				<i class="fa fa-pencil"></i> Edit </a>
                    @endif
		</div>
	</div>
	<div class="portlet-body">
            <div class='row'>
                <div class='col-md-6'>
                    <div class="employee-panel">
			<div class="well">
				<div class="row">
				
					<div class="hidden-xs col-sm-3 col-md-3 employee-pic-box">
						<img src="/assets/global/tiles/woman.jpg" class="img-responsive img-thumbnail" style="max-height: 178px;">
					</div>

					<div class="col-xs-12 col-sm-9 col-md-9">
						<div class="employee-details-1">
								<a href="javascript:;" class="btn btn-circle btn-default green-jungle pull-right" title="Employee is at work"> Active </a>
								<h4>{{ $empl_row->display_name }}</h4>
								<a href="#" class="dx_position_link" dx_attr="Biroja vadītāja" dx_source_id=""> {{ $empl_row->position_title }}</a><br>
								<a href="#" class="small dx_department_link " dx_dep_id="" dx_attr="" dx_source_id="">Administrative department</a><br><br>
								<div class="text-left">
									<a href="mailto:{{ $empl_row->email }}">{{ $empl_row->email }}</a><br>
									+371 29 131 987<br><br>
									<p style="margin-bottom: 0px;">
										<img src="/assets/global/flags/en.png" title="English" style="margin-top: 1px;"/>&nbsp;<img src="/assets/global/flags/ru.png" title="Russian" />
										<span title="Employee location" class="pull-right"><i class="fa fa-map-marker"></i> London, UK</span>
									</p>
								</div>
							
						</div>
					</div>
					
				</div>
			</div>
                    </div>
                    <h3>About</h3>
                    <p>I was born in Riga and now I work here and I am wery happy to work here. My moto is: Be fast and furious!</p>
                </div>
                <div class='col-md-6'>                    
                    <div class="tiles" style="margin-bottom: 20px">
                            <div class="tile bg-blue-hoki double">
                                    <div class="tile-body">
                                            <i class="fa fa-briefcase"></i>
                                    </div>
                                    <div class="tile-object">
                                            <div class="name"> Hired </div>
                                            <div class="number"> Jun 24, 2014 </div>
                                    </div>
                            </div>
                            <div class="tile double bg-blue-madison">
                                    <div class="tile-body">
                                            <img src="/assets/global/tiles/manager.jpg" alt="">
                                            <h4>Bob Nilson</h4>
                                            <p> Projects supervisor<br /> IT department </p>
                                    </div>
                                    <div class="tile-object">
                                            <div class="name"> Direct supervisor </div>
                                            <div class="number"> </div>
                                    </div>
                            </div>
                            <div class="tile bg-red-intense">
                                    <div class="tile-body">
                                            <i class="fa fa-calendar"></i>
                                    </div>
                                    <div class="tile-object">
                                            <div class="name"> Sick days </div>
                                            <div class="number"> 3 </div>
                                    </div>
                            </div>
                            <div class="tile image double selected">
                                    <div class="tile-body">
                                            <img src="/assets/global/tiles/vacation.jpg" alt=""> </div>
                                    <div class="tile-object">
                                            <div class="name"> Available vacation days </div>
                                            <div class="number"> 14 </div>
                                    </div>
                            </div>
                            <div class="tile bg-yellow-saffron">
                                    <div class="tile-body">
                                            <i class="fa fa-gift"></i>
                                    </div>
                                    <div class="tile-object">
                                            <div class="name"> Bonuses </div>
                                            <div class="number"> 2 </div>
                                    </div>
                            </div>
                    </div>
                </div>
            </div>		
		@if (!$is_empl_edit_rights)
                    @include('blocks.empl_profile_tabs')
                @else
                    @include('blocks.empl_profile_tabs_large')
                @endif
                
		<div class="tab-content">
                    <div class="tab-pane fade in active" id="tab_general">
                        @include('blocks.empl_profile.general')
                    </div>
                    <div class="tab-pane fade" id="tab_leaves">				
                        <a class="btn btn-primary pull-right btn-sm dx-employee-leave-add-btn" style="margin-bottom: 20px; margin-top: 10px;"><i class="fa fa-plus"></i> Add leave </a>
                        @include('blocks.empl_profile.leaves')				
                    </div>
                    
			<div class="tab-pane fade" id="tab_bonuses">
                            @if ($is_empl_edit_rights)    
                                <a class="btn btn-primary pull-right btn-sm dx-employee-bonus-add-btn" style="margin-bottom: 20px; margin-top: 10px;"><i class="fa fa-plus"></i> Add bonus </a>
                            @endif        
                            @include('blocks.empl_profile.bonuses')
			</div>
			<div class="tab-pane fade" id="tab_team">
                            @include('blocks.empl_profile.team')
			</div>
			<div class="tab-pane fade" id="tab_skills">
                            @include('blocks.empl_profile.skill')
			</div>
                        <div class="tab-pane fade" id="tab_achievements">
                            @include('blocks.empl_profile.achieve')
			</div>
		</div>
										
	</div>
</div>