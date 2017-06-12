@extends('constructor.common')

@section('constructor_content')
  <div class="col-md-3">
    <h4>{{ trans('constructor.register_fields') }}</h4>
    <div class="dx-fields-container">
      <div class="dd dx-cms-nested-list dx-used">
        <div class="row dd-list columns">
          @foreach($listFields as $field)
            <div class="col-md-12">
              <div class="dd-item not-in-form" data-id="{{ $field->id }}" data-hidden="0">
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
          @endforeach
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-9">
    <h4>{{ trans('constructor.form_fields') }}
      <button type="button" class="btn btn-white dx-preview-btn pull-right" style="margin-top: -15px;">
        <i class="fa fa-search"></i> {{ trans('constructor.preview_form') }}
      </button>
    </h4>
    <div class="constructor-grid">
      @for($i = 0; $i < count($grid); $i++)
        <div class="row-container">
          <div class="row-box row-handle"><i class="fa fa-arrows-v"></i></div>
          <div class="row-box row-button">
            <a href="javascript:;" class="dx-constructor-row-remove" title="{{ trans('constructor.remove_row') }}"><i class="fa fa-times"></i></a>
          </div>
          <div class="row columns dd-list">
            @for($j = 0; $j < count($grid[$i]); $j++)
              @if($field = $grid[$i][$j])
              @endif
              <div class="col-md-{{ 12 / count($grid[$i]) }}">
                <div class="dd-item {{ $field->is_hidden ? 'dx-field-hidden' : '' }}" data-id="{{ $field->field_id }}" data-hidden="{{ $field->is_hidden ? 1 : 0 }}">
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
            @endfor
          </div>
        </div>
      @endfor
      @if(count($grid) < 4)
        @for($i = 0; $i < 4 - count($grid); $i++)
            <div class="row-container">
              <div class="row-box row-handle"><i class="fa fa-arrows-v"></i></div>
              <div class="row-box row-button">
                <a href="javascript:;" class="dx-constructor-row-remove" title="{{ trans('constructor.remove_row') }}"><i class="fa fa-times"></i></a>
              </div>
              <div class="row columns dd-list">
              </div>
            </div>
        @endfor
      @endif
    </div>
    <div class="row">
      <div class="col-md-12" style="text-align: center; padding: 15px 0;">
        <button type="button" class="btn red dx-add-row-btn" style="">
          <i class="fa fa-plus"></i> {{ trans('constructor.add_row') }}
        </button>
      </div>
    </div>
  </div>
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
@endsection
