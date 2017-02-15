<li class="dd-item" 
    data-id="{{ $field->id }}" 
    data-is-hidden="{{ (isset($field->is_hidden)) ? $field->is_hidden : 0 }}"
    data-operation-id="{{ (isset($field->operation_id)) ? $field->operation_id : 0 }}"
    data-criteria="{{ (isset($field->criteria)) ? $field->criteria : '' }}"
    data-field-type="{{ $field->field_type }}"
    data-rel-list-id="{{ $field->rel_list_id }}"
    data-rel-field-id="{{ $field->rel_display_field_id }}"
    >
    <div class="dd-handle dd3-handle"> </div>
    <div class="dd3-content">
        <div class="row">
            <div class="col-md-10">
                <b class="dx-fld-title">{{ $field->title }}</b>
                <i class='fa fa-filter dx-icon-filter' title='{{ trans('grid.tooltip_filter') }}'></i>
                <i class='fa fa-eye-slash dx-icon-hidden' title='{{ trans('grid.tooltip_hidden') }}'></i>
            </div>            
            <div class="col-md-2">
                <a href="JavaScript:;" title="{{ trans('grid.btn_remove_fld') }}" class="pull-right dx-cms-field-remove"><i class="fa fa-trash-o"></i></a>
                <a href="JavaScript:;" title="{{ trans('grid.btn_add_fld') }}" class="pull-right dx-cms-field-add"><i class="fa fa-plus-square-o"></i></a>
            </div>            
        </div>
    </div>
</li>