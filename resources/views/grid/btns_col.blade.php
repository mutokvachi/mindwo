<td align='center'>
    <input type='checkbox' class='dx-grid-input-check' dx_item_id='{{ $item_id }}'>&nbsp;
    <div class="btn-group {{ $dropup }}">
        <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style='color: #DDDDDD;'><i class='fa fa-cog'></i> <i class="fa fa-caret-down"></i></button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li><a href='#' class='dx-grid-cmd-view' dx_item_id='{{ $item_id }}'><i class='fa fa-external-link'></i> {{ trans('grid.menu_view') }}</a></li>
            <li><a href='#' class='dx-grid-cmd-edit' dx_item_id='{{ $item_id }}'><i class='fa fa-edit'></i> {{ trans('grid.menu_edit') }}</a></li>
            <li><a href='#' class='dx-grid-cmd-delete' style='color: red;' dx_item_id='{{ $item_id }}'><i class='fa fa-cut'></i> {{ trans('grid.menu_delete') }}</a></li>
        </ul>
    </div>
</td>