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
		  $('.dx-constructor-wrap').ConstructorWizard({
			  list_id: {{ $list_id }},
			  view_id: {{ $view_id }},
			  step: '{{ $step }}'
		  });
	  });
  </script>
@endsection

@section('main_content')
  <div class="dx-constructor-wrap">
    <div class="portlet light" style="margin-bottom: 100px;">
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
      </div>
    </div>
    @include('constructor.footer')
  </div>
@endsection

