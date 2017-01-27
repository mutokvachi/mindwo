@extends('frame')

@section('main_custom_css')
  <link href="/js/plugins/select2-4.0/css/select2.min.css" rel="stylesheet" type="text/css" />
  <link href="/zmetronic/global/plugins/select2/css/select2.bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="/metronic/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" rel="stylesheet" type="text/css" />
  <link href="/metronic/global/plugins/jquery-file-upload/blueimp-gallery/blueimp-gallery.min.css" rel="stylesheet" type="text/css" />
  <link href="/metronic/global/plugins/jquery-file-upload/css/jquery.fileupload.css" rel="stylesheet" type="text/css" />
  <link href="/metronic/global/plugins/jquery-file-upload/css/jquery.fileupload-ui.css" rel="stylesheet" type="text/css" />
  <link href="{{ url('metronic/apps/css/inbox.min.css') }}" rel="stylesheet" type="text/css"/>
  <style>
    .inbox .mt-checkbox {
      margin-bottom: 15px;
    }
  </style>
@endsection

@section('main_custom_javascripts')
  <script src="/js/plugins/select2-4.0/js/select2.js" type="text/javascript"></script>
  <script src="/metronic/global/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js" type="text/javascript"></script>
  <script src="/metronic/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js" type="text/javascript"></script>
  <script src="/metronic/global/plugins/jquery-file-upload/js/vendor/jquery.ui.widget.js" type="text/javascript"></script>
  <script src="/metronic/global/plugins/jquery-file-upload/js/vendor/tmpl.min.js" type="text/javascript"></script>
  <script src="/metronic/global/plugins/jquery-file-upload/js/vendor/load-image.min.js" type="text/javascript"></script>
  <script src="/metronic/global/plugins/jquery-file-upload/js/vendor/canvas-to-blob.min.js" type="text/javascript"></script>
  <script src="/metronic/global/plugins/jquery-file-upload/blueimp-gallery/jquery.blueimp-gallery.min.js" type="text/javascript"></script>
  <script src="/metronic/global/plugins/jquery-file-upload/js/jquery.iframe-transport.js" type="text/javascript"></script>
  <script src="/metronic/global/plugins/jquery-file-upload/js/jquery.fileupload.js" type="text/javascript"></script>
  <script src="/metronic/global/plugins/jquery-file-upload/js/jquery.fileupload-process.js" type="text/javascript"></script>
  <script src="/metronic/global/plugins/jquery-file-upload/js/jquery.fileupload-image.js" type="text/javascript"></script>
  <script src="/metronic/global/plugins/jquery-file-upload/js/jquery.fileupload-audio.js" type="text/javascript"></script>
  <script src="/metronic/global/plugins/jquery-file-upload/js/jquery.fileupload-video.js" type="text/javascript"></script>
  <script src="/metronic/global/plugins/jquery-file-upload/js/jquery.fileupload-validate.js" type="text/javascript"></script>
  <script src="/metronic/global/plugins/jquery-file-upload/js/jquery.fileupload-ui.js" type="text/javascript"></script>
  <script src="{{ url('js/pages/mail.js') }}"></script>
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