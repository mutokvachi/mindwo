<div class="modal fade dx-popup-modal" id="fields_popup" aria-hidden="true" role="dialog" data-backdrop="static" style="z-index: 999999;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      @include('elements.form_header', [
        'form_title' => trans('grid.view_editor_form_title'),
        'badge' => trans('grid.badge_edit'),
        'form_icon' => '<i class="fa fa-list"></i>'
      ])
      <div class="modal-body" style="overflow-y: auto; max-height: 500px;">
        <input type="hidden" name="field_id" value="0">
        <div class="form-group col-md-12">
          <label for="field_title">
            <span class="dx-fld-title">{{ trans('grid.lbl_field_title') }}</span>
          </label>
          <div class="input-group">
            <input class="form-control" autocomplete="off" type="text" name="title_form" value="">
          </div>
        </div>
        <div class="form-group col-md-12">
          <label for="is_hidden">
            <span class="dx-fld-title">{{ trans('grid.ch_is_hidden') }}</span>
          </label>
          <div class="input-group">
            <input type="checkbox" name="is_hidden" class="dx-bool">
          </div>
        </div>
      </div>
      <div class="modal-footer dx-view-popup-footer">
        <button type="button" class="btn btn-primary dx-view-btn-save">{{ trans('form.btn_save') }}</button>
        <button type="button" class="btn btn-white" data-dismiss="modal">{{ trans('form.btn_cancel') }}</button>
      </div>
    </div>
  </div>
</div>
