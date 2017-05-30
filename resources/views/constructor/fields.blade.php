@extends('constructor.common')

@section('constructor_content')
  <div class="col-md-3">
    <h4>{{ trans('constructor.register_fields') }}</h4>
    <div class="dx-fields-container">
      <div class="dd dx-cms-nested-list dx-used">
        <div class="row dd-list columns">
          @foreach($listFields as $field)
            <div class="col-md-12">
              <div class="dd-item" data-id="{{ $field->id }}">
                <div class="dd-handle dd3-handle"></div>
                <div class="dd3-content">
                  <span class="controls">
                    <a href="JavaScript:;" class="pull-right dx-cms-field-remove" title="{{ trans('constructor.remove') }}"><i class="fa fa-times"></i></a>
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
                <div class="dd-item" data-id="{{ $field->field_id }}">
                  <div class="dd-handle dd3-handle"></div>
                  <div class="dd3-content">
                    <span class="controls">
                      <a href="JavaScript:;" class="pull-right dx-cms-field-remove" title="{{ trans('constructor.remove') }}"><i class="fa fa-times"></i></a>
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
    </div>
    <div class="row">
      <div class="col-md-12" style="text-align: center; padding: 15px 0;">
        <button type="button" class="btn red dx-add-row-btn" style="">
          <i class="fa fa-plus"></i> {{ trans('constructor.add_row') }}
        </button>
      </div>
    </div>
  </div>
@endsection
