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

        show_page_splash(1);

        $.ajax({
            url: DX_CORE.site_url + 'employee/timeoff/get/view/' + window.DxEmpTimeoff.userId,
            type: "get",
            success: window.DxEmpTimeoff.onLoadViewSuccess,
            error: function (data) {
                hide_page_splash(1);
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

        window.DxEmpTimeoff.isLoaded = true;

        hide_page_splash(1);
    },
    /**
     * Shows loading box
     * @returns {undefined}
     */
    showLoading: function () {
        window.DxEmpTimeoff.isSending = true;
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