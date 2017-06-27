<div id="{{ $block_id }}" class="dx-block-container-view {{($is_full_page) ? 'dx-view-fullpage' : ''}}" 
     dx_block_init = '0' 
     dx_tab_id="{{ $tab_id }}" 
     dx_menu_id="{{ $menu_id }}" 
     dx_grid_id="{{ $grid_id }}" 
     dx_list_id="{{ $list_id }}"
     dx_rel_field_id = "{{ $rel_field_id }}"
     dx_rel_field_value = "{{ $rel_field_value }}"
     dx_form_htm_id = "{{ $form_htm_id }}" 
     dx_view_id = "{{ $view_id }}" 
     dx_grid_form = "{{ $grid_form }}"
     dx_open_item_id ="{{ $open_item_id }}"
     data-trans-msg-marked = "{{ trans('grid.msg_marked1') }}"
     data-trans-confirm-del1 = "{{ trans('grid.msg_confirm_del1') }}"
     data-trans-confirm-del-all = "{{ trans('grid.msg_confirm_del_all') }}"
     data-form-type-id = "{{ $form_type_id }}"
     data-filter-field-id="{{ $view_row->filter_field_id }}"
     data-form-id="{{ $form_id }}"
    >
    @if (!$tab_id)
        <div class='portlet' style='background-color: white; padding: 10px;'>
            
            <div class='portlet-body'>
    @endif

                <form class='form-horizontal'>
                    <input type=hidden name=filter_data id="filter_data_{{ $grid_id }}" value="{{ $filter_data }}">
                    <div class='row'>
                        <div class='{{ ($view_row->is_report) ? 'col-xs-12 col-sm-12 col-md-12 col-lg-12' : 'col-xs-6 col-sm-6 col-md-6 col-lg-6' }}' style="margin-bottom: 15px;">
                            <div class='btn-group dx-grid-butons'>
                                <button  type='button' class='btn btn-white dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title="{{ trans('grid.data_hint') }}">
                                    @if ($view_row->is_report)
                                        {{ $view_row->title }}
                                    @else
                                        {{ ($tab_id) ? trans('grid.data') : $grid_title }}
                                    @endif
                                    &nbsp;<i class='fa fa-caret-down'></i>
                                </button>
                                <ul class='dropdown-menu' style="z-index: 50000;">
                                    <li><a href='javascript:;' id="{{ $menu_id }}_refresh" dx_attr="refresh"><i class='fa fa-refresh'></i>&nbsp;{{ trans('grid.reload') }}</a></li>                                    
                                    <li role="separator" class="divider"></li>
                                    <li><a href='javascript:;' id="{{ $menu_id }}_excel" title='{{ trans('grid.excel_hint') }}'><i class='fa fa-file-excel-o'></i>&nbsp;{{ trans('grid.excel') }}</a></li>
                                    @if ($show_new_button && !$view_row->is_report && $is_import_rights)
                                        <li><a href='javascript:;' id="{{ $menu_id }}_import"><i class="fa fa-upload"></i>&nbsp;{{ trans('grid.import') }}</a></li>
                                    @endif
                                </ul>
                            </div>
                            @if ($show_new_button && !$view_row->is_report)
                                @if ($form_type_id == 3)
                                    <a class='btn btn-primary' href="{{Request::root()}}/employee/new">{{ trans('employee.new_employee') }}</a>
                                @else
                                    <button class='btn btn-primary' type='button' id="{{ $menu_id }}_new">{{ trans('grid.new') }}</button>
                                @endif                                
                            @endif

                            @include('blocks.view.view_settings_btn', ['pull_class' => ''])
                            
                            @if ($view_row->is_report && $view_row->filter_field_id)
                                @include('blocks.view.report_filter')
                            @endif
                        </div>

                        @if (!$view_row->is_report && count($combo_items))
                                <div class='col-xs-6 col-sm-6 col-md-6 col-lg-6'>
                                <div class='form-group'>
                                    <label class='col-sm-2 control-label'>{{ trans('grid.view') }}:</label>
                                    <div class='col-sm-10'>
                                        <div class='input-group' style="width: 100%;">
                                            <select class='form-control dx-views-cbo' id="{{ $menu_id }}_viewcbo">
                                                @if (count($combo_items_my) > 0)
                                                    <optgroup label="{{ trans('grid.lbl_public') }}">
                                                @endif
                                                @foreach($combo_items as $item)
                                                    <option 

                                                        @if ($item->id == $view_id)
                                                            selected
                                                        @endif                                                

                                                        value="{{ ($item->url && !$tab_id) ? $item->url : $item->id }}">{{ $item->title }}</option>
                                                @endforeach
                                                @if (count($combo_items_my) > 0)
                                                    </optgroup>
                                                @endif
                                                
                                                @if (count($combo_items_my) > 0)
                                                    <optgroup label="{{ trans('grid.lbl_private') }}">
                                                @endif
                                                @foreach($combo_items_my as $item)
                                                    <option 

                                                        @if ($item->id == $view_id)
                                                            selected
                                                        @endif                                                

                                                        value="{{ ($item->url && !$tab_id) ? $item->url : $item->id }}">{{ $item->title }}</option>
                                                @endforeach
                                                @if (count($combo_items_my) > 0)
                                                    </optgroup>
                                                @endif
                                                
                                            </select>
                                                @if ($is_view_rights)
                                                    <span class="input-group-btn">
                                                        <button class='btn btn-white dx-view-edit-btn' type='button' id ='{{ $menu_id}}_view_edit_btn' style="border: 1px solid #c2cad8!important; margin-left: -2px!important;"><i class='fa fa-list'></i></button>                                                    
                                                    </span>
                                                @endif
                                        </div>
                                    </div>
                                </div>
                            </div>                        
                        @endif

                    </div>
                </form>

                @if ($tab_id)
                    <div style='height: 15px'></div>
                @endif

                {!! $grid_htm !!}
                {!! $paginator_htm !!}
    @if (!$tab_id)
            </div>        
        </div>
    @endif
    
    @if ($is_view_rights)
        @include('blocks.view.view_edit_popup')
    @endif
    
    @include('blocks.view.field_settings')
    
    @if ($show_new_button && $is_import_rights)
        @include('blocks.view.view_import')
    @endif
</div>
