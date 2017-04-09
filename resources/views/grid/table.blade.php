<div class="dx-grid-container">
  <div class='table-responsive dx-grid-outer-div dx-grid-inner-container'>
    <table class='table table-bordered table-striped cf table-overflow table-hover dx-grid-table' id='{{ $grid_id }}' {!! $data_attr !!}>
      <thead class='cf'>
        {!! $table_head !!}
      </thead>
      <tbody>
        {!! $table_body !!}
      </tbody>
    </table>
    <div class="dx-dropdown-content" data-field="" id="grid_popup_{{ $grid_id }}">
        <div class="dx-menu-item"><a href="javascript:;" class="dx-sort-asc"><i class="fa fa-sort-alpha-asc"></i> {{ trans('grid.sort_asc') }}</a></div>
        <div class="dx-menu-item"><a href="javascript:;" class="dx-sort-desc"><i class="fa fa-sort-alpha-desc"></i> {{ trans('grid.sort_desc') }}</a></div>
        <div class="dx-menu-item"><a href="javascript:;" class="dx-sort-none"><i class="fa fa-bars"></i> {{ trans('grid.sort_off') }}</a></div>
        <div class="dx-divider"></div>
        <div class="dx-menu-item"><a href="javascript:;" class="dx-filter" title='{{ trans('grid.filter_hint') }}'><i class='fa fa-filter'></i>&nbsp;{{ trans('grid.filter') }}<i class="fa fa-check pull-right dx-filter-on-mark" style="display: {{ ($filter_data && $filter_data != '[]') ? 'block' : 'none' }}"></i></a></div>
    </div>
  </div>
</div>