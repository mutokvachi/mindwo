<div class="dx-constructor-row">
  <div class="row-box row-handle"><i class="fa fa-arrows-v"></i></div>
  <div class="row-box row-button">
    <a href="javascript:;" class="dx-constructor-row-remove" title="{{ trans('constructor.remove_row') }}"><i class="fa fa-times"></i></a>
  </div>
  <div class="row columns dd-list">
    @if(isset($id))
      @stack('row_content_'.$id)
    @endif
  </div>
</div>