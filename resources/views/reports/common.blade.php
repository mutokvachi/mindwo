@extends('frame')

@section('main_custom_css')
  <link href="{{ elixir('css/elix_mail.css') }}" rel="stylesheet"/>
@endsection

@section('main_custom_javascripts')  
@endsection

@section('main_content')
    
        <div class="portlet light">
          <div class="portlet-title">
            <div class="caption font-grey-cascade uppercase">
              <i class="fa fa-bar-chart"></i> {{ trans('reports.page.title') }}
            </div>
          </div>
          <div class="portlet-body">                
                <div class="inbox">
                  <div class="row">
                    <div class="col-md-3">
                          @include('reports.sidebar')
                    </div>
                    <div class="col-md-9">
                      <div class="inbox-body">
                        <div class="inbox-header">
                            <h1 class="pull-left"><i class="@yield('icon')"></i> @yield('title')</h1>                
                        </div>
                        <div class="inbox-content">
                          @section('report_content')
                          @show
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
          </div>
        </div>
    
@endsection