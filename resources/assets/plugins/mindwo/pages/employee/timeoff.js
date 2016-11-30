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

        //$('#dx-tab_timeoff').on('click', '.dx-emp-timeoff-sel-timeoff', {}, window.DxEmpNotes.timeoffSelect);
       // $('#dx-tab_timeoff').on('click', '.dx-emp-timeoff-sel-year', {}, window.DxEmpNotes.yearSelect);

       // window.DxEmpTimeoff.year = $('#dx-emp-timeoff-panel').data('year');
     //  window.DxEmpTimeoff.timeoff = $('#dx-emp-timeoff-panel').data('timeoff');

        window.DxEmpTimeoff.initDataTable();

        window.DxEmpTimeoff.isLoaded = true;
    },
    initDataTable: function () {
        $('#dx-empt-datatable-timeoff').DataTable({
            serverSide: true,
            searching: false,
            ajax: DX_CORE.site_url + 'employee/timeoff/get/table/' + window.DxEmpTimeoff.userId + '/' + 1 + '/' + 1,
            columns: [
                {data: 'calc_date', name: 'calc_date'},
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

        window.DxEmpTimeoff.year = btn.value();
        
        $('.dx-emp-timeoff-curr').html(window.DxEmpTimeoff.timeoff + ' (' + window.DxEmpTimeoff.year + ')');

        window.DxEmpTimeoff.initDataTable();
    },
    timeoffSelect: function (e) {
        var btn = $(e.target);

        window.DxEmpTimeoff.timeoff = btn.value();
        
        $('.dx-emp-timeoff-curr').html(window.DxEmpTimeoff.timeoff + ' (' + window.DxEmpTimeoff.year + ')');

        window.DxEmpTimeoff.initDataTable();
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