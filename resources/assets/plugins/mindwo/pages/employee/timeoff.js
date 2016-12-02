/**
 * Contains logic for viewing and editing employee's notes
 * @type Window.DxEmpTimeoff|window.DxEmpTimeoff 
 */
window.DxEmpTimeoff = window.DxEmpTimeoff || {
    /**
     * User ID which is loaded
     */
    userId: 0,
    /**
     * Parameter if control is loaded
     */
    isLoaded: false,
    /**
     * Parameter if note is sending to server
     */
    isSending: false,
    year: 2016,
    timeoff: 1,
    /**
     * Initializes component
     */
    init: function (userId) {
        window.DxEmpTimeoff.userId = userId;
    },
    /**
     * Loads view
     * @returns {undefined}
     */
    loadView: function () {
        if (window.DxEmpTimeoff.isLoaded) {
            return;
        }

        window.DxEmpTimeoff.showLoading();

        $.ajax({
            url: DX_CORE.site_url + 'employee/timeoff/get/view/' + window.DxEmpTimeoff.userId,
            type: "get",
            success: window.DxEmpTimeoff.onLoadViewSuccess,
            error: function (data) {
                window.DxEmpTimeoff.hideLoading();
            }
        });
    },
    /**
     * Evnet handler when view is successfully loaded
     * @returns {string} View's HTML
     */
    onLoadViewSuccess: function (data) {
        $('#dx-tab_timeoff').html(data);

        $("#dx-tab_timeoff [data-counter='counterup']").counterUp({delay: 10, time: 700});

        $(".dx-accrual-calc").click(function () {
            window.DxEmpTimeoff.showLoading();
            var a_elem = $(this);
            $.ajax({
                url: DX_CORE.site_url + 'employee/timeoff/get/calculate/' + window.DxEmpTimeoff.userId + "/" + a_elem.data('timeoff'),
                type: "get",
                success: function (data) {
                    var thumb = a_elem.closest("div.widget-thumb");
                    var cnt_elem = thumb.find(".widget-thumb-body-stat").first();

                    cnt_elem.attr("data-value", data.balance);

                    cnt_elem.html(data.balance);

                    thumb.find(".widget-thumb-subtitle").first().html(data.unit);
                    cnt_elem.counterUp({delay: 10, time: 700});

                    window.DxEmpTimeoff.onCalculateSuccess(a_elem.data('timeoff'));
                },
                error: function (data) {
                    window.DxEmpTimeoff.hideLoading();
                }
            });
        });

        $(".dx-accrual-policy").click(function () {
            window.DxEmpTimeoff.showLoading();
            view_list_item("form", $(this).data('policy-id'), $(this).data('policy-list-id'), $(this).data('policy-user-field-id'), window.DxEmpTimeoff.userId, "", "");
        });

        $('.dx-emp-timeoff-sel-timeoff').click(window.DxEmpTimeoff.timeoffSelect);
        $('.dx-emp-timeoff-sel-year').click(window.DxEmpTimeoff.yearSelect);


        window.DxEmpTimeoff.year = $('#dx-emp-timeoff-panel').data('year');
        window.DxEmpTimeoff.timeoff = $('#dx-emp-timeoff-panel').data('timeoff');
        window.DxEmpTimeoff.timeoffTitle = $('#dx-emp-timeoff-panel').data('timeoff_title');

        window.DxEmpTimeoff.initDataTable();

        window.DxEmpTimeoff.isLoaded = true;
    },
    onCalculateSuccess: function (timeoff_id) {
        if (window.DxEmpTimeoff.timeoff == timeoff_id) {
            window.DxEmpTimeoff.refreshDataTable();
        } else {
            window.DxEmpTimeoff.hideLoading();
        }
    },
    refreshDataTable: function () {
        var url = DX_CORE.site_url + 'employee/timeoff/get/table/' + window.DxEmpTimeoff.userId + '/' + window.DxEmpTimeoff.timeoff + '/' + window.DxEmpTimeoff.year;

        window.DxEmpTimeoff.dataTable.ajax.url(url).load();

        return;
    },
    initDataTable: function () {
        window.DxEmpTimeoff.dataTable = $('#dx-empt-datatable-timeoff').DataTable({
            serverSide: true,
            searching: false,
            order: [[ 0, "desc" ]],
            ajax: DX_CORE.site_url + 'employee/timeoff/get/table/' + window.DxEmpTimeoff.userId + '/' + window.DxEmpTimeoff.timeoff + '/' + window.DxEmpTimeoff.year,
            columns: [
                {data: 'calc_date', name: 'calc_date'},
                {data: 'from_date', name: 'from_date'},
                {data: 'to_date', name: 'to_date'},
                {data: 'timeoff_record_type.title', name: 'timeoffRecordType.title'},
                {data: 'notes', name: 'notes'},
                {data: 'amount', name: 'amount'},
                {data: 'balance', name: 'balance'}
            ],
            fnPreDrawCallback: function () {
                window.DxEmpTimeoff.showLoading();
            },
            fnDrawCallback: function () {
                window.DxEmpTimeoff.hideLoading();
            }
        });
    },
    yearSelect: function (e) {
        var btn = $(e.target);

        window.DxEmpTimeoff.year = btn.data('value');

        $('.dx-emp-timeoff-curr-year').html(window.DxEmpTimeoff.year);

        window.DxEmpTimeoff.refreshDataTable();
    },
    timeoffSelect: function (e) {
        var btn = $(e.target);

        window.DxEmpTimeoff.timeoff = btn.data('value');
        window.DxEmpTimeoff.timeoffTitle = btn.data('title');

        $('.dx-emp-timeoff-curr-timeoff').html(window.DxEmpTimeoff.timeoffTitle);

        window.DxEmpTimeoff.refreshDataTable();
    },
    /**
     * Shows loading box
     * @returns {undefined}
     */
    showLoading: function () {
        window.DxEmpTimeoff.inProgressCount++;
        show_page_splash(1);
    },
    /**
     * Hides loading box
     * @returns {undefined}
     */
    hideLoading: function () {
        window.DxEmpTimeoff.isSending = false;
        hide_page_splash(1);
    },
    /**
     * Event when ajax request gets error
     * @param {array} data Data containing error information
     */
    onAjaxError: function (data) {
        window.DxEmpTimeoff.hideLoading();
    }
};