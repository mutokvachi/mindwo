<div id="dx-emp-pers-docs-panel" data-employee-id="{{ $employee_id }}">
    <div>
        <select id="dx-emp-pers-docs-country">
            @foreach (App\Models\Country::all() as $country)
            <option value="{{$country->id}}">{{ $country->title }}</option>
            @endforeach

        </select>
    </div>    
    <div>
        <button id="dx-emp-pers-docs-new-btn">{{ trans('employee.personal_docs.new_doc') }}</button>
    </div>
    <div id="dx-emp-pers-docs-table"></div> 
    <div>
        <button id="dx-emp-pers-docs-save-btn">{{ trans('employee.personal_docs.save_docs') }}</button>
    </div>
    <div id="dx-emp-pers-docs-new-row" style="display: none;">
        <div class="dx-emp-pers-docs-row row">
            <input class="dx-emp-pers-docs-id-input" type="hidden" />
            <div class="col-md-2">
                <select class='dx-emp-pers-docs-type-input'></select>
            </div>
            <div class="col-md-2">
                <input />
            </div>
            <div class="col-md-2">
                <input class="dx-emp-pers-docs-publisher-input" type="text" />
            </div>
            <div class="col-md-2">
                <input />
            </div>
            <div class="col-md-2">
                <button class="dx-emp-pers-docs-delete-btn">{{ trans('employee.personal_docs.delete_doc') }}</button>
            </div>
        </div>
    </div>
</div>
<script>
    window.DxEmpPersDocs = window.DxEmpPersDocs || {
        rowCount: 0,
        employeeId: 0,
        currentDocumentByCountry: false,
        /**
         * Initializes component
         * @returns {undefined}
         */
        init: function () {
            window.DxEmpPersDocs.employeeId = $('#dx-emp-pers-docs-panel').attr('data-employee-id');

            $('#dx-emp-pers-docs-new-btn').click(window.DxEmpPersDocs.onClickNewDocRow);

            $('#dx-emp-pers-docs-save-btn').click(window.DxEmpPersDocs.onClickSaveDocs);

            $("#dx-emp-pers-docs-country").change(window.DxEmpPersDocs.onChangeCountry);


            window.DxEmpPersDocs.loadEmployeeData();
        },
        loadEmployeeData: function () {
            $.ajax({
                url: '/employee/personal_docs/get/employee_docs/' + window.DxEmpPersDocs.employeeId,
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
        onClickNewDocRow: function () {
            window.DxEmpPersDocs.createNewDocRow(true, null);
        },
        createNewDocRow: function (is_new, data) {
            // Gets template for row and converts it as jquery object
            var new_row_html = $($('#dx-emp-pers-docs-new-row').html());

            // Sets id for new row
            new_row_html.attr('id', 'dx-emp-pers-docs-row-' + window.DxEmpPersDocs.rowCount);

            if (is_new) {
                // Fills drowdown with documents available for selected country
                if (window.DxEmpPersDocs.currentDocumentByCountry) {
                    new_row_html.find('.dx-emp-pers-docs-type-input').html(window.DxEmpPersDocs.currentDocumentByCountry.clone());
                }
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
        setValues: function (new_row_html, data_row) {
            var option = $('<option></option>').val(data_row.doc_id).html(data_row.personal_document.name);
            new_row_html.find('.dx-emp-pers-docs-type-input').html(option);

            new_row_html.find('.dx-emp-pers-docs-id-input').val(data_row.id);
            new_row_html.find('.dx-emp-pers-docs-type-input').val(data_row.doc_id);
            new_row_html.find('.dx-emp-pers-docs-publisher-input').val(data_row.publisher);

            return new_row_html;
        },
        bindDocRowEvenets: function () {
            // Gets current row
            var row = $('#dx-emp-pers-docs-row-' + window.DxEmpPersDocs.rowCount);

            row.find('.dx-emp-pers-docs-delete-btn').click(window.DxEmpPersDocs.deleteDocRow);

        },
        deleteDocRow: function (e) {
            $(e.target).parents('.dx-emp-pers-docs-row').remove();
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

            var options = $();

            // Prepares dropdown list options
            for (var i = 0; i < docs.length; i++) {
                var doc = docs[i];

                options = options.add(
                        $('<option></option>').val(doc.id).html(doc.name));
            }

            window.DxEmpPersDocs.currentDocumentByCountry = options;

            window.DxEmpPersDocs.fillDocDropdowns();
        },
        fillDocDropdowns: function () {
            var dropdowns = $('#dx-emp-pers-docs-table .dx-emp-pers-docs-type-input');

            for (var d = 0; d < dropdowns.length; d++) {
                var dropdown = $(dropdowns[d]);

                // Saves previously selected value
                var selected_val = dropdown.val();

                // Check if current dropdwon's selected value exists in document list of selected country
                if (window.DxEmpPersDocs.existDocumentInCurrentDocs(selected_val)) {
                    // Redraw all options in drowdown
                    dropdown.html(window.DxEmpPersDocs.currentDocumentByCountry.clone());

                    // Select previously selected value (it was lost when redrawing all list options)
                    dropdown.val(selected_val);

                    // Show whole document row if it was hidden
                    window.DxEmpPersDocs.showDocumentRow(dropdown);
                } else {
                    // Hides row but doesn't delete it because it can be later shown again if country is changed
                    window.DxEmpPersDocs.hideDocumentRow(dropdown);
                }
            }
        },
        existDocumentInCurrentDocs: function (val) {
            var exists = false;
            window.DxEmpPersDocs.currentDocumentByCountry.each(function () {
                if (this.value == val) {
                    exists = true;
                    return false;
                }
            });

            return exists;
        },
        showDocumentRow: function (dropdown) {
            dropdown.parents('.dx-emp-pers-docs-row').show();
        },
        hideDocumentRow: function (dropdown) {
            dropdown.parents('.dx-emp-pers-docs-row').hide();
        },
        onClickSaveDocs: function () {
            var data = window.DxEmpPersDocs.getDataForSave();

            $.ajax({
                url: '/employee/personal_docs/save',
                data: {data: JSON.stringify(data)},
                type: "post",
                success: window.DxEmpPersDocs.onSuccessSave,
                error: window.DxEmpPersDocs.onAjaxError
            });
        },
        getDataForSave: function () {
            var rows = $('#dx-emp-pers-docs-table .dx-emp-pers-docs-row:visible');

            var data = {
                employee_id: window.DxEmpPersDocs.employeeId,
                rows: []
            };

            for (var i = 0; i < rows.length; i++) {
                var row = $(rows[i]);

                var row_data = {};

                row_data.id = row.find('.dx-emp-pers-docs-id-input').val();
                row_data.document_type = row.find('.dx-emp-pers-docs-type-input').val();
                row_data.publisher = row.find('.dx-emp-pers-docs-publisher-input').val();

                data.rows.push(row_data);
            }

            return data;
        },
        onSuccessSave: function (data) {

        },
        onAjaxError: function (data) {

        }
    }
    document.addEventListener("DOMContentLoaded", function (event) {
        window.DxEmpPersDocs.init();
    });

</script>