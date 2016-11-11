window.DxEmpPersDocs = window.DxEmpPersDocs || {
    /**
     * Row counter, used to identify rows
     */
    rowCount: 0,
    
    /**
     * User ID which is loaded
     */
    userId: 0,
    
    /**
     * Date format used in system. This format is used to initialize date picker
     */
    dateFormat: '',
    
    /**
     * Locale used in system. This format is used to initializes date picker
     */
    locale: 'en',
    
    /**
     * Registers ID for table where documents are saved
     */
    empDocListId: 0,
    
    /**
     * Registers field ID where documents are saved
     */
    empDocFldId: 0,
    
    /**
     * Parameter if component is initialized
     */
    isInit: false,
    
    /**
     * Clone of view which contains view state with data saved in database.
     * If edit mode is canceled, then this view is replaced with edit view and all made changes are lost
     */
    viewClone: '',
    
    /**
     * Callback function which is called after successful components initialization
     * @param {type} data Data which is sent to callback function
     */
    callbackOnInitiSuccess: function (data) {},
    
    /**
     * Callback function which is called after successful data save
     * @param {type} data Data which is sent to callback function
     */
    callbackOnSaveSuccess: function (data) {},
    
    /**
     * Callback function which is called after process is exited with error
     * @param {type} data Data which is sent to callback function
     */
    callbackOnError: function (data) {},
    
    /**
     * Initializes component
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
        if (data != '') {
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
        // Initiates tooltips because they are not initiated because rows are created dynamicaly
        $('#dx-emp-pers-docs-table').find('[data-tooltip-title]').each(function (i, obj) {
            $(obj).attr('title', $(obj).data('tooltip-title'));
            $(obj).tooltip();
        });
    },
    finishInit: function () {
        if (!window.DxEmpPersDocs.isInit) {
            window.DxEmpPersDocs.isInit = true;
            if (window.DxEmpPersDocs.userId == 0) {
                window.DxEmpPersDocs.toggleDisable(false);
            } else {
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