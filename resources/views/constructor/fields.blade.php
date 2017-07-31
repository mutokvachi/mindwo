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
    <h4>Form tabs</h4>
    <div class="dd dx-constructor-tab-buttons clearfix">
      <div class="dd-list">
        @foreach($tabs as $tab)
          @include('constructor.fields_tab', [ 'tab' => $tab ])
        @endforeach
      </div>
    </div>
    <h4>{{ trans('constructor.form_fields') }}</h4>
    <div class="dx-constructor-tabs">
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
            @include('constructor.fields_grid')
          @else
            @include('constructor.fields_related', [
              'listTitle' => $tab->lists ? $tab->lists->list_title : '',
              'fieldTitle' => $tab->field ? $tab->field->title_list : ''
            ])
          @endif
        </div>
      @endforeach
    </div>
  </div>
  @include('constructor.fields_modal')
@endsection
