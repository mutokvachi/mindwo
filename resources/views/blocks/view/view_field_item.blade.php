<li class="dd-item" data-id="{{ $field->id }}">
    <div class="dd-handle dd3-handle"> </div>
    <div class="dd3-content">
        <div class="row">
            <div class="col-md-10">
                <b class="dx-fld-title">{{ $field->title }}</b>
            </div>            
            <div class="col-md-2">
                <a href="JavaScript:;" title="{{ trans('grid.btn_remove_fld') }}" class="pull-right dx-cms-field-remove"><i class="fa fa-trash-o"></i></a>
                <a href="JavaScript:;" title="{{ trans('grid.btn_add_fld') }}" class="pull-right dx-cms-field-add"><i class="fa fa-plus-square-o"></i></a>
            </div>            
        </div>
    </div>
</li>