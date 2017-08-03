@extends('constructor.common')

@section('constructor_content')
  <div class="col-md-3">
    <h4>{{ trans('constructor.register_fields') }}</h4>
    <div class="dx-fields-container">
      <div class="dd dx-cms-nested-list dx-used">
        <div class="row dd-list columns">
          @foreach($listFields as $field)
            @include('constructor.fields_item', [
              'field' => $field,
              'class' => 'not-in-form',
              'column' => 12,
              'hidden' => 0
            ])
          @endforeach
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-9">
    <div class="dx-constructor-form" data-tab-id="0">
      <h4>{{ trans('constructor.form_fields') }}</h4>
      @include('constructor.fields_grid', ['tabId' => 0])
      <div class="col-md-12" style="text-align: center">
        <button type="button" class="btn green dx-add-row-btn" style="">
          <i class="fa fa-plus"></i> {{ trans('constructor.add_row') }}
        </button>
      </div>
    </div>
    <div class="dx-constructor-form-tabs" style="{{ count($tabs) ? '' : 'display: none' }}">
      <h4>{{ trans('constructor.form_tabs') }}</h4>
      <div class="dd dx-constructor-tab-buttons clearfix">
        <div class="dd-list">
          @foreach($tabs as $tab)
            @include('constructor.fields_tab', [ 'tab' => $tab ])
          @endforeach
        </div>
      </div>
      <h4>{{ trans('constructor.tab_fields') }}</h4>
      <div class="dx-constructor-tabs">
        <div class="dx-constructor-tabs-wrap">
          @if($t = 0)
          @endif
          @foreach($tabs as $tab)
            <div class="dx-constructor-tab tab-id-{{ $tab->id }} {{ $tab->is_custom_data ? 'custom-data' : 'related-grid' }}"
              style="{{ $t ? 'display: none' : '' }}"
              data-tab-id="{{ $tab->id }}"
              data-tab-title="{{ $tab->title }}">
              @if($t++)
              @endif
              <h5>{{ $tab->title }}</h5>
              @if($tab->is_custom_data == 1)
                @include('constructor.fields_grid', [
                  'tabId' => $tab->id
                ])
              @else
                @include('constructor.fields_related', [
                  'listTitle' => $tab->lists ? $tab->lists->list_title : '',
                  'fieldTitle' => $tab->field ? $tab->field->title_list : ''
                ])
              @endif
            </div>
          @endforeach
        </div>
        <div class="col-md-12" style="text-align: center">
          <div class="btn-group" role="group">
            <button type="button" class="btn green dx-add-row-btn" style="">
              <i class="fa fa-plus"></i> {{ trans('constructor.add_row') }}
            </button>
            <button type="button" class="btn green dx-tab-edit-btn" style="">
              <i class="fa fa-edit"></i> {{ trans('constructor.tab_edit') }}
            </button>
            <button type="button" class="btn green dx-tab-delete-btn" style="">
              <i class="fa fa-close"></i> {{ trans('constructor.tab_del') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  @include('constructor.fields_modal')
@endsection
