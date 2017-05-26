@extends('constructor.common')

@section('constructor_content')
  <div class="col-md-3">
    <h4>Register fields</h4>
    <div class="dx-fields-container">
      <div class="dd dx-cms-nested-list dx-used">
        <ol class="dd-list">
          @foreach($fields as $field)
            @if(!$formFields->where('field_id', $field->id)->count())
              <li class="dd-item" data-id="{{ $field->id }}">
                <div class="dd-handle dd3-handle"></div>
                <div class="dd3-content">
                  <div class="row">
                    <div class="col-md-10">
                      <b class="dx-fld-title">{{ $field->title_list }}</b>
                    </div>
                    <div class="col-md-2">
                      <a href="JavaScript:;" class="pull-right dx-cms-field-remove" title='Remove'><i class="fa fa-times"></i></a>
                      <a href="JavaScript:;" class="pull-right dx-cms-field-edit tooltipstered" title='Edit field properties'><i class="fa fa-cog"></i></a>
                    </div>
                  </div>
                </div>
              </li>
            @endif
          @endforeach
        </ol>
      </div>
    </div>
  </div>
  <div class="col-md-9">
    <h4>Form fields
      <button type="button" class="btn btn-white dx-preview-btn pull-right" style="margin-top: -15px;">
        <i class="fa fa-search"></i> Preview form
      </button>
    </h4>
    <table class="droppable-grid">
      @for($i = 1; $i <= 4; $i++)
        <tr>
          @for($j = 1; $j <= 4; $j++)
            <td>
              @foreach($formFields as $field)
                @if($field->row_number == $i && $field->col_number == $j)
                  <li class="dd-item dropped" data-id="{{ $field->field_id }}">
                    <div class="dd-handle dd3-handle"></div>
                    <div class="dd3-content">
                      <div class="row">
                        <div class="col-md-10">
                          <b class="dx-fld-title">{{ $fields->where('id', $field->field_id)->first()->title_list }}</b>
                        </div>
                        <div class="col-md-2">
                          <a href="JavaScript:;" class="pull-right dx-cms-field-remove" title='Remove'><i class="fa fa-times"></i></a>
                          <a href="JavaScript:;" class="pull-right dx-cms-field-edit tooltipstered" title='Edit field properties'><i class="fa fa-cog"></i></a>
                        </div>
                      </div>
                    </div>
                  </li>
                @endif
              @endforeach
            </td>
          @endfor
        </tr>
      @endfor
    </table>
  </div>
@endsection
