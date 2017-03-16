<div class='table-responsive dx-grid-outer-div'>
    <table class='table table-bordered table-striped cf table-overflow table-hover dx-grid-table' id='{{ $grid_id }}' {!! $data_attr !!}>
        <thead class='cf'>
            {!! $table_head !!}
        </thead>
        <tbody>
            {!! $table_body !!}
        </tbody>        
    </table>
    <div class="dx-dropdown-content" data-field="">
        <div class="dx-menu-item"><a href="javascript:" class="dx-sort-asc"><i class="fa fa-sort-alpha-asc"></i> {{ trans('grid.sort_asc') }}</a></div>
        <div class="dx-menu-item"><a href="javascript:" class="dx-sort-desc"><i class="fa fa-sort-alpha-desc"></i> {{ trans('grid.sort_desc') }}</a></div>
        <div class="dx-menu-item"><a href="javascript:" class="dx-sort-none"><i class="fa fa-bars"></i> {{ trans('grid.sort_off') }}</a></div>
    </div>
</div>