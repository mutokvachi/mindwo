<?php
$emp_docs_list = App\Libraries\DBHelper::getListByTable('in_employees_personal_docs');

$emp_docs_fld_id = DB::table('dx_lists_fields')->select('id')
                ->where('list_id', '=', $emp_docs_list->id)
                ->where('db_name', '=', 'file_guid')
                ->first()->id;
?>
<div id="dx-emp-pers-docs-panel" data-user-id="{{ $user_id }}" data-date-format="{{ config('dx.txt_date_format') }}" data-locale='{{ Lang::locale() }}'>
    <div>
        <select id="dx-emp-pers-docs-country">
            @foreach (App\Models\Country::all() as $country)
            <option value="{{$country->id}}">{{ $country->title }}</option>
            @endforeach

        </select>
    </div>
    <div id="dx-emp-pers-docs-table" class="row"></div> 
    <div class="row">
        <div class="col-md-5">
            <button class="btn btn-primary" id="dx-emp-pers-docs-save-btn">{{ trans('employee.personal_docs.save_docs') }}</button>
        </div>
    </div>
    <div id="dx-emp-pers-docs-new-row" style="display: none;">
        <div class="dx-emp-pers-docs-row">
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading dx-emp-pers-docs-type-label"></div>
                    <div class="panel-body">
                        <input class="dx-emp-pers-docs-id-input" type="hidden" />
                        <input class="dx-emp-pers-docs-type-input" type="hidden" />                    
                        <div>                
                            @include('fields.visible', 
                            ['fld_name' => '', 
                            'group_label' => '', 
                            'label_title' => trans('employee.personal_docs.doc_nr'), 
                            'is_required' => 0, 
                            'hint' => '', 
                            'item_htm' => '<input class="form-control dx-emp-pers-docs-docnr-input" type="text" maxlength="500" />'])
                        </div>
                        <div>           
                            <div class='input-group'>
                                <span class='input-group-btn'>
                                    <button type='button' class='btn btn-white dx-emp-pers-docs-validto-input-calc'><i class='fa fa-calendar'></i></button>
                                </span>
                                <input class='form-control dx-emp-pers-docs-validto-input' type='text' />
                            </div>
                        </div>
                        <div>                
                            @include('fields.visible', 
                            ['fld_name' => '', 
                            'group_label' => '', 
                            'label_title' => trans('employee.personal_docs.publisher'), 
                            'is_required' => 0, 
                            'hint' => '', 
                            'item_htm' => '<input class="form-control dx-emp-pers-docs-publisher-input" type="text" maxlength="500" />'])
                        </div>
                        <div>
                            <div class="dx-emp-pers-docs-file-input">

                                <div class='fileinput fileinput-new input-group' data-provides='fileinput' style="width: 100%;" dx_file_field_id="{{ $emp_docs_fld_id }}">
                                    <div class='form-control'>
                                        <i class='glyphicon glyphicon-file fileinput-exists'></i> 
                                        <span class='fileinput-filename truncate dx-emp-pers-docs-file-input-download' style="max-width: 300px;">                                            
                                            <!--<a href='JavaScript: download_file($item_id , $emp_docs_list->id, $emp_docs_fld_id);'>$item_value </a>-->                                            
                                        </span>
                                    </div>                                    
                                    <span class='input-group-addon btn btn-default btn-file'>
                                        <span class='fileinput-new'>{{ trans('fields.btn_set') }}</span>
                                        <span class='fileinput-exists'>{{ trans('fields.btn_change') }}</span>
                                        <input type='file' name='file_guid' class="dx-emp-pers-docs-file-input-file" />
                                        <input class='fileinput-remove-mark' type='hidden' value='0' name = 'file_guid_remove' />
                                    </span>
                                    <a href='#' class='input-group-addon btn btn-default fileinput-exists' data-dismiss='fileinput'>{{ trans('fields.btn_remove') }}</a>
                                    <input class="dx-emp-pers-docs-file-input-isset" type="hidden" name='file_guid_is_set' value="0" />                                   
                                </div>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-danger dx-emp-pers-docs-clear-btn">
                                <i class='fa fa-remove'></i> {{ trans('employee.personal_docs.clear_doc') }}
                            </button>
                        </div>
                    </div>
                </div>
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
        /**
         * Initializes component
         * @returns {undefined}
         */
        init: function () {
            window.DxEmpPersDocs.userId = $('#dx-emp-pers-docs-panel').attr('data-user-id');
            window.DxEmpPersDocs.dateFormat = $('#dx-emp-pers-docs-panel').attr('data-date-format');
            window.DxEmpPersDocs.locale = $('#dx-emp-pers-docs-panel').attr('data-locale');

            $('#dx-emp-pers-docs-save-btn').click(window.DxEmpPersDocs.onClickSaveDocs);

            $("#dx-emp-pers-docs-country").change(window.DxEmpPersDocs.onChangeCountry);

            window.DxEmpPersDocs.loadEmployeeData();
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
            var data_rows = JSON.parse(data);

            // Prepares dropdown list options
            for (var i = 0; i < data_rows.length; i++) {
                window.DxEmpPersDocs.createNewDocRow(false, data_rows[i]);
            }

            $("#dx-emp-pers-docs-country").trigger('change');
        },
        createNewDocRow: function (is_new, data) {
            // Gets template for row and converts it as jquery object
            var new_row_html = $($('#dx-emp-pers-docs-new-row').html());

            // Sets id for new row
            new_row_html.attr('id', 'dx-emp-pers-docs-row-' + window.DxEmpPersDocs.rowCount);

            // Prepare "valid to" date picker            
            new_row_html = window.DxEmpPersDocs.initValidToDatePicker(new_row_html);

            if (is_new) {
                new_row_html = window.DxEmpPersDocs.setDocTypeValue(new_row_html, data);
            } else {
                new_row_html = window.DxEmpPersDocs.setValues(new_row_html, data);
            }

            // Append row to table
            $('#dx-emp-pers-docs-table').append(new_row_html);

            // Bind all rquired events for row elements
            window.DxEmpPersDocs.bindDocRowEvenets();

            // Increase row counter
            window.DxEmpPersDocs.rowCount++;
        },
        initValidToDatePicker: function (new_row_html) {
            var picker = new_row_html.find('.dx-emp-pers-docs-validto-input');

            picker.attr('id', 'dx-emp-pers-docs-validto-input-' + window.DxEmpPersDocs.rowCount);

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
            new_row_html.find('.dx-emp-pers-docs-type-input').val(data_row.id);
            new_row_html.find('.dx-emp-pers-docs-type-label').html(data_row.name);

            return new_row_html;
        },
        setValues: function (new_row_html, data_row) {
            new_row_html.find('.dx-emp-pers-docs-id-input').val(data_row.id);
            new_row_html.find('.dx-emp-pers-docs-type-input').val(data_row.doc_id);
            new_row_html.find('.dx-emp-pers-docs-type-label').html(data_row.personal_document.name);
            new_row_html.find('.dx-emp-pers-docs-docnr-input').val(data_row.doc_nr);
            new_row_html.find('.dx-emp-pers-docs-validto-input').val(data_row.valid_to);
            new_row_html.find('.dx-emp-pers-docs-publisher-input').val(data_row.publisher);

            return new_row_html;
        },
        clearDocRow: function (e) {
            var row = $(e.target).parents('.dx-emp-pers-docs-row');

            row.find('.dx-emp-pers-docs-id-input').val(0);
            row.find('.dx-emp-pers-docs-docnr-input').val('');
            row.find('.dx-emp-pers-docs-validto-input').val('');
            row.find('.dx-emp-pers-docs-publisher-input').val('');
        },
        getDataForSave: function () {
            var rows = $('#dx-emp-pers-docs-table .dx-emp-pers-docs-row:visible');

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

                var file = row.find('.dx-emp-pers-docs-file-input-file').prop("files")[0];
                formData.append('file'.i, file);

                data.rows.push(row_data);
            }

            formData.append('data', JSON.stringify(data));

            return formData;
        },
        bindDocRowEvenets: function () {
            // Gets current row
            var row = $('#dx-emp-pers-docs-row-' + window.DxEmpPersDocs.rowCount);

            row.find('.dx-emp-pers-docs-clear-btn').click(window.DxEmpPersDocs.clearDocRow);

        },
        onChangeCountry: function (e) {
            var country_id = $(e.target).val();

            $.ajax({
                url: '/employee/personal_docs/get/docs_by_country/' + country_id,
                type: "get",
                success: window.DxEmpPersDocs.onSuccessChangeCountry,
                error: window.DxEmpPersDocs.onAjaxError
            });
        },
        onSuccessChangeCountry: function (data) {
            var docs = JSON.parse(data);

            var inputs = $('#dx-emp-pers-docs-table .dx-emp-pers-docs-type-input');

            window.DxEmpPersDocs.toggleDocumentRows(inputs, docs);

            window.DxEmpPersDocs.createMissingDocumentRows(inputs, docs);
        },
        toggleDocumentRows: function (inputs, docs) {
            for (var i = 0; i < inputs.length; i++) {
                var input = $(inputs[i]);
                var value = input.val();

                var exists = false;
                for (var d = 0; d < docs.length; d++) {
                    if (docs[d].id == value) {
                        exists = true;
                        break;
                    }
                }

                if (exists) {
                    // Show whole document row if it was hidden
                    window.DxEmpPersDocs.showDocumentRow(input);
                } else {
                    // Hides row but doesn't delete it because it can be later shown again if country is changed
                    window.DxEmpPersDocs.hideDocumentRow(input);
                }
            }
        },
        /**
         * Creates new document rows if there isn't saved such document type for user
         * @param {type} inputs
         * @param {type} docs
         * @returns {undefined}
         */
        createMissingDocumentRows: function (inputs, docs) {
            for (var d = 0; d < docs.length; d++) {
                var doc = docs[d];

                var exists = false;

                for (var i = 0; i < inputs.length; i++) {
                    if (doc.id == $(inputs[i]).val()) {
                        exists = true;
                        break;
                    }
                }

                if (!exists) {
                    window.DxEmpPersDocs.createNewDocRow(true, doc);
                }
            }
        },
        showDocumentRow: function (dropdown) {
            dropdown.parents('.dx-emp-pers-docs-row').show();
        },
        hideDocumentRow: function (dropdown) {
            dropdown.parents('.dx-emp-pers-docs-row').hide();
        },
        onClickSaveDocs: function () {
            var form_data = window.DxEmpPersDocs.getDataForSave();

            $.ajax({
                url: '/employee/personal_docs/save',
                data: form_data,
                type: "post",
                processData: false,
                dataType: "json",
                contentType: false,
                success: window.DxEmpPersDocs.onSuccessSave,
                error: window.DxEmpPersDocs.onAjaxError
            });
        },
        onSuccessSave: function (data) {
            $('#dx-emp-pers-docs-table .dx-emp-pers-docs-row:hidden').remove();
        },
        onAjaxError: function (data) {

        }
    }
    document.addEventListener("DOMContentLoaded", function (event) {
        window.DxEmpPersDocs.init();
    });

</script>