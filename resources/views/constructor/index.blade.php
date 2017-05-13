@extends('frame')

@section('main_custom_css')
  <link href="{{ elixir('css/elix_mail.css') }}" rel="stylesheet"/>
  
  @include('pages.view_css_includes')
  
  {{-- style must go to elixir --}}
  <style>
      
      .dx-constructor-cell {
          border: 1px dashed lightgray;
          height: 40px;
      }
  </style>
@endsection

@section('main_custom_javascripts')

    @include('pages.view_js_includes')
    
    <script>
        // custom scripts must be to elixir and in nice way
        $(document).ready(function () {
            
            $('.dx-adv-btn').click(function() {
                var settings_closed = function() {                                        
                    // reload all page - there could be changes made very deep in related objects..
                };
                var list_id = $(this).closest('.dx-cms-register-constructor').data('list-id');
                
                // if list_id = 0 then try to save with AJAX (must be register title provided)
                // for new registers user object_id = 140
                
                view_list_item('form', list_id, 3, 0, 0, "", "", {after_close: settings_closed});
            });
            
            $('.dx-new-field').click(function() {
                var field_closed = function(frm) {
                    // update here fields list
                    // add in form in new row as last item too
                    
                    // get meta data from frm with jquery find
                    
                    // all cms forms have field item_id if it is 0 then item is not saved
                    alert(frm.html());
                };                
                
                var list_id = $(this).closest('.dx-cms-register-constructor').data('list-id');
                
                // if list_id = 0 then save list first with ajax then continue
                new_list_item(7, 17, list_id, "", "", {after_close: field_closed});
            });
            
            $(".dx-fields-container").nestable();
            $(".dx-constructor-cell").nestable();
        });
    </script>
@endsection

{{-- All texts must be in translate file --}}
@section('main_content')
    <div class='dx-cms-register-constructor' data-list-id='{{ $self->list_id }}'>
        <div class="portlet light">
          <div class="portlet-title">
            <div class="caption font-grey-cascade uppercase">
                <i class="fa fa-list"></i> Register                 
            </div>
            <div class="btn-group dx-register-tools pull-right">
                  <button type="button" class="btn btn-white dx-adv-btn">
                      <i class="fa fa-cog"></i> Advanced settings
                  </button>
            </div>
          </div>
          <div class="portlet-body">
              <div class="row">
                <div class="form-group has-feedback dx-form-field-line col-lg-6 col-md-6 col-sm-12 col-xs-12" dx_fld_name_form="skype" data-field-id="2106">
                                <label for="32f6de8d-dfa2-407a-bb43-cac4a6ece0b5_skype" style="vertical-align: top; margin-right: 10px;">
                                            <span class="dx-fld-title">Register name</span>
                                        </label>

                                <input class="form-control" autocomplete="off" type="text" id="32f6de8d-dfa2-407a-bb43-cac4a6ece0b5_skype" name="skype" maxlength="100" value="">
                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>

                                <div class="help-block with-errors" style="position: absolute; margin-top: -2px; max-height: 20px; overflow-y: hidden;"></div>

                            </div>
                  <div class="form-group has-feedback dx-form-field-line col-lg-6 col-md-6 col-sm-12 col-xs-12" dx_fld_name_form="skype" data-field-id="2106">
                                <label for="32f6de8d-dfa2-407a-bb43-cac4a6ece0b5_skype" style="vertical-align: top; margin-right: 10px;">
                                            <span class="dx-fld-title">Item name</span>
                                        </label>

                                <input class="form-control" autocomplete="off" type="text" id="32f6de8d-dfa2-407a-bb43-cac4a6ece0b5_skype" name="skype" maxlength="100" value="">
                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>

                                <div class="help-block with-errors" style="position: absolute; margin-top: -2px; max-height: 20px; overflow-y: hidden;"></div>

                            </div>
              </div>
                <div class="inbox">
                    
                  <div class="row">
                    <div class="col-md-3">
                          @include('constructor.sidebar')
                    </div>
                    <div class="col-md-9">    
                        @include('constructor.form_cells')
                    </div>
                  </div>
                </div>
          </div>
        </div>   
    </div>  
@endsection