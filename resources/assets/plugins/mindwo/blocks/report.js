/**
 * Contains logic for viewing and editing employee's time off data
 * @type Window.DxBlockReport|window.DxBlockReport 
 */
window.DxBlockReport = window.DxBlockReport || {
    reportName: '',
    domObject: null,
    uid: 0,
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
    groupId: 0,
    /**
     * Current filter's time off types title
     */
    groupTitle: '',
    /**
     * Parameter if current filter's time off type is in hours or days
     */
    timeoffIsAccrualHours: 1,
    /**
     * Date format used in system. This format is used to initialize date picker
     */
    dateFormat: '',
    /**
     * Working days length in hours
     */
    workingDayH: '',
    /**
     * Initializes component
     * @returns {undefined}
     */
    init: function (domObject) {
        window.DxBlockReport.domObject = domObject;

        if (window.DxBlockReport.isLoaded) {
            return;
        }

        window.DxBlockReport.showLoading();

        window.DxBlockReport.uid = window.DxBlockReport.domObject.data('uid');
        window.DxBlockReport.reportName = window.DxBlockReport.domObject.data('report_name');
        window.DxBlockReport.workingDayH = window.DxBlockReport.domObject.data('working_day_h');
        window.DxBlockReport.dateFormat = window.DxBlockReport.domObject.data('date_format').toUpperCase();
        window.DxBlockReport.dateFrom = new Date(window.DxBlockReport.domObject.data('date_from'));
        window.DxBlockReport.dateTo = new Date(window.DxBlockReport.domObject.data('date_to'));

        window.DxBlockReport.domObject.find('.dx-widget-report-sel-group').click(window.DxBlockReport.groupSelect);

        window.DxBlockReport.initFilterDatePicker();

        // Load chart
        window.DxBlockReport.loadChart();

        window.DxBlockReport.isLoaded = true;
    },
    /**
     * Get filter parameters for adding to request when getting chart
     * @returns {String} Reteived URL part containign parameters
     */
    getFilterParams: function () {
        return window.DxBlockReport.reportName + '/' + window.DxBlockReport.groupId + '/' + (window.DxBlockReport.dateFrom / 1000) + '/' + (window.DxBlockReport.dateTo / 1000);
    },
    /**
     * Reload chart data. Initializes component if needed
     * @returns {undefined}
     */
    loadChart: function () {
        if (!window.DxBlockReport.isChartInit) {
            window.DxBlockReport.initChart();
            window.DxBlockReport.isChartInit = true;
        }
    },
    /**
     * Initializes chart
     * @returns {undefined}
     */
    initChart: function () {
        window.DxBlockReport.showLoading();

        $("<div id='dx-widget-report-chart-tooltip-" + window.DxBlockReport.uid + "' class='dx-widget-report-chart-tooltip'></div>").appendTo("body");

        $("#dx-widget-report-chart-" + window.DxBlockReport.uid).bind("plothover", window.DxBlockReport.onPlotHover);

        window.DxBlockReport.refreshChart();
    },
    /**
     * Refreshes chart data
     * @returns {undefined}
     */
    refreshChart: function () {
        window.DxBlockReport.showLoading();

        $.ajax({
            url: DX_CORE.site_url + 'widget/report/get/chart/' + window.DxBlockReport.getFilterParams(),
            type: "get",
            success: window.DxBlockReport.onGetChartDataSuccess,
            error: window.DxBlockReport.onAjaxError
        });
    },
    /**
     * Gets unit name for current filtered value
     * @returns {string} Units name
     */
    getUnit: function () {
        return Lang.get('reports.' + window.DxBlockReport.reportName + '.unit');
    },
    /**
     * Load total values for specified period into panel
     * @param {array} data Data contaning period total data
     * @returns {undefined}
     */
    loadTotalData: function (data) {
        if (data) {
            // window.DxBlockReport.timeoffIsAccrualHours = data[0].is_accrual_hours; !!!!!!!!!!!!!!!!!!
            var unit = window.DxBlockReport.getUnit();
            window.DxBlockReport.domObject.find('.dx-widget-report-period-balance').html(window.DxBlockReport.calculateChartHours(data.total) + ' ' + unit);
            window.DxBlockReport.domObject.find('.dx-widget-report-period-accrued').html(window.DxBlockReport.calculateChartHours(data.gain) + ' ' + unit);
            window.DxBlockReport.domObject.find('.dx-widget-report-period-used').html(window.DxBlockReport.calculateChartHours(data.loss) + ' ' + unit);
        } else {
            window.DxBlockReport.domObject.find('.dx-widget-report-period-balance').html(0);
            window.DxBlockReport.domObject.find('.dx-widget-report-period-accrued').html(0);
            window.DxBlockReport.domObject.find('.dx-widget-report-period-used').html(0);
        }
    },
    /**
     * If needed convert hours to days
     * @param {Number} value Input hours
     * @returns {Number} Output value, could be in days or hours
     */
    calculateChartHours: function (value) {
        if (window.DxBlockReport.timeoffIsAccrualHours == 1) {
            return value;
        } else {
            // Adding 0.00001 removes problem in javascript floating round problem
            return Math.round(((value / window.DxBlockReport.workingDayH) + 0.00001) * 100) / 100;
        }
    },
    /**
     * Event on usccessful data retrieval for chart 
     * @param {object} data Data retrieved form server
     * @returns {undefined}
     */
    onGetChartDataSuccess: function (data) {
        window.DxBlockReport.loadTotalData(data.total);

        var barsGain = [];
        var barsLoss = [];
        var lineTotal = [];
        var categories = [];

        for (var i = 0; i < data.res.length; i++) {
            var row = data.res[i];

            categories.push([i, row.year + '/' + row.month]);

            if (Number(row.gain) > 0) {
                barsGain.push([i, window.DxBlockReport.calculateChartHours(row.gain)]);
            }

            if (Number(row.loss) > 0) {
                barsLoss.push([i, window.DxBlockReport.calculateChartHours(row.loss)]);
            }

            lineTotal.push([i, window.DxBlockReport.calculateChartHours(row.total)]);
        }

        $.plot("#dx-widget-report-chart-" + window.DxBlockReport.uid, [
            {
                data: barsLoss,
                bars: {
                    show: true,
                    barWidth: 0.4,
                    align: "left"
                },
                color: '#E7505A',
                label: Lang.get('reports.' + window.DxBlockReport.reportName + '.loss')
            },
            {
                data: barsGain,
                bars: {
                    show: true,
                    barWidth: 0.4,
                    align: "right"
                },
                color: '#26C281',
                label: Lang.get('reports.' + window.DxBlockReport.reportName + '.gain')
            }, {
                data: lineTotal,
                lines: {show: true},
                points: {show: true},
                color: '#3598DC',
                label: Lang.get('reports.' + window.DxBlockReport.reportName + '.total'),
                animator: {start: 100, steps: data.res.length, duration: 1000, direction: "right"}
            }],
                {
                    axisLabels: {
                        show: true
                    },
                    yaxes: [{
                            axisLabel: window.DxBlockReport.getUnit()
                        }],
                    xaxis: {
                        ticks: categories
                    },
                    grid: {
                        hoverable: true
                    }
                });

        window.DxBlockReport.hideLoading();
    },
    /**
     * Shows tooltip on chart hover
     * @param {object} event Event caller
     * @param {object} pos Mouse position
     * @param {object} item Hovered item
     * @returns {undefined}
     */
    onPlotHover: function (event, pos, item) {
        if (item) {
            var y = item.datapoint[1].toFixed(2);

            $("#dx-widget-report-chart-tooltip-" + window.DxBlockReport.uid).html(item.series.label + ": " + y + ' ' + window.DxBlockReport.getUnit())
                    .css({top: pos.pageY + 20, left: pos.pageX + 5})
                    .fadeIn(200);
        } else {
            $("#dx-widget-report-chart-tooltip-" + window.DxBlockReport.uid).hide();
        }
    },
    /**
     * Initiates date picker for filter
     * @returns {undefined}
     */
    initFilterDatePicker: function () {
        window.DxBlockReport.domObject.find('.dx-widget-report-filter-year-btn').click(function (event) {
            window.DxBlockReport.domObject.find('.dx-widget-report-filter-year-input').data('daterangepicker').toggle();
        });

        window.DxBlockReport.domObject.find('.dx-widget-report-filter-year-input').daterangepicker({
            locale: {
                "format": window.DxBlockReport.dateFormat,
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
            "startDate": window.DxBlockReport.dateFrom,
            "endDate": window.DxBlockReport.dateTo,
            "showDropdowns": true
        }, window.DxBlockReport.yearSelect);
    },
    /**
     * Event callback when filter's year value is selected
     * @param {object} e Event caller
     * @returns {undefined}
     */
    yearSelect: function (start, end, label) {
        window.DxBlockReport.dateFrom = start;
        window.DxBlockReport.dateTo = end;

        window.DxBlockReport.domObject.find('.dx-widget-report-curr-year').html(start.format(window.DxBlockReport.dateFormat) + ' - ' + end.format(window.DxBlockReport.dateFormat));

        window.DxBlockReport.refreshChart();
    },
    /**
     * Event callback when filter's time off type value is selected
     * @param {object} e Event caller
     * @returns {undefined}
     */
    groupSelect: function (e) {
        var btn = $(e.target);

        window.DxBlockReport.groupId = btn.data('value');
        window.DxBlockReport.groupTitle = btn.data('title');

        window.DxBlockReport.domObject.find('.dx-widget-report-curr-group').html(window.DxBlockReport.groupTitle);

        window.DxBlockReport.refreshChart();
    },
    /**
     * Shows loading box
     * @returns {undefined}
     */
    showLoading: function () {
        window.DxBlockReport.inProgressCount++;
        show_page_splash(1);
    },
    /**
     * Hides loading box
     * @returns {undefined}
     */
    hideLoading: function () {
        window.DxBlockReport.isSending = false;
        hide_page_splash(1);
    },
    /**
     * Event when ajax request gets error
     * @param {array} data Data containing error information
     * @returns {undefined}
     */
    onAjaxError: function (data) {
        window.DxBlockReport.hideLoading();
    }
};

$(document).ready(function () {
    $('.dx-widget-report-panel').each(function (index) {
        window.DxBlockReport.init($(this));
    });
});