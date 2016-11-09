<?php
$emp_docs_list = App\Libraries\DBHelper::getListByTable('in_employees_personal_docs');

if ($emp_docs_list) {
    $emp_docs_list_id = $emp_docs_list->id;
    $emp_docs_fld_id = DB::table('dx_lists_fields')->select('id')
                    ->where('list_id', '=', $emp_docs_list->id)
                    ->where('db_name', '=', 'file_name')
                    ->first()->id;
} else {
    $emp_docs_list_id = 0;
    $emp_docs_fld_id = 0;
}

if ($user->doc_country_id) {
    $selected_country_id = $user->doc_country_id;
} elseif ($user->country_id) {
    $selected_country_id = $user->country_id;
} else {
    $selected_country_id = 0;
}
?>
<div id="dx-emp-pers-docs-panel" data-user-id="{{ $user->id }}" 
     data-date-format="{{ config('dx.txt_date_format') }}" 
     data-locale='{{ Lang::locale() }}'
     data-emp-docs-list-id='{{ $emp_docs_list_id }}'
     data-emp-docs-fld-id='{{ $emp_docs_fld_id }}'>
    <div class='row'>
        <div class='col-lg-4 col-md-12'>
            <select class='form-control dx-not-focus' id="dx-emp-pers-docs-country">
                @foreach (App\Models\Country::all() as $country)            
                <option value="{{$country->id}}" {{ $selected_country_id == $country->id ? 'selected' : '' }}>{{ $country->title }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row visible-lg" style="margin-top: 10px;">
        <div class="col-lg-2">   
            {{ trans('employee.personal_docs.personal_doc_type') }}
        </div>
        <div class="col-lg-2">   
            {{ trans('employee.personal_docs.doc_nr') }}
        </div>
        <div class="col-lg-2">   
            {{ trans('employee.personal_docs.valid_to') }}
        </div>
        <div class="col-lg-2">   
            {{ trans('employee.personal_docs.publisher') }}
        </div>
        <div class="col-lg-3">   
            {{ trans('employee.personal_docs.file') }}
        </div>
    </div>
    <div id="dx-emp-pers-docs-table" style="margin-top: 10px;"></div>
    <div id='dx-emp-pers-docs-table-history' style="display: none;"></div>
    <div id="dx-emp-pers-docs-new-row" style="display: none;">
        <div class="dx-emp-pers-docs-row row">
            <input class="dx-emp-pers-docs-id-input" type="hidden" />
            <input class="dx-emp-pers-docs-type-input" type="hidden" />    
            <div class="col-lg-2">
                <div class="hidden-lg" style='margin-top: 5px;'>{{ trans('employee.personal_docs.personal_doc_type') }}</div>
                <label class="dx-emp-pers-docs-type-label" style="padding-top: 10px; font-weight: bold; text-transform: uppercase;"></label>
            </div>
            <div class="col-lg-2"> 
                <div class="hidden-lg" style='margin-top: 5px;'>{{ trans('employee.personal_docs.doc_nr') }}</div>
                <input class="form-control dx-emp-pers-docs-docnr-input" type="text" maxlength="500" />  
            </div>
            <div class="col-lg-2">           
                <div class="hidden-lg" style='margin-top: 5px;'>{{ trans('employee.personal_docs.valid_to') }}</div>
                <div class='input-group'>
                    <span class='input-group-btn'>
                        <button type='button' class='btn btn-white dx-emp-pers-docs-validto-input-calc'><i class='fa fa-calendar'></i></button>
                    </span>
                    <input class='form-control dx-emp-pers-docs-validto-input' type='text' />
                </div>
            </div>
            <div class="col-lg-2">  
                <div class="hidden-lg" style='margin-top: 5px;'>{{ trans('employee.personal_docs.publisher') }}</div>
                <input class="form-control dx-emp-pers-docs-publisher-input" type="text" maxlength="500" />   
            </div>
            <div class="col-lg-3">
                <div class="hidden-lg" style='margin-top: 5px;'>{{ trans('employee.personal_docs.file') }}</div>
                <div class="dx-emp-pers-docs-file-input">
                    <div class='fileinput fileinput-new input-group' data-provides='fileinput' style="width: 100%;" dx_file_field_id="{{ $emp_docs_fld_id }}">
                        <div class='form-control'>
                            <i class='glyphicon glyphicon-file fileinput-exists'></i> 
                            <span class='fileinput-filename truncate dx-emp-pers-docs-file-input-download' style="max-width: 130px;">                                                    
                            </span>
                        </div>  
                        <span class='input-group-addon btn btn-default btn-file dx-emp-pers-docs-file-input-set-btn'>
                            <span class='fileinput-new'>{{ trans('fields.btn_set') }}</span>
                            <span class='fileinput-exists'>{{ trans('fields.btn_change') }}</span>
                            <input type='file' name='file_guid' class="dx-emp-pers-docs-file-input-file" />
                            <input class='fileinput-remove-mark dx-emp-pers-docs-file-input-remove' type='hidden' value='0' name = 'file_guid_remove' />
                        </span>
                        <a href='#' class='input-group-addon btn btn-default fileinput-exists dx-emp-pers-docs-file-input-remove-btn' data-dismiss='fileinput'>{{ trans('fields.btn_remove') }}</a>
                        <input class="dx-emp-pers-docs-file-input-isset" type="hidden" name='file_guid_is_set' value="0" />                        
                    </div>
                </div>
            </div>
            <div class="col-lg-1">
                <button class="btn btn-white dx-emp-pers-docs-clear-btn" title="{{ trans('employee.personal_docs.clear_doc') }}">
                    <i class='fa fa-trash-o'></i>
                </button>
            </div>
            <div class='hidden-lg'>
                <hr />
            </div>
        </div>
    </div>
</div>
<script>
    window.DxEmpPersDocs = window.DxEmpPersDocs || {
        rowCount: 0,
        userId: 0,
        dateFormat: '',
        locale: 'en',
        empDocListId: 0,
        empDocFldId: 0,
        isInit: false,
        viewClone: '',
        callbackOnInitiSuccess: function (data) {},
        callbackOnSaveSuccess: function (data) {},
        callbackOnError: function (data) {},
        /**
         * Initializes component
         * @returns {undefined}
         */
        init: function (callbackOnInitiSuccess) {
            if (callbackOnInitiSuccess) {
                window.DxEmpPersDocs.callbackOnInitiSuccess = callbackOnInitiSuccess;
            } else {
                window.DxEmpPersDocs.callbackOnInitiSuccess = function () {};
            }

            window.DxEmpPersDocs.userId = ($('#dx-emp-pers-docs-panel').attr('data-user-id') == '' ? 0 : $('#dx-emp-pers-docs-panel').attr('data-user-id'));
            window.DxEmpPersDocs.dateFormat = $('#dx-emp-pers-docs-panel').attr('data-date-format');
            window.DxEmpPersDocs.locale = $('#dx-emp-pers-docs-panel').attr('data-locale');
            window.DxEmpPersDocs.empDocListId = $('#dx-emp-pers-docs-panel').attr('data-emp-docs-list-id');
            window.DxEmpPersDocs.empDocFldId = $('#dx-emp-pers-docs-panel').attr('data-emp-docs-fld-id');
            $("#dx-emp-pers-docs-country").change(window.DxEmpPersDocs.onChangeCountry);
            window.DxEmpPersDocs.loadEmployeeData();
        },
        enterEditMode: function () {
            window.DxEmpPersDocs.viewClone = $('#dx-emp-pers-docs-panel').clone(true, true);
        },
        cancelEditMode: function () {
            $('#dx-emp-pers-docs-panel').replaceWith(window.DxEmpPersDocs.viewClone);
            window.DxEmpPersDocs.viewClone = null;
            window.DxEmpPersDocs.toggleDisable(true);
        },
        loadEmployeeData: function () {
            $.ajax({
                url: '/employee/personal_docs/get/employee_docs/' + window.DxEmpPersDocs.userId,
                type: "get",
                success: window.DxEmpPersDocs.onSuccessLoadEmployeeData,
                error: window.DxEmpPersDocs.onAjaxError
            });
        },
        onSuccessLoadEmployeeData: function (data) {
            if(data != ''){
                var data_rows = JSON.parse(data);
                // Prepares dropdown list options
                for (var i = 0; i < data_rows.length; i++) {
                    window.DxEmpPersDocs.createNewDocRow(false, data_rows[i]);
                }
            }

            $("#dx-emp-pers-docs-country").trigger('change');
        },
        createNewDocRow: function (is_new, data) {
            // Gets template for row and converts it as jquery object
            var new_row_html = $($('#dx-emp-pers-docs-new-row').html());
            if (is_new) {
                new_row_html = window.DxEmpPersDocs.setDocTypeValue(new_row_html, data);
            } else {
                new_row_html = window.DxEmpPersDocs.setValues(new_row_html, data);
            }

            // Append row to table
            if (is_new) {
                $('#dx-emp-pers-docs-table').append(new_row_html);
            } else {
                $('#dx-emp-pers-docs-table-history').append(new_row_html);
            }

            // Bind all rquired events for row elements
            window.DxEmpPersDocs.bindDocRowEvenets(new_row_html);
            // Increase row counter
            window.DxEmpPersDocs.rowCount++;
        },
        initValidToDatePicker: function (new_row_html, value) {
            var picker = new_row_html.find('.dx-emp-pers-docs-validto-input');
            picker.attr('id', 'dx-emp-pers-docs-validto-input-' + window.DxEmpPersDocs.rowCount);
            picker.val(value);
            picker.datetimepicker({
                lang: window.DxEmpPersDocs.locale,
                format: window.DxEmpPersDocs.dateFormat,
                timepicker: 0,
                dayOfWeekStart: 1,
                closeOnDateSelect: true
            });
            new_row_html.find('.dx-emp-pers-docs-validto-input-calc').click({picker_num: window.DxEmpPersDocs.rowCount}, function (e) {
                jQuery('#dx-emp-pers-docs-validto-input-' + e.data.picker_num).datetimepicker('show');
            });
            return new_row_html;
        },
        setDocTypeValue: function (new_row_html, data_row) {
            // Prepare "valid to" date picker            
            new_row_html = window.DxEmpPersDocs.initValidToDatePicker(new_row_html, '');
            new_row_html.attr('id', 'dx-emp-pers-docs-row-' + data_row.id);
            new_row_html.find('.dx-emp-pers-docs-type-input').val(data_row.id);
            new_row_html.find('.dx-emp-pers-docs-type-label').html(data_row.name);
            return new_row_html;
        },
        setValues: function (new_row_html, data_row) {
            new_row_html = window.DxEmpPersDocs.initValidToDatePicker(new_row_html, data_row.valid_to);
            new_row_html.attr('id', 'dx-emp-pers-docs-row-' + data_row.doc_id);
            new_row_html.find('.dx-emp-pers-docs-id-input').val(data_row.id);
            new_row_html.find('.dx-emp-pers-docs-type-input').val(data_row.doc_id);
            new_row_html.find('.dx-emp-pers-docs-type-label').html(data_row.personal_document.name);
            new_row_html.find('.dx-emp-pers-docs-docnr-input').val(data_row.doc_nr);
            new_row_html.find('.dx-emp-pers-docs-publisher-input').val(data_row.publisher);
            window.DxEmpPersDocs.setFileValue(new_row_html, data_row.id, data_row.file_name);

            return new_row_html;
        },
        setFileValue: function (new_row_html, row_id, file_name) {
            if (file_name && file_name != null) {
                var file_link = "<a href='JavaScript: download_file(" + row_id + " , " + window.DxEmpPersDocs.empDocListId + ", " + window.DxEmpPersDocs.empDocFldId + ");'>" + file_name + "</a>";
                new_row_html.find('.dx-emp-pers-docs-file-input-download').html(file_link);
                new_row_html.find('.dx-emp-pers-docs-file-input-isset').val(1);
            }
        },
        clearDocRow: function (e) {
            var row = $(e.target).parents('.dx-emp-pers-docs-row');
            row.find('.dx-emp-pers-docs-id-input').val(0);
            row.find('.dx-emp-pers-docs-docnr-input').val('');
            row.find('.dx-emp-pers-docs-validto-input').val('');
            row.find('.dx-emp-pers-docs-publisher-input').val('');
            row.find('.dx-emp-pers-docs-file-input-remove-btn').trigger('click');
        },
        getDataForSave: function () {
            var rows = $('#dx-emp-pers-docs-table .dx-emp-pers-docs-row');
            var data = {
                user_id: window.DxEmpPersDocs.userId,
                rows: []
            };
            var formData = new FormData();
            for (var i = 0; i < rows.length; i++) {
                var row = $(rows[i]);
                var row_data = {};
                row_data.id = row.find('.dx-emp-pers-docs-id-input').val();
                row_data.document_type = row.find('.dx-emp-pers-docs-type-input').val();
                row_data.publisher = row.find('.dx-emp-pers-docs-publisher-input').val();
                row_data.valid_to = row.find('.dx-emp-pers-docs-validto-input').val();
                row_data.doc_nr = row.find('.dx-emp-pers-docs-docnr-input').val();
                row_data.file_remove = $.trim(row.find('.dx-emp-pers-docs-file-input-download').html()) === '';
                var file = row.find('.dx-emp-pers-docs-file-input-file').prop("files")[0];
                formData.append('file' + i, file);
                data.rows.push(row_data);
            }


            formData.append('doc_country_id', $('#dx-emp-pers-docs-country').val());
            formData.append('data', JSON.stringify(data));
            return formData;
        },
        bindDocRowEvenets: function (new_row_html) {
            new_row_html.find('.dx-emp-pers-docs-clear-btn').click(window.DxEmpPersDocs.clearDocRow);
        },
        onChangeCountry: function (e) {
            var country_id = $(e.target).val();
            $.ajax({
                url: DX_CORE.site_url + 'employee/personal_docs/get/docs_by_country/' + country_id,
                type: "get",
                success: window.DxEmpPersDocs.onSuccessChangeCountry,
                error: window.DxEmpPersDocs.onAjaxError
            });
        },
        onSuccessChangeCountry: function (data) {
            var docs = JSON.parse(data);
            window.DxEmpPersDocs.drawRows(docs);
            window.DxEmpPersDocs.finishInit();
        },
        drawRows: function (docs) {
            // Moves all existing rows to hidden history div
            $('#dx-emp-pers-docs-table').contents().appendTo('#dx-emp-pers-docs-table-history');
            // Iterates through all the document types
            for (var d = 0; d < docs.length; d++) {
                var doc = docs[d];
                var existing_row = $('#dx-emp-pers-docs-row-' + doc.id);
                // Check if row exist in history div
                if (existing_row.length > 0) {
                    // Move existing document type row into visible view
                    existing_row.appendTo('#dx-emp-pers-docs-table');
                } else {
                    // Creates new row if it doesn't exist for document type
                    window.DxEmpPersDocs.createNewDocRow(true, doc);
                }
            }
        },
        finishInit: function () {
            if (!window.DxEmpPersDocs.isInit) {
                window.DxEmpPersDocs.isInit = true;
                if(window.DxEmpPersDocs.userId == 0){
                    window.DxEmpPersDocs.toggleDisable(false);
                }else{
                    window.DxEmpPersDocs.toggleDisable(true);
                }                
                window.DxEmpPersDocs.callbackOnInitiSuccess();
            }
        },
        onClickSaveDocs: function (callbackOnSaveSuccess, callbackOnError) {
            if (callbackOnSaveSuccess) {
                window.DxEmpPersDocs.callbackOnSaveSuccess = callbackOnSaveSuccess;
            } else {
                window.DxEmpPersDocs.callbackOnSaveSuccess = function () {};
            }
            if (callbackOnError) {
                window.DxEmpPersDocs.callbackOnError = callbackOnError;
            } else {
                window.DxEmpPersDocs.callbackOnError = function () {};
            }

            var form_data = window.DxEmpPersDocs.getDataForSave();
            $.ajax({
                url: DX_CORE.site_url + 'employee/personal_docs/save',
                data: form_data,
                type: "post",
                processData: false,
                dataType: "json",
                contentType: false,
                success: window.DxEmpPersDocs.onSuccessSave,
                error: window.DxEmpPersDocs.onAjaxError
            });
        },
        onSuccessSave: function (data_rows) {
            // Set id for rows and update file input control value
            for (var i = 0; i < data_rows.length; i++) {
                var data_row = data_rows[i];
                var row = $('#dx-emp-pers-docs-row-' + data_row.doc_id);

                if (row.length > 0) {
                    row.find('.dx-emp-pers-docs-id-input').val(data_row.id);
                    row.find('.dx-emp-pers-docs-file-input-remove-btn').trigger('click');
                    window.DxEmpPersDocs.setFileValue(row, data_row.id, data_row.file_name);
                }
            }

            $('#dx-emp-pers-docs-table-history').empty();
            window.DxEmpPersDocs.callbackOnSaveSuccess();
        },
        onAjaxError: function (data) {
            window.DxEmpPersDocs.finishInit();
            window.DxEmpPersDocs.callbackOnError();
        },
        toggleDisable: function (is_disabled) {
            if (!is_disabled) {
                window.DxEmpPersDocs.enterEditMode();
            }

            var rows = $('#dx-emp-pers-docs-table .dx-emp-pers-docs-row');
            for (var i = 0; i < rows.length; i++) {
                var row = $(rows[i]);
                row.find('.dx-emp-pers-docs-publisher-input').prop('disabled', is_disabled);
                row.find('.dx-emp-pers-docs-validto-input').prop('disabled', is_disabled);
                row.find('.dx-emp-pers-docs-validto-input-calc').prop('disabled', is_disabled);
                row.find('.dx-emp-pers-docs-docnr-input').prop('disabled', is_disabled);
                if (is_disabled) {
                    row.find('.dx-emp-pers-docs-file-input-set-btn').hide();
                    if (row.find('.dx-emp-pers-docs-file-input-file').prop("files")[0]) {
                        row.find('.dx-emp-pers-docs-file-input-remove-btn').hide();
                    }

                    row.find('.dx-emp-pers-docs-clear-btn').hide();
                } else {
                    row.find('.dx-emp-pers-docs-validto-input').datetimepicker('destroy');
                    row.find('.dx-emp-pers-docs-validto-input').datetimepicker({
                        lang: window.DxEmpPersDocs.locale,
                        format: window.DxEmpPersDocs.dateFormat,
                        timepicker: 0,
                        dayOfWeekStart: 1,
                        closeOnDateSelect: true
                    });

                    row.find('.dx-emp-pers-docs-file-input-set-btn').show();
                    if (row.find('.dx-emp-pers-docs-file-input-file').prop("files")[0]) {
                        row.find('.dx-emp-pers-docs-file-input-remove-btn').show();
                    }

                    row.find('.dx-emp-pers-docs-clear-btn').show();
                }
            }

            $('#dx-emp-pers-docs-country').prop('disabled', is_disabled);
        }
    }

</script>