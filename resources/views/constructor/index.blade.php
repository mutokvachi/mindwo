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
      
      // wizard 
      .dx-step-naming .dx-panel-naming {
          display: block;
      }
      
      .dx-step-naming .dx-panel-fields {
          display: none;
      }
      
      .dx-step-naming .dx-panel-roles {
          display: none;
      }
      
      .dx-step-naming .dx-panel-navigation {
          display: none;
      }
      
      .dx-step-fields .dx-panel-fields {
          display: block;
      }
      
      .dx-step-fields .dx-panel-naming {
          display: none;
      }
      
      .dx-step-fields .dx-panel-roles {
          display: none;
      }
      
      .dx-step-fields .dx-panel-navigation {
          display: none;
      }
      
      .dx-step-roles .dx-panel-roles {
          display: block;
      }
      
      .dx-step-roles .dx-panel-fields {
          display: none;
      }
      
      .dx-step-roles .dx-panel-naming {
          display: none;
      }
      
      .dx-step-roles .dx-panel-navigation {
          display: none;
      }
      
      .dx-step-navigation .dx-panel-navigation {
          display: block;
      }
      
      .dx-step-navigation .dx-panel-fields {
          display: none;
      }
      
      .dx-step-navigation .dx-panel-naming {
          display: none;
      }
      
      .dx-step-navigation .dx-panel-roles {
          display: none;
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
            
            $('.dx-preview-btn').click(function() {
                
                var list_id = $(this).closest('.dx-cms-register-constructor').data('list-id');
                
                // if list_id = 0 then try to save with AJAX (must be register title provided)
                // for new registers user object_id = 140
                
                new_list_item(list_id, 0, 0, "", "");
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
            
            $(".dx-cms-nested-list").nestable();
           
            $(".dx-wizard-btn").click(function() {
                // perform ajax save or current panel data
                $(".dx-constructor-wizard").removeClass($(this).attr('data-current-step')).addClass($(this).attr('data-next-step'));
                
                $(this).attr('data-current-step', $(this).attr('data-next-step'));
                
                if ($(this).attr('data-next-step') == 'dx-step-fields') {
                    $('.mt-element-step .dx-step-naming').removeClass('active').addClass('done');
                    $('.mt-element-step .dx-step-fields').addClass('active').removeClass('done');
                    $(this).attr('data-next-step', 'dx-step-roles');
                }else if ($(this).attr('data-next-step') == 'dx-step-roles') {
                    $('.mt-element-step .dx-step-fields').removeClass('active').addClass('done');
                    $('.mt-element-step .dx-step-roles').addClass('active').removeClass('done');
                    $(this).attr('data-next-step', 'dx-step-navigation');
                }else if ($(this).attr('data-next-step') == 'dx-step-navigation') {
                    $('.mt-element-step .dx-step-roles').removeClass('active').addClass('done');
                    $('.mt-element-step .dx-step-navigation').addClass('active').removeClass('done');
                    $(this).attr('data-next-step', 'dx-step-naming');
                }else if ($(this).attr('data-next-step') == 'dx-step-naming') {
                    $('.mt-element-step .dx-step-navigation').removeClass('active').addClass('done');
                    $('.mt-element-step .dx-step-naming').addClass('active').removeClass('done');
                    $(this).attr('data-next-step', 'dx-step-fields');
                }
                
                
            });
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
              
                <div class="mt-element-step">
                    <div class="row step-line">
                        <div class="dx-step-naming col-md-3 mt-step-col first active">
                            <div class="mt-step-number bg-white">1</div>
                            <div class="mt-step-title uppercase font-grey-cascade">Naming</div>
                            <div class="mt-step-content font-grey-cascade">Register & item titles</div>
                        </div>
                        <div class="dx-step-fields col-md-3 mt-step-col">
                            <div class="mt-step-number bg-white">2</div>
                            <div class="mt-step-title uppercase font-grey-cascade">Fields</div>
                            <div class="mt-step-content font-grey-cascade">View & form fields</div>
                        </div>
                        <div class="dx-step-roles col-md-3 mt-step-col">
                            <div class="mt-step-number bg-white">3</div>
                            <div class="mt-step-title uppercase font-grey-cascade">Rights</div>
                            <div class="mt-step-content font-grey-cascade">Assign users roles</div>
                        </div>
                        <div class="dx-step-navigation col-md-3 mt-step-col last">
                            <div class="mt-step-number bg-white">4</div>
                            <div class="mt-step-title uppercase font-grey-cascade">Navigation</div>
                            <div class="mt-step-content font-grey-cascade">Setup link in menu</div>
                        </div>
                    </div>

                </div>
              
              <div class='dx-constructor-wizard dx-step-naming'>
                <div class="row dx-panel-naming" style='width: 50%; margin: 0 auto;'>
                  <div class="form-group has-feedback dx-form-field-line col-lg-12 col-md-12 col-sm-12 col-xs-12" dx_fld_name_form="skype" data-field-id="2106">
                                  <label for="32f6de8d-dfa2-407a-bb43-cac4a6ece0b5_skype" style="vertical-align: top; margin-right: 10px;">
                                              <span class="dx-fld-title">Register name</span>
                                          </label>

                                  <input class="form-control" autocomplete="off" type="text" id="32f6de8d-dfa2-407a-bb43-cac4a6ece0b5_skype" name="skype" maxlength="100" value="">
                                  <span class="glyphicon form-control-feedback" aria-hidden="true"></span>

                                  <div class="help-block with-errors" style="position: absolute; margin-top: -2px; max-height: 20px; overflow-y: hidden;"></div>

                              </div>
                    <div class="form-group has-feedback dx-form-field-line col-lg-12 col-md-12 col-sm-12 col-xs-12" dx_fld_name_form="skype" data-field-id="2106">
                                  <label for="32f6de8d-dfa2-407a-bb43-cac4a6ece0b5_skype" style="vertical-align: top; margin-right: 10px;">
                                              <span class="dx-fld-title">Item name</span>
                                          </label>

                                  <input class="form-control" autocomplete="off" type="text" id="32f6de8d-dfa2-407a-bb43-cac4a6ece0b5_skype" name="skype" maxlength="100" value="">
                                  <span class="glyphicon form-control-feedback" aria-hidden="true"></span>

                                  <div class="help-block with-errors" style="position: absolute; margin-top: -2px; max-height: 20px; overflow-y: hidden;"></div>

                              </div>
                </div>
                  
                <div class="row dx-panel-navigation" style='width: 70%; margin: 0 auto;'>                  
                    @include('constructor.menu_field')
                </div>
                  
                <div class="inbox dx-panel-fields">
                    
                  <div class="row">
                    <div class="col-md-3">
                          @include('constructor.sidebar')
                    </div>
                    <div class="col-md-9">    
                        @include('constructor.form_cells')
                    </div>
                  </div>                  
                    
                </div>
                @include('constructor.roles')
                <br><br>
                <hr>
                <div style='text-align: center;'>
                    <button type="button" class="btn btn-primary dx-wizard-btn" style='margin: 0 auto;' data-current-step='dx-step-naming' data-next-step='dx-step-fields'>
                          Save & next <i class="fa fa-arrow-right"></i>
                    </button>
                </div>
          </div>
        </div>   
    </div>  
@endsection