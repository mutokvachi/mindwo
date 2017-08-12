<div class="col-md-{{ $column < 3 ? 3 : $column }} dx-field">
  <div class="dd-item {{ $class }}" data-id="{{ $field->id }}" data-hidden="{{ $hidden }}">
    <div class="dd-handle dd3-handle"></div>
    <div class="dd3-content">
      <span class="controls">
        <a href="JavaScript:;" class="pull-right dx-cms-field-remove" title="{{ trans('constructor.remove') }}"><i class="fa fa-times"></i> </a>
        <a href="JavaScript:;" class="pull-right dx-cms-field-edit" title="{{ trans('constructor.edit_field') }}"><i class="fa fa-cog"></i></a>
      </span>
      <b class="dx-fld-title">{{ $field->title_form }}</b>
    </div>
  </div>
</div>
