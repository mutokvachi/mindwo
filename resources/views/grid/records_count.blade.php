<div class='row' id='paginator_{{ $grid_id }}'>
    <div class='col-lg-6'> 
        @if ($grid_total_pages == 1)
            {{ trans('grid.row_count') }}:
        @else
            {{ trans('grid.rows') }} {{ $start_row }} {{ trans('grid.rows_to') }} {{ $end_row }} {{ trans('grid.rows_from') }}
        @endif
        <span class="dx-grid-total-rows">{{ $total_count }}</span>
        @if (!$view_row->is_report && $total_count > 0)
            <div class="btn-group dropup">
                <button type="button" class="btn btn-white dropdown-toggle btn-xs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">{{ trans('grid.lbl_marked') }} <span class="dx-marked-count-lbl">0</span> <i class="fa fa-caret-down"></i></button>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href='#' class='dx-grid-cmd-markall'><i class='fa fa-check'></i> {{ trans('grid.menu_mark_all') }}</a></li>
                    <li><a href='#' class='dx-grid-cmd-delall'><i class='fa fa-cut'></i> {{ trans('grid.menu_delete_marked') }}</a></li>
                </ul>
            </div>
        @endif
    </div>
    @if ($is_paginator)
        <div class='col-lg-6'>
            <div class='btn-group pull-right dx-paginator-butons'>
                <button class='btn btn-white btn-sm' data_page_nr='1'><i class='fa fa-step-backward'></i></button>
                <button class='btn btn-white btn-sm' data_page_nr='{{ $prev_page }}'><i class='fa fa-caret-left'></i></button>
                <button class='btn btn-white btn-sm' data_page_nr='{{ $grid_page_nr }}'>{{ trans('grid.paginator_page')}} {{ $grid_page_nr }} {{ trans('grid.paginator_from')}} {{ $grid_total_pages }}</button>
                <button class='btn btn-white btn-sm' data_page_nr='{{ $next_page }}'><i class='fa fa-caret-right'></i></button>
                <button class='btn btn-white btn-sm' data_page_nr='{{ $grid_total_pages }}'><i class='fa fa-step-forward'></i></button>
            </div>            
        </div>
    @endif
</div>