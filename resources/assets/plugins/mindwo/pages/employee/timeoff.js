/**
 * Contains logic for viewing and editing employee's time off data
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
     * Parameter if data is sending to server
     */
    isSending: false,
    /**
     * Parameter if table has been initialized
     */
    isTableInit: false,
    /**
     * Parameter if chart has been initialized
     */
    isChartInit: false,
    /**
     * Parameter if table needs to be reloaded when gets focus
     */
    doTableRefresh: true,
    /**
     * Parameter if chart needs to be reloaded when gets focus
     */
    doChartRefresh: true,
    /**
     * Current tab index. 0 - focus on chart tab, 1 - focus on table tab
     */
    currentTab: 0,
    /**
     * Current filter's date from value
     */
    dateFrom: '',
    /**
     * Current filter's date to value
     */
    dateTo: '',
    /**
     * Current filter's time off value
     */
    timeoff: 1,
    /**
     * Date format used in system. This format is used to initialize date picker
     */
    dateFormat: '',
    /**
     * Initializes component
     * @param {integer} userId User's ID which is opened
     * @returns {undefined}
     */
    init: function (userId) {
        window.DxEmpTimeoff.userId = userId;
    },
    /**
     * Get filter parameters for adding to request when getting table or  chart
     * @returns {String} Reteived URL part containign parameters
     */
    getFilterParams: function () {
        return window.DxEmpTimeoff.userId + '/' + window.DxEmpTimeoff.timeoff + '/' + (window.DxEmpTimeoff.dateFrom / 1000) + '/' + (window.DxEmpTimeoff.dateTo / 1000);
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
     * Reloads year filter
     * @returns {undefined}
     */
    loadFilterYear: function () {
        $.ajax({
            url: DX_CORE.site_url + 'employee/timeoff/get/filter/year/' + window.DxEmpTimeoff.userId,
            type: "get",
            success: window.DxEmpTimeoff.onLoadFilterYearSuccess,
            error: function (data) {
            }
        });
    },
    /**
     * On successfully retrieved filter HTML
     * @param {string} data  HTML of the filter
     * @returns {undefined}
     */
    onLoadFilterYearSuccess: function (data) {
        $('.dx-emp-timeoff-filter-year-list').html(data);
    },
    /**
     * Evnet handler when view is successfully loaded
     * @returns {string} View's HTML
     */
    onLoadViewSuccess: function (data) {
        $('#dx-tab_timeoff').html(data);

        window.DxEmpTimeoff.dateFormat = $('#dx-emp-timeoff-panel').data('date_format').toUpperCase();
        window.DxEmpTimeoff.dateFrom = new Date($('#dx-emp-timeoff-panel').data('date_from'));
        window.DxEmpTimeoff.dateTo = new Date($('#dx-emp-timeoff-panel').data('date_to'));
        window.DxEmpTimeoff.timeoff = $('#dx-emp-timeoff-panel').data('timeoff');
        window.DxEmpTimeoff.timeoffTitle = $('#dx-emp-timeoff-panel').data('timeoff_title');

        $("#dx-tab_timeoff [data-counter='counterup']").counterUp({delay: 10, time: 700});

        $(".dx-accrual-calc").click(function () {
            window.DxEmpTimeoff.showLoading();
            var a_elem = $(this);
            $.ajax({
                url: DX_CORE.site_url + 'employee/timeoff/get/calculate/' + window.DxEmpTimeoff.userId + "/" + a_elem.data('timeoff'),
                type: "get",
                success: function (data) {
                    window.DxEmpTimeoff.onCalculateSuccess(a_elem, data);
                }
            });
        });

        $(".dx-accrual-delete").click(function () {
            PageMain.showConfirm(window.DxEmpTimeoff.deleteCalculation, $(this), Lang.get('form.modal_confirm_title'), Lang.get('empl_profile.timeoff.delete_confirm'), Lang.get('form.btn_delete'), Lang.get('form.btn_cancel'));
        });

        $(".dx-accrual-policy").click(function () {
            window.DxEmpTimeoff.showLoading();
            view_list_item("form", $(this).data('policy-id'), $(this).data('policy-list-id'), $(this).data('policy-user-field-id'), window.DxEmpTimeoff.userId, "", "");
        });

        $('.dx-emp-timeoff-sel-timeoff').click(window.DxEmpTimeoff.timeoffSelect);

        $('.dx-emp-timeoff-tab-chart-btn').click(function () {
            window.DxEmpTimeoff.switchTab(0);
        });
        $('.dx-emp-timeoff-tab-table-btn').click(function () {
            window.DxEmpTimeoff.switchTab(1);
        });

        window.DxEmpTimeoff.initFilterDatePicker();

        // Initializes current tab by loadings its data (by default chart tab)
        window.DxEmpTimeoff.reloadTabData();

        window.DxEmpTimeoff.isLoaded = true;

        window.DxEmpTimeoff.hideLoading();
    },
    /**
     * Event handler on successful calculation
     * @param {DOMObject} a_elem Button which triggered event
     * @param {array} data Data returned from server
     * @returns {undefined}
     */
    onCalculateSuccess: function (a_elem, data) {
        var thumb = a_elem.closest("div.widget-thumb");
        var cnt_elem = thumb.find(".widget-thumb-body-stat").first();

        cnt_elem.attr("data-value", data.balance);

        cnt_elem.html(data.balance);

        thumb.find(".widget-thumb-subtitle").first().html(data.unit);
        cnt_elem.counterUp({delay: 10, time: 700});

        // This also refresh table after filter is reloaded
        window.DxEmpTimeoff.loadFilterYear();

        if (window.DxEmpTimeoff.timeoff == a_elem.data('timeoff')) {
            window.DxEmpTimeoff.setDataRefreshRequest();
        } else {
            window.DxEmpTimeoff.hideLoading();
        }
    },
    /**
     * Deletes calculation
     * @param {DOMObject} a_elem Button which triggered event
     * @returns {undefined}
     */
    deleteCalculation: function (a_elem) {
        window.DxEmpTimeoff.showLoading();
        $.ajax({
            url: DX_CORE.site_url + 'employee/timeoff/get/delete_calculated/' + window.DxEmpTimeoff.userId + "/" + a_elem.data('timeoff'),
            type: "get",
            success: function (data) {
                window.DxEmpTimeoff.onCalculateSuccess(a_elem, data);
            }
        });
    },
    /**
     * Sets parameters that data in tabs must be reloaded
     * @returns {undefined}
     */
    setDataRefreshRequest: function () {
        window.DxEmpTimeoff.doChartRefresh = true;
        window.DxEmpTimeoff.doTableRefresh = true;

        // After setting refresh request, refreshes current tab data
        window.DxEmpTimeoff.reloadTabData();
    },
    /**
     * Switches tab and reload data if needed
     */
    switchTab: function (tab_index) {
        window.DxEmpTimeoff.currentTab = tab_index;

        // After changing tab, chech if data must be resfreshed in current tab
        window.DxEmpTimeoff.reloadTabData();
    },
    /**
     * Reload cureent tab after filter has been changed
     * @returns {undefined}
     */
    reloadTabData: function () {
        if (window.DxEmpTimeoff.currentTab === 0) {
            window.DxEmpTimeoff.loadChart();
        } else {
            window.DxEmpTimeoff.loadTable();
        }
    },
    /**
     * Reload table data. Initializes component if needed
     * @returns {undefined}
     */
    loadTable: function () {
        if (!window.DxEmpTimeoff.isTableInit) {
            window.DxEmpTimeoff.initDataTable();
            window.DxEmpTimeoff.isTableInit = true;
        } else if (window.DxEmpTimeoff.doTableRefresh) {
            window.DxEmpTimeoff.refreshDataTable();
        }

        // Resets parameter that table has been refreshed
        window.DxEmpTimeoff.doTableRefresh = false;
    },
    /**
     * Reload chart data. Initializes component if needed
     * @returns {undefined}
     */
    loadChart: function () {
        if (!window.DxEmpTimeoff.isChartInit) {
            window.DxEmpTimeoff.initChart();
            window.DxEmpTimeoff.isChartInit = true;
        } else if (window.DxEmpTimeoff.doChartRefresh) {
            window.DxEmpTimeoff.refreshChart();
        }

        // Resets parameter that chart has been refreshed
        window.DxEmpTimeoff.doChartRefresh = false;
    },
    /**
     * Initializes data table
     * @returns {undefined}
     */
    initDataTable: function () {
        if (!window.DxEmpTimeoff.dataTable) {
            window.DxEmpTimeoff.dataTable = $('#dx-empt-datatable-timeoff').DataTable({
                serverSide: true,
                searching: false,
                order: [[0, "desc"]],
                ajax: DX_CORE.site_url + 'employee/timeoff/get/table/' + window.DxEmpTimeoff.getFilterParams(),
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
        }
    },
    /**
     * Refreshes data table
     * @returns {undefined}
     */
    refreshDataTable: function () {
        var url = DX_CORE.site_url + 'employee/timeoff/get/table/' + window.DxEmpTimeoff.getFilterParams();

        window.DxEmpTimeoff.dataTable.ajax.url(url).load();
    },
    /**
     * Initializes chart
     * @returns {undefined}
     */
    initChart: function () {
        window.DxEmpTimeoff.showLoading();

        window.DxEmpTimeoff.refreshChart();

        window.DxEmpTimeoff.hideLoading();
    },
    /**
     * Refreshes chart data
     * @returns {undefined}
     */
    refreshChart: function () {
        window.DxEmpTimeoff.showLoading();

        $.ajax({
            url: DX_CORE.site_url + 'employee/timeoff/get/chart/' + window.DxEmpTimeoff.getFilterParams(),
            type: "get",
            success: window.DxEmpTimeoff.onGetChartDataSuccess,
            error: function (data) {
                $('#dx-emp-timeoff-chart').html(data);
                window.DxEmpTimeoff.hideLoading();
            }
        });
    },
    onGetChartDataSuccess: function (data) {
        $('#dx-emp-timeoff-chart').html(data);
        window.DxEmpTimeoff.hideLoading();
    },
    initFilterDatePicker: function () {
        $('.dx-emp-timeoff-filter-year-btn').click(function (event) {
            $('#dx-emp-timeoff-filter-year-input').data('daterangepicker').toggle();
        });

        $('#dx-emp-timeoff-filter-year-input').daterangepicker({
            locale: {
                "format": window.DxEmpTimeoff.dateFormat,
                "separator": " - ",
                "applyLabel": Lang.get('date_range.btn_set'),
                "cancelLabel": Lang.get('date_range.btn_cancel'),
                "fromLabel": Lang.get('date_range.lbl_from'),
                "toLabel": Lang.get('date_range.lbl_to'),
                "customRangeLabel": Lang.get('date_range.lbl_interval'),
                "daysOfWeek": [
                    Lang.get('date_range.d_7'),
                    Lang.get('date_range.d_1'),
                    Lang.get('date_range.d_2'),
                    Lang.get('date_range.d_3'),
                    Lang.get('date_range.d_4'),
                    Lang.get('date_range.d_5'),
                    Lang.get('date_range.d_6')
                ],
                "monthNames": [Lang.get('date_range.m_jan'), Lang.get('date_range.m_feb'), Lang.get('date_range.m_mar'), Lang.get('date_range.m_apr'), Lang.get('date_range.m_may'), Lang.get('date_range.m_jun'), Lang.get('date_range.m_jul'), Lang.get('date_range.m_aug'), Lang.get('date_range.m_sep'), Lang.get('date_range.m_oct'), Lang.get('date_range.m_nov'), Lang.get('date_range.m_dec')],
                "firstDay": 1
            },
            "startDate": window.DxEmpTimeoff.dateFrom,
            "endDate": window.DxEmpTimeoff.dateTo,
            "showDropdowns": true
        }, window.DxEmpTimeoff.yearSelect);
    },
    /**
     * Event callback when filter's year value is selected
     * @param {object} e Event caller
     * @returns {undefined}
     */
    yearSelect: function (start, end, label) {
        window.DxEmpTimeoff.dateFrom = start;
        window.DxEmpTimeoff.dateTo = end;

        $('.dx-emp-timeoff-curr-year').html(start.format(window.DxEmpTimeoff.dateFormat) + ' - ' + end.format(window.DxEmpTimeoff.dateFormat));

        window.DxEmpTimeoff.setDataRefreshRequest();
    },
    /**
     * Event callback when filter's time off type value is selected
     * @param {object} e Event caller
     * @returns {undefined}
     */
    timeoffSelect: function (e) {
        var btn = $(e.target);

        window.DxEmpTimeoff.timeoff = btn.data('value');
        window.DxEmpTimeoff.timeoffTitle = btn.data('title');

        $('.dx-emp-timeoff-curr-timeoff').html(window.DxEmpTimeoff.timeoffTitle);

        window.DxEmpTimeoff.setDataRefreshRequest();
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
     * @returns {undefined}
     */
    onAjaxError: function (data) {
        window.DxEmpTimeoff.hideLoading();
    }
};