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
    <div id="dx-emp-pers-docs-table">

    </div> 
    <div id="dx-emp-pers-docs-new-row" style="display: none;">
        <div class="dx-emp-pers-docs-row row">
            <div class="col-md-2">
                <select class='dx-emp-pers-docs-type-input'></select>
            </div>
            <div class="col-md-2">
                <input />
            </div>
            <div class="col-md-2">
                <input />
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
        /**
         * Initializes component
         * @returns {undefined}
         */
        init: function () {
            window.DxEmpPersDocs.employeeId = $('#dx-emp-pers-docs-panel').attr('data-employee-id');

            $('#dx-emp-pers-docs-new-btn').click(window.DxEmpPersDocs.createNewDocRow);

            $("#dx-emp-pers-docs-country").change(window.DxEmpPersDocs.changeCountry);
        },
        createNewDocRow: function () {
            // Gets template for row and converts it as jquery object
            var new_row_html = $($('#dx-emp-pers-docs-new-row').html());

            // Sets id for new row
            new_row_html.attr('id', 'dx-emp-pers-docs-row-' + window.DxEmpPersDocs.rowCount);

            $('#dx-emp-pers-docs-table').append(new_row_html);

            window.DxEmpPersDocs.bindDocRowEvenets();

            window.DxEmpPersDocs.rowCount++;
        },
        bindDocRowEvenets: function () {
            // Gets current row
            var row = $('#dx-emp-pers-docs-row-' + window.DxEmpPersDocs.rowCount);

            row.find('.dx-emp-pers-docs-delete-btn').click(window.DxEmpPersDocs.deleteDocRow);

        },
        deleteDocRow: function (e) {
            $(e.target).closest('.dx-emp-pers-docs-row').remove();
        },
        changeCountry: function (e) {
            var country_id = $(e.target).val();

            $.ajax({
                url: '/employee/get/personal_docs_by_country/' + country_id,
                type: "get",
                success: function (data) {
                    var docs = JSON.parse(data);

                    var dropdowns = $('#dx-emp-pers-docs-table .dx-emp-pers-docs-type-input');

                    for (var i = 0; i < docs.length; i++) {
                        var doc = docs[i];

                        dropdowns.forEach(function (dropdown) {
                            dropdown.append('<option value="' + doc.id + '">' + doc.name + '</option>');
                        });
                    }
                },
                error: function (data){
                    var e = data;
                }
            });
        },
    }
    document.addEventListener("DOMContentLoaded", function (event) {
        window.DxEmpPersDocs.init();
    });

</script>