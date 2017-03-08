@extends('frame')

@section('main_custom_css')
  <link href="{{ elixir('css/elix_mail.css') }}" rel="stylesheet"/>
@endsection

@section('main_custom_javascripts')
  <script type="text/javascript">
    var inboxOptions = {
    	dateFormat: '{{ config('dx.txt_datetime_format', 'Y-m-d H:i') }}'
    };
  </script>
  <script src="{{ elixir('js/elix_mail.js') }}" type='text/javascript'></script>
@endsection

@section('main_content')
  <div class="portlet light">
    <div class="portlet-title">
      <div class="caption font-grey-cascade uppercase">
        <i class="fa fa-envelope-o"></i> {{ trans('mail.title') }}
        {{--
        <span class="badge badge-info" title="{{ trans('organization.hint_count') }}">{{ 0 }}</span>
        --}}
      </div>
    </div>
    <div class="portlet-body">
      <div class="inbox">
        <div class="row">
          <div class="col-md-3">
            @include('mail.sidebar')
          </div>
          <div class="col-md-9">
            <div class="inbox-body">
              <div class="inbox-header">
                <h1 class="pull-left">@yield('title')</h1>
                {{--
                <form class="form-inline pull-right" action="#">
                  <div class="input-group input-medium">
                    <input class="form-control" placeholder="{{ trans('mail.search') }}" type="text">
                    <span class="input-group-btn">
                      <button type="submit" class="btn green">
                        <i class="fa fa-search"></i>
                      </button>
                    </span>
                  </div>
                </form>
                --}}
              </div>
              <div class="inbox-content">
                @section('mail_content')
                @show
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection