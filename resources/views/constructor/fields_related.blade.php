<div class="dx-constructor-related">
  <div class="form-group col-md-6">
    <label for="list_title">
      <span class="dx-fld-title">{{ trans('constructor.lbl_related_list') }}</span>
    </label>
    <div class="input-group">
      <input class="form-control" disabled="disabled" type="text" name="list_title" value="{{ $listTitle or '' }}">
    </div>
  </div>
  <div class="form-group col-md-6">
    <label for="field_title">
      <span class="dx-fld-title">{{ trans('constructor.lbl_related_field') }}</span>
    </label>
    <div class="input-group">
      <input class="form-control" disabled="disabled" type="text" name="field_title" value="{{ $fieldTitle or '' }}">
    </div>
  </div>
</div>