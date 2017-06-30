@extends('frame')

@section('main_custom_css')
  <link href="{{ elixir('css/elix_constructor_wizard.css') }}" rel="stylesheet"/>
  @include('pages.view_css_includes')
@endsection

@section('main_custom_javascripts')
    @include('pages.view_js_includes')
    <script src="{{ elixir('js/elix_constructor_wizard.js') }}" type='text/javascript'></script>
    <script>
            $(document).ready(function()
            {
                    $('.dx-constructor-wizard').ConstructorWizard({
                            list_id: {{ $list_id }},
                            view_id: {{ $view_id }},
                            step: '{{ $step }}'
                    });
            });
    </script>
@endsection

@section('main_content')
  <div class="portlet light">
    <div class="portlet-title">
      <div class="caption font-grey-cascade uppercase">
        <i class="fa fa-list"></i> {{ trans('constructor.register') }}
        @if($list)
          - {{ $list->list_title }}
        @endif
      </div>
      @if($list_id)
        <div class="btn-group dx-register-tools pull-right">
          <button type="button" class="btn btn-white dx-adv-btn">
            <i class="fa fa-cog"></i> {{ trans('constructor.adv_settings') }}
          </button>
        </div>
      @endif
    </div>
    <div class="portlet-body dx-constructor-wizard">
      <div class="row">
        @include('constructor.steps')
      </div>
      <div class="row" style="margin-bottom: 20px">
        @section('constructor_content')
        @show
      </div>
      <div class="row">
        <div class="col-md-12" style="text-align: center">
          @if($step != 'names')
            <button id="prev_step" type="button" class="btn btn-primary dx-wizard-btn pull-left">
              <i class="fa fa-arrow-left"></i> {{ trans('constructor.back') }}
            </button>
          @endif
          <button id="submit_step" type="button" class="btn {{ ($step == 'rights') ? 'btn-white' : 'btn-primary'}} dx-wizard-btn pull-right">
            @if($step == 'rights')
              {{ trans('constructor.view_list') }} <i class="fa fa-list"></i>
            @else
              {{ trans('constructor.next') }} <i class="fa fa-arrow-right"></i>
            @endif
          </button>
        </div>
      </div>
    </div>
  </div>
@endsection

