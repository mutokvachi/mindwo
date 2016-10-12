<div class='modal fade dx-cms-task-form' aria-hidden='true' id='list_item_view_form_{{ $frm_uniq_id }}' role='dialog' data-backdrop='static' 
     dx_is_init="0"
     dx_form_url="{{ $form_url }}"
     dx_rel_list_id="{{ $rel_list_id }}"
     dx_item_id="{{ $task_row->item_id }}"
     dx_rel_field_id="{{ $rel_field_id }}"
     dx_frm_uniq_id="{{ $frm_uniq_id }}"
     dx_grid_htm_id="{{ $grid_htm_id }}"
     dx_task_id="{{ $item_id }}"
     >
        <div class='modal-dialog modal-lg'>
                <div class='modal-content'>
                        
                        @include('elements.form_header',['form_title' => $form_title, 'badge' => ''])
                                            
                        <div class='modal-body'>
                            <div class='row'>
                            <form class="form-horizontal" id='item_edit_form_{{ $frm_uniq_id }}' method='POST'>
                                
                                <div class='form-group'>
                                    <label class='col-lg-4 control-label'>Dokumenta reģistrs:</label>
                                    <div class='col-lg-8'><input type="text" disabled class='form-control' value="{{ $task_row->register_name }}"></div>    
                                </div>
                                
                                <div class='form-group'>
                                    <label class='col-lg-4 control-label'>Dokumenta reģ. nr.:</label>
                                    <div class='col-lg-8'>
                                        <div class="input-group">
                                            <b><input type="text" disabled class='form-control' value="{{ $task_row->item_reg_nr }}"></b>
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary dx-cms-task-btn-open-item" type="button" title="Skatīt dokumenta kartiņu"><i class='fa fa-external-link'></i> Skatīt</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='col-lg-4 control-label'>Saturs:</label>
                                    <div class='col-lg-8'><textarea type="text" disabled class='form-control' rows='3'>{{ $task_row->item_info }}</textarea></div>    
                                </div>
                                
                                <hr>
                                
                                <div class='form-group'>
                                    <label class='col-lg-4 control-label'>Uzdevums:</label>
                                    <div class='col-lg-8'><b><input type="text" disabled class='form-control' name="task_title" value="{{ $task_row->task_type_title }}"></b></div>    
                                </div>
                                
                                @if ($task_row->task_details)
                                    <div class='form-group'>
                                        <label class='col-lg-4 control-label'>Uzdevuma paskaidrojums:</label>
                                        <div class='col-lg-8'><textarea type="text" disabled class='form-control' rows='3' name="task_txt">{{ $task_row->task_details }}</textarea></div>    
                                    </div>
                                @endif
                                
                                @if ($task_row->task_creator_name)
                                    <div class='form-group'>
                                        <label class='col-lg-4 control-label'>Uzdevējs:</label>
                                        <div class='col-lg-8'><input type="text" disabled class='form-control' value="{{ $task_row->task_creator_name }}"></div>    
                                    </div>
                                @endif
                                
                                @if ($task_row->substit_info)
                                    <div class='form-group'>
                                        <label class='col-lg-4 control-label'>Aizvietošanas informācija:</label>
                                        <div class='col-lg-8'><input type="text" disabled class='form-control' value="{{ $task_row->substit_info }}"></div>    
                                    </div>
                                @endif
                                
                                <div class='form-group'>
                                    <label class='col-lg-4 control-label'>Uzdots:</label>
                                    <div class='col-lg-8' style="margin-top: 5px;">{!! format_event_time($task_row->task_created_time) !!}</div>    
                                </div>
                                
                                <div class='form-group'>
                                    <label class='col-lg-4 control-label'>Izpildītājs:</label>
                                    <div class='col-lg-8'><input type="text" disabled class='form-control' value="{{ $task_row->employee_name }}"></div>    
                                </div>
                                
                                @if ($task_row->due_date)
                                    <div class='form-group'>
                                        <label class='col-lg-4 control-label'>Izpildes termiņš:</label>
                                        <div class='col-lg-8' style="margin-top: 5px;">{!! format_event_time($task_row->due_date) !!}</div>    
                                    </div>
                                @endif
                                
                                <div class='form-group'>
                                    <label class='col-lg-4 control-label'>Statuss:</label>
                                    <div class='col-lg-8'><input name="task_status" type="text" disabled class='form-control' value="{{ $task_row->status_title }}"></div>    
                                </div>
                                
                                @if ($task_row->task_closed_time)
                                    <div class='form-group'>
                                        <label class='col-lg-4 control-label'>Pabeigts:</label>
                                        <div class='col-lg-8' style="margin-top: 5px;">{!! format_event_time($task_row->task_closed_time) !!}</div>    
                                    </div>
                                @endif
                                
                                <div class='form-group'>
                                    <label class='col-lg-4 control-label'>Komentārs:</label>
                                    <div class='col-lg-8'><textarea type="text" {{ ($is_disabled) ? "disabled" : "" }} class='form-control' rows='3' id="{{ $frm_uniq_id }}_comment" name="task_comment">{{ $task_row->task_comment }}</textarea></div>    
                                </div>
                                
                                <input type=hidden id='{{ $frm_uniq_id }}_item_id' name='item_id' value='{{ $item_id }}'>
                            
                            </form>
                            
                            </div>
                        </div>
                        <div class="modal-footer">
                            @if ($is_disabled == 0)                               
                                <div class="alert alert-info" id='btns_sec_{{ $frm_uniq_id }}'>
                                        <div style="text-align: center;">
                                            @if ($task_row->task_type_id==6)
                                                <button type="button" class="btn btn-white dx-cms-task-btn-yes"><font color=green><i class="fa fa-check"></i></font> Iepazinos</button>
                                            @else
                                                <button type="button" class="btn btn-white dx-cms-task-btn-yes"><font color=green><i class="fa fa-check"></i></font> {{ ($task_row->task_type_id==2) ? "Izpildīt" : "Izpildīt" }}</button> &nbsp;
                                                @if (count($employees) > 0)
                                                <button type="button" class="btn btn-white dx-cms-task-btn-delegate"><font color=black><i class="fa fa-code-fork"></i></font> Deleģēt</button> &nbsp;
                                                @endif
                                                <button type="button" class="btn btn-white dx-cms-task-btn-no"><font color=red><i class="fa fa-times-circle"></i></font> Noraidīt</button>
                                            @endif
                                        </div>		
                                </div>	
                            @endif
                            <button type='button' class='btn btn-white' data-dismiss='modal'><i class='fa fa-sign-out'></i>&nbsp;Aizvērt</button>
                        </div>
                </div>
        </div>
</div>

@if ($is_disabled == 0 && count($employees) > 0)
    @include('workflow.task_form_delegate')
@endif