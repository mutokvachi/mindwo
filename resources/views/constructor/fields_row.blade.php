<div class="dx-constructor-row">
  <div class="row-box row-handle"><i class="fa fa-arrows-v"></i></div>
  <div class="row-box row-button">
    <a href="javascript:;" class="dx-constructor-row-remove" title="{{ trans('constructor.remove_row') }}"><i class="fa fa-times"></i></a>
  </div>
  @if(isset($id))
    @yield('fields_row_content_'.$id)
  @else
    @yield('fields_row_content')
  @endif
</div>