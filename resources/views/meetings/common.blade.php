@extends('frame')

@section('main_custom_css')
  <link href="{{ elixir('css/elix_mail.css') }}" rel="stylesheet"/>
  
  @include('pages.view_css_includes')
  
  <style>
      tr.agenda_process {
          border: 1px solid red!important;
      }
      
      td.agenda_process {
          color: red;
      }
      
      td.agenda_processed {
          color: #36c6d3;
      }
      
      .margin-top-5 {
          margin-top: 5px;
      }
      
  </style>
@endsection

@section('main_custom_javascripts')

@include('pages.view_js_includes')
<script>
    $(document).ready(function () {
        $(".dropdown-toggle").dropdownHover();
        
        $(".dx-agenda-link").click(function() {
            var formData = "agenda_id=" + $(this).closest('tr').attr('data-id');

            var request = new FormAjaxRequestIE9('meetings/agenda', "", "", formData);
            request.progress_info = true;

            request.callback = function (data) {
                var frm_el = $("#dx-agenda-popup");
                frm_el.find(".modal-body").html(data['html']);
                
                frm_el.find(".modal-header span.badge").html(data['status']);

                frm_el.find('.dx-open-decidion-link').click(function() {
                    view_list_item('form', 7680, 264, 0, 0, "", "");
                })
                
                frm_el.modal('show');
            };

            // execute AJAX request
            request.doRequest();   
             
        });
        
        $(".dx-meeting-agendas-link").click(function() {
            var formData = "meeting_id=" + $(this).attr('data-id');

            var request = new FormAjaxRequestIE9('meetings/get_agendas_list', "", "", formData);
            request.progress_info = true;

            request.callback = function (data) {
                var frm_el = $("#agenda_set_popup");
                frm_el.find(".modal-body").html(data['html']);
                
                frm_el.modal('show');
            };

            // execute AJAX request
            request.doRequest();    
        
        })
    });
</script>
@endsection

@section('main_content')
    
        <div class="portlet light">
          <div class="portlet-title">
            <div class="caption font-grey-cascade uppercase">
                <i class="fa fa-users"></i> {{ $meeting_type_row->title }}                 
            </div>
            <div class="btn-group dx-register-tools" style="margin-left: 10px;">
                  <button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="fa fa-cog"></i> <i class="fa fa-caret-down"></i>
                  </button>
                  <ul class="dropdown-menu pull-right" style="z-index: 50000;">
                      <li><a href="javascript:;" class="dx-register-settings">Sapulču vietnes iestatījumi</a></li>
                      <li><a href="javascript:;" class="dx-view-settings">Jauna sapulču vietne</a></li>
                  </ul>
            </div>
            <input type='text' class='pull-right' placeholder='Meklēt sapulcēs...' value='' style='margin-top: 10px; padding-left: 5px;' />
          </div>
          <div class="portlet-body">                
                <div class="inbox">
                  <div class="row">
                    <div class="col-md-3">
                          @include('meetings.sidebar')
                    </div>
                    <div class="col-md-9">
                      <div class="inbox-body">
                        <div class="inbox-header" style='overflow: visible;'>
                            <h1 class="pull-left"><i class="fa fa-file-text-o"></i> @yield('title')</h1>
                            @yield('meeting_status')
                            <div class="btn-group dx-register-tools pull-right">
                                <button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Sapulce <i class="fa fa-caret-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" style="z-index: 60000;">
                                    @yield('meeting_menus')                                    
                                </ul>
                          </div>
                        </div>
                        <div class="inbox-content">
                          @section('meeting_content')
                          @show
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
          </div>
        </div>   
        
@endsection