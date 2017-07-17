<a href="javascript:;" class="btn btn-primary red dx-constructor-add-role">
  <i class='fa fa-plus'></i> {{ trans('constructor.add_role') }}
</a>
<div class='modal fade' aria-hidden='true' id='list_item_view_form_01f604f9-d35d-4360-b9b5-36efde7d0935' role='dialog' data-backdrop='static'>
  <div class='modal-dialog modal-lg'>
    <div class='modal-content'>
      <div class='modal-header dx-form-header'>
        <button type='button' class='close dx-form-close-btn' data-dismiss='modal' title="Close">
          <i class='fa fa-times' style="color: white"></i></button>
        <h4 class='modal-title' style="color: white;"> Role register &nbsp;<span class='badge'>New</span></h4></div>
      <div class='modal-header' style='background-color: #EEEEEE; border-bottom: 1px solid #c1c1c1; min-height: 55px; display: none;' id="top_toolbar_list_item_view_form_01f604f9-d35d-4360-b9b5-36efde7d0935">
        <div class="dx_form_btns_left">
          <a href="http://mindwo.dev/web/viewer.html?file=http://mindwo.dev/get_form_pdf_0_23.pdf" target="_blank" style="color: #333!important; text-decoration: none!important;">
            <button class='btn btn-white'><i class="fa fa-file-pdf-o"></i> To PDF</button>
          </a></div>
      </div>
      <div class='modal-body'>
        <div class='row dx-form-row'>
          <form class="form-horizontal" method='POST' data-toggle="validator" id='item_edit_form_01f604f9-d35d-4360-b9b5-36efde7d0935'>
            <div class="dx-cms-form-fields-section" style='margin-left: 20px;' dx_attr="form_fields" dx_is_init="0" dx_form_id="01f604f9-d35d-4360-b9b5-36efde7d0935" dx_grid_id="" dx_is_wf_btn="0" dx_list_id="23" dx_is_custom_approve="0" data-parent-field-id="105" data-parent-item-id="254" data-is-edit-mode="1">
              <div class='form-group has-feedback dx-form-field-line col-lg-12' dx_fld_name_form="list_id" data-field-id="105">
                <label for="01f604f9-d35d-4360-b9b5-36efde7d0935_list_id" style="vertical-align: top; margin-right: 10px;">
                  <span class='dx-fld-title'>Register</span> <span style="color: red"> *</span> </label>
                <div class="input-group dx-rel-id-field" id="01f604f9-d35d-4360-b9b5-36efde7d0935_list_id_rel_field" style="width: 100%;" data-is-init="0" data-form-url="form" data-rel-list-id="3" data-rel-field-id="1" data-item-field="list_id" data-binded-field-name="user_field_id" data-binded-field-id="161" data-binded-rel-field-id="17" data-item-value="254" data-frm-uniq-id="01f604f9-d35d-4360-b9b5-36efde7d0935" data-trans-must-choose="Please choose wich dropdown item to edit!">
                  <input type=hidden id='01f604f9-d35d-4360-b9b5-36efde7d0935_list_id' value='254' name='list_id'/>
                  <input class='form-control dx-rel-id-text' readonly value='Received correspondence' dx_fld_name='list_id' dx_binded_field_id='161' dx_binded_rel_field_id='17'/>
                  <span class="input-group-btn" style="padding-left: 1px;">                            <button class="btn btn-white dx-rel-id-view-btn" type="button" title="View item" data-item-id="254" style="border: 1px solid #c2cad8!important; margin-left: -2px!important;"><i class='fa fa-external-link'></i></button>                                            </span>
                </div>
                <div class="help-block with-errors" style="position: absolute; margin-top: -2px; max-height: 20px; overflow-y: hidden;"></div>
              </div>
              <div class='form-group has-feedback dx-form-field-line col-lg-12' dx_fld_name_form="role_id" data-field-id="104">
                <label for="01f604f9-d35d-4360-b9b5-36efde7d0935_role_id" style="vertical-align: top; margin-right: 10px;">
                  <span class='dx-fld-title'>Role</span> <span style="color: red"> *</span> </label>
                <div class="input-group dx-rel-id-field" id="01f604f9-d35d-4360-b9b5-36efde7d0935_role_id_rel_field" style="width: 100%;" data-is-init="0" data-form-url="form" data-rel-list-id="20" data-rel-field-id="92" data-item-field="role_id" data-binded-field-name="" data-binded-field-id="" data-binded-rel-field-id="" data-item-value="" data-frm-uniq-id="01f604f9-d35d-4360-b9b5-36efde7d0935" data-trans-must-choose="Please choose wich dropdown item to edit!">
                  <select class='form-control dx-not-focus' id='01f604f9-d35d-4360-b9b5-36efde7d0935_role_id' dx_fld_name='role_id' name='role_id' required data-foo="bar" dx_binded_field_id='' dx_binded_rel_field_id=''>
                    <option value=0></option>
                    <option value='28'> Correspondence - Watching</option>
                    <option value='31'>Board meetings - Editing</option>
                    <option value='32'>Board meetings - Watching</option>
                    <option value='36'>Company tasks</option>
                    <option value='27'>Contracts - Editing</option>
                    <option value='29'>Contracts - Watching</option>
                    <option value='34'>Contracts signing organization</option>
                    <option value='26'>Correspondence - Editing</option>
                    <option value='23'>Document management</option>
                    <option value='25'>Document registration</option>
                    <option value='39'>HR</option>
                    <option value='43'>HR classifiers</option>
                    <option value='41'>HR domains</option>
                    <option value='42'>HR emails</option>
                    <option value='40'>HR guest</option>
                    <option value='44'>HR master keys</option>
                    <option value='33'>Jurist</option>
                    <option value='37'>Office manager</option>
                    <option value='30'>Orders - economic - editing</option>
                    <option value='35'>Own task execution</option>
                    <option value='38'>Personnel and Administration Department Manager</option>
                    <option value='24'>Portal content management</option>
                    <option value='1'>System management</option>
                    <option value='2'>User management</option>
                  </select>
                  <span class="glyphicon form-control-feedback" aria-hidden="true" style="margin-right: 40px;"></span>
                  <span class="input-group-btn" style="padding-left: 1px;">                                                    <button class="btn btn-white dx-rel-id-edit-btn" type="button" title="Edit selected item. Attention: changes will be applied to all records where this item is used!" style="border: 1px solid #c2cad8!important; margin-left: -2px!important;" data-readmode="0" data-item-id=""><i class='fa fa-pencil-square-o'></i></button>                <button class="btn btn-white dx-rel-id-add-btn" type="button" title="Add new item" style="border: 1px solid #c2cad8!important; margin-left: -2px!important;"><i class='fa fa-plus'></i></button>                    </span>
                </div>
                <div class="help-block with-errors" style="position: absolute; margin-top: -2px; max-height: 20px; overflow-y: hidden;"></div>
              </div>
              <div class='form-group has-feedback dx-form-field-line col-lg-12' dx_fld_name_form="is_new_rights" data-field-id="428">
                <label for="01f604f9-d35d-4360-b9b5-36efde7d0935_is_new_rights" style="vertical-align: top; margin-right: 10px;">
                  <span class='dx-fld-title'>Can enter a new</span> <span style="color: red"> *</span> </label>
                <div><input type="checkbox" class="dx-bool" data-off-text="No" data-on-text="Yes" name='is_new_rights'/>
                </div>
                <div class="help-block with-errors" style="position: absolute; margin-top: -2px; max-height: 20px; overflow-y: hidden;"></div>
              </div>
              <div class='form-group has-feedback dx-form-field-line col-lg-12' dx_fld_name_form="is_edit_rights" data-field-id="106">
                <label for="01f604f9-d35d-4360-b9b5-36efde7d0935_is_edit_rights" style="vertical-align: top; margin-right: 10px;">
                  <span class='dx-fld-title'>Is editing rights</span> <span style="color: red"> *</span> </label>
                <div>
                  <input type="checkbox" class="dx-bool" data-off-text="No" data-on-text="Yes" name='is_edit_rights'/>
                </div>
                <div class="help-block with-errors" style="position: absolute; margin-top: -2px; max-height: 20px; overflow-y: hidden;"></div>
              </div>
              <div class='form-group has-feedback dx-form-field-line col-lg-12' dx_fld_name_form="is_delete_rights" data-field-id="427">
                <label for="01f604f9-d35d-4360-b9b5-36efde7d0935_is_delete_rights" style="vertical-align: top; margin-right: 10px;">
                  <span class='dx-fld-title'>Is delete rights</span> <span style="color: red"> *</span> </label>
                <div>
                  <input type="checkbox" class="dx-bool" data-off-text="No" data-on-text="Yes" name='is_delete_rights'/>
                </div>
                <div class="help-block with-errors" style="position: absolute; margin-top: -2px; max-height: 20px; overflow-y: hidden;"></div>
              </div>
              <div class='form-group has-feedback dx-form-field-line col-lg-12' dx_fld_name_form="is_import_rights" data-field-id="2556">
                <label for="01f604f9-d35d-4360-b9b5-36efde7d0935_is_import_rights" style="vertical-align: top; margin-right: 10px;">
                  <span class='dx-fld-title'>Data import from Excel</span> </label>
                <div>
                  <input type="checkbox" class="dx-bool" data-off-text="No" data-on-text="Yes" name='is_import_rights'/>
                </div>
                <div class="help-block with-errors" style="position: absolute; margin-top: -2px; max-height: 20px; overflow-y: hidden;"></div>
              </div>
              <div class='form-group has-feedback dx-form-field-line col-lg-12' dx_fld_name_form="is_view_rights" data-field-id="2557">
                <label for="01f604f9-d35d-4360-b9b5-36efde7d0935_is_view_rights" style="vertical-align: top; margin-right: 10px;">
                  <span class='dx-fld-title'>Views configuration</span> </label>
                <div>
                  <input type="checkbox" class="dx-bool" data-off-text="No" data-on-text="Yes" name='is_view_rights'/>
                </div>
                <div class="help-block with-errors" style="position: absolute; margin-top: -2px; max-height: 20px; overflow-y: hidden;"></div>
              </div>
              <div class='form-group has-feedback dx-form-field-line col-lg-12' dx_fld_name_form="user_field_id" data-field-id="161">
                <label for="01f604f9-d35d-4360-b9b5-36efde7d0935_user_field_id" style="vertical-align: top; margin-right: 10px;">
                  <span class='dx-fld-title'>Users entry</span> </label>
                <div class="input-group dx-rel-id-field" id="01f604f9-d35d-4360-b9b5-36efde7d0935_user_field_id_rel_field" style="width: 100%;" data-is-init="0" data-form-url="form" data-rel-list-id="7" data-rel-field-id="23" data-item-field="user_field_id" data-binded-field-name="" data-binded-field-id="" data-binded-rel-field-id="" data-item-value="" data-frm-uniq-id="01f604f9-d35d-4360-b9b5-36efde7d0935" data-trans-must-choose="Please choose wich dropdown item to edit!">
                  <select class='form-control dx-not-focus' id='01f604f9-d35d-4360-b9b5-36efde7d0935_user_field_id' dx_fld_name='user_field_id' name='user_field_id' data-foo="bar" dx_binded_field_id='' dx_binded_rel_field_id=''>
                    <option value=0></option>
                    <option value='1551'>About</option>
                    <option value='1552'>Address</option>
                    <option value='1562'>Answer document</option>
                    <option value='1544'>Client number</option>
                    <option value='1563'>Department</option>
                    <option value='1555'>Document type</option>
                    <option value='1556'>Due date</option>
                    <option value='1545'>File</option>
                    <option value='1539'>ID</option>
                    <option value='1560'>Is on controle</option>
                    <option value='2019'>Need an answer</option>
                    <option value='1561'>Notes</option>
                    <option value='1554'>Number of copies</option>
                    <option value='1553'>Number of pages</option>
                    <option value='2018'>Performer</option>
                    <option value='1548'>Received from</option>
                    <option value='1543'>Receiving date</option>
                    <option value='1566'>Register</option>
                    <option value='1541'>Registration date</option>
                    <option value='1542'>Registration number</option>
                    <option value='1557'>Resolution</option>
                    <option value='1558'>Resolution file</option>
                    <option value='2017'>Workflow status</option>
                  </select>
                  <span class="glyphicon form-control-feedback" aria-hidden="true" style="margin-right: 40px;"></span>
                  <span class="input-group-btn" style="padding-left: 1px;">                                                    <button class="btn btn-white dx-rel-id-edit-btn" type="button" title="Edit selected item. Attention: changes will be applied to all records where this item is used!" style="border: 1px solid #c2cad8!important; margin-left: -2px!important;" data-readmode="0" data-item-id=""><i class='fa fa-pencil-square-o'></i></button>                <button class="btn btn-white dx-rel-id-add-btn" type="button" title="Add new item" style="border: 1px solid #c2cad8!important; margin-left: -2px!important;"><i class='fa fa-plus'></i></button>                    </span>
                </div>
                <div class="help-block with-errors" style="position: absolute; margin-top: -2px; max-height: 20px; overflow-y: hidden;"></div>
              </div>
              <div class='form-group has-feedback dx-form-field-line col-lg-12' dx_fld_name_form="is_subord" data-field-id="2484">
                <label for="01f604f9-d35d-4360-b9b5-36efde7d0935_is_subord" style="vertical-align: top; margin-right: 10px;">
                  <i class='fa fa-question-circle dx-form-help-popup' title='Can access direct subordinates and all sub-levels subordinates data' style='cursor: help;'></i>&nbsp;
                  <span class='dx-fld-title'>Access subordinates data</span> </label>
                <div><input type="checkbox" class="dx-bool" data-off-text="No" data-on-text="Yes" name='is_subord'/>
                </div>
                <div class="help-block with-errors" style="position: absolute; margin-top: -2px; max-height: 20px; overflow-y: hidden;"></div>
              </div>
            </div>
            <input type=hidden id='01f604f9-d35d-4360-b9b5-36efde7d0935_edit_form_id' name='edit_form_id' value='21'>
            <input type=hidden id='01f604f9-d35d-4360-b9b5-36efde7d0935_item_id' name='item_id' value='0'></form>
        </div>
      </div>
      <div class='modal-footer' style='border-top: 1px solid #c1c1c1;'>
        <a href='javascript:;' class='dx-cms-history-link pull-left' style='margin-top: 5px; display: none' title='View item changes history'><i class='fa fa-history'></i>
          History&nbsp;</a><span class="badge badge-default pull-left dx-history-badge" style="display: none;">0</span>
        <a href='javascript:;' class='dx-cms-settings-link pull-left' style='margin-top: 5px; margin-left: 15px;' title='form.hint_settings'><i class='fa fa-cog'></i></a>
        <button type='button' class='btn btn-primary dx-btn-save-form' id='btn_save_01f604f9-d35d-4360-b9b5-36efde7d0935'>
          Save
        </button>
        <button type='button' class='btn btn-white dx-btn-cancel-form' data-dismiss='modal'><i class='fa fa-undo'></i>
          Cancel
        </button>
      </div>
      <script type='text/javascript'>                register_form('list_item_view_form_01f604f9-d35d-4360-b9b5-36efde7d0935', 0);            </script>
      <script type='text/javascript'>                                       $('#list_item_view_form_01f604f9-d35d-4360-b9b5-36efde7d0935').on('show.bs.modal', function() { $('#list_item_view_form_01f604f9-d35d-4360-b9b5-36efde7d0935 .modal-body').css('overflow-y', 'auto'); });
		  $('#list_item_view_form_01f604f9-d35d-4360-b9b5-36efde7d0935').on('hidden.bs.modal', function(e)
		  {
			  var arr_callbacks = get_form_callbacks('01f604f9-d35d-4360-b9b5-36efde7d0935');
			  if(typeof arr_callbacks != 'undefined')
			  {
				  if(typeof arr_callbacks.after_close != 'undefined' && $('#list_item_view_form_01f604f9-d35d-4360-b9b5-36efde7d0935').length > 0)
				  { arr_callbacks.after_close.call(this, $('#list_item_view_form_01f604f9-d35d-4360-b9b5-36efde7d0935')); }
			  }
			  unregister_form('list_item_view_form_01f604f9-d35d-4360-b9b5-36efde7d0935');
			  $('#list_item_view_form_01f604f9-d35d-4360-b9b5-36efde7d0935').remove();
			  $('#form_init_wf_01f604f9-d35d-4360-b9b5-36efde7d0935').remove();
			  $('#form_init_wf_approver_01f604f9-d35d-4360-b9b5-36efde7d0935').remove();
			  toastr.clear();
		  });
		  $('#btn_save_01f604f9-d35d-4360-b9b5-36efde7d0935').click(function dx_btn_save_click(event)
		  {
			  event.stopPropagation();
			  $('#item_edit_form_01f604f9-d35d-4360-b9b5-36efde7d0935').validator('validate');
			  if($('#item_edit_form_01f604f9-d35d-4360-b9b5-36efde7d0935').find(".with-errors ul").length > 0)
			  {
				  notify_err(Lang.get('errors.form_validation_err'));
				  return false;
			  }                                                        // Calls encryption function which encryptes data and on callback it executes again save function                            if(!event.encryptionFinished || event.encryptionFinished == false){                                var cryptoFields = $('input.dx-crypto-field,textarea.dx-crypto-field,input.dx-crypto-field-file', $(this).closest('.modal-content'));                                                                window.DxCrypto.encryptFields(cryptoFields, event, function(event){                                    dx_btn_save_click(event);                                });                                                                return;                            }                                                        var arr_callbacks = get_form_callbacks('01f604f9-d35d-4360-b9b5-36efde7d0935');                            if (typeof arr_callbacks != 'undefined') {                                if (typeof arr_callbacks.before_save != 'undefined') {                                    if (!arr_callbacks.before_save.call(this, $( '#list_item_view_form_01f604f9-d35d-4360-b9b5-36efde7d0935' ))) {                                        return false;                                    }                                }                            }                            save_list_item('item_edit_form_01f604f9-d35d-4360-b9b5-36efde7d0935', '',23, 105, 254, '', arr_callbacks);//replace                                                       /* var cryptoFields = $('.dx-crypto-field', $(this).closest('.modal-content'));                            window.DxCrypto.decryptFields(cryptoFields);  */                                              });                        $('#item_edit_form_01f604f9-d35d-4360-b9b5-36efde7d0935').validator({                            custom : {                                foo: function($el)                                 {                                     if (!($el.val()>0) && $el.attr('required'))                                    {                                        return false;                                    }                                    return true;                                },                                cbotext: function($el) {                                    if (!($el.val().length > 0) && $el.attr('required'))                                    {                                        return false;                                    }                                    return true;                                },                                auto: function($el)                                {                                    alert($el.val());                                    return false;                                }                            },                            errors: {                                foo: 'Value not set!',                                auto: 'Value not set!',                                cbotext: 'Value not set!'                            },                            feedback: {                                success: 'glyphicon-ok',                                error: 'glyphicon-alert'                            }                        });                                                                                var arr_callbacks = get_form_callbacks('01f604f9-d35d-4360-b9b5-36efde7d0935');                    if (typeof arr_callbacks != 'undefined') {                        if (typeof arr_callbacks.before_show != 'undefined') {                            arr_callbacks.before_show.call(this, $( '#list_item_view_form_01f604f9-d35d-4360-b9b5-36efde7d0935' ));                        }                    }                                                                                $( '#list_item_view_form_01f604f9-d35d-4360-b9b5-36efde7d0935' ).modal('show');                                                    </script>
    </div>
  </div>
</div>
<div class='modal fade dx-cancel-wf-form' aria-hidden='true' id='wf_cancel_form_01f604f9-d35d-4360-b9b5-36efde7d0935' role='dialog' aria-hidden='true' data-backdrop='static' style="z-index: 999999;" data-is-init="0" data-list-id="23" data-item-id="0" data-grid-id="">
  <div class='modal-dialog modal-md'>
    <div class='modal-content'>
      <div class='modal-header dx-form-header'>
        <button type='button' class='close dx-form-close-btn' data-dismiss='modal' title="Close">
          <i class='fa fa-times' style="color: white"></i></button>
        <h4 class='modal-title' style="color: white;"> Cancel workflow </h4></div>
      <div class='modal-body'>
        <div class="form-horizontal">
          <div class='form-group has-feedback'><label class='col-lg-4 control-label'>
              Comment<span style="color: red"> *</span> </label>
            <div class='col-lg-8'>
              <div class="input-group" style="width: 100%;">
                <textarea class='form-control' name='comment' rows='4' maxlength='4000'></textarea></div>
            </div>
          </div>
        </div>
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-primary dx-btn-cancel-wf'>Cancel workflow</button>
        <button type='button' class='btn btn-white' data-dismiss='modal'><i class='fa fa-sign-out'></i>&nbsp;Close
        </button>
      </div>
    </div>
  </div>
</div>