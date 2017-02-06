(function ($)
{
    /**
     * Creates jQuery plugin for report widget
     * @returns DxBlockReport
     */
    $.fn.DxBlockReport = function ()
    {
        return this.each(function ()
        {
            new $.DxBlockReport($(this));
        });
    };

    /**
     * Class for managing repot widget
     * @type Window.DxBlockReport 
     */
    $.DxBlockReport = function (domObject) {
        /**
         * Report controls DOM object which is related to this class
         */
        this.domObject = domObject;

        /**
         * Report's name
         */
        this.reportName = '';

        /**
         * Report column's data
         */
        this.reportColumns = [];

        /**
         * Unique ID for control
         */
        this.uid = 0;

        /**
         * Parameter if control is loaded
         */
        this.isLoaded = false;

        /**
         * Parameter if data is sending to server
         */
        this.isSending = false;

        /**
         * Parameter if table has been initialized
         */
        this.isTableInit = false;

        /**
         * Parameter if chart has been initialized
         */
        this.isChartInit = false;

        /**
         * Current filter's date from value
         */
        this.dateFrom = '';

        /**
         * Current filter's date to value
         */
        this.dateTo = '';

        /**
         * Current filter's time off value
         */
        this.groupId = 0;

        /**
         * Current filter's time off types title
         */
        this.groupTitle = '';

        /**
         * Parameter if current filter's time off type is in hours or days
         */
        this.timeoffIsAccrualHours = 1;

        /**
         * Date format used in system. This format is used to initialize date picker
         */
        this.dateFormat = '';

        /**
         * Working days length in hours
         */
        this.workingDayH = '';

        // Initializes class
        this.init();
    }

    /**
     * Initializes component
     * @returns {undefined}
     */
    $.extend($.DxBlockReport.prototype, {
        init: function () {
            var self = this;
            
            if (this.isLoaded) {
                return;
            }

            this.showLoading();

            this.uid = this.domObject.data('uid');
            this.reportName = this.domObject.data('report_name');
            this.reportColumns = this.domObject.data('report_columns');
            this.workingDayH = this.domObject.data('working_day_h');
            this.dateFormat = this.domObject.data('date_format').toUpperCase();
            this.dateFrom = new Date(this.domObject.data('date_from'));
            this.dateTo = new Date(this.domObject.data('date_to'));

            this.domObject.find('.dx-widget-report-sel-group').click(function(e){
                self.groupSelect(e, self);            
            });

            this.initFilterDatePicker();

            // Load chart
            this.loadChart();

            this.isLoaded = true;
        },
        /**
         * Get filter parameters for adding to request when getting chart
         * @returns {String} Reteived URL part containign parameters
         */
        getFilterParams: function () {
            return this.reportName + '/' + this.groupId + '/' + (this.dateFrom / 1000) + '/' + (this.dateTo / 1000);
        },
        /**
         * Reload chart data. Initializes component if needed
         * @returns {undefined}
         */
        loadChart: function () {
            if (!this.isChartInit) {
                this.initChart();
                this.isChartInit = true;
            }
        },
        /**
         * Initializes chart
         * @returns {undefined}
         */
        initChart: function () {
            var self = this;

            this.showLoading();

            $("<div id='dx-widget-report-chart-tooltip-" + this.uid + "' class='dx-widget-report-chart-tooltip'></div>").appendTo("body");

            $("#dx-widget-report-chart-" + this.uid).bind("plothover", function (event, pos, item) {
                self.onPlotHover(event, pos, item, self);
            });

            this.refreshChart();
        },
        /**
         * Refreshes chart data
         * @returns {undefined}
         */
        refreshChart: function () {
            this.showLoading();

            $.ajax({
                url: DX_CORE.site_url + 'widget/report/get/chart/' + this.getFilterParams(),
                type: "get",
                context: this,
                success: this.onGetChartDataSuccess,
                error: this.onAjaxError
            });
        },
        /**
         * Gets unit name for current filtered value
         * @returns {string} Units name
         */
        getUnit: function () {
            return Lang.get('reports.' + this.reportName + '.unit');
        },
        /**
         * Load total values for specified period into panel
         * @param {array} data Data contaning period total data
         * @returns {undefined}
         */
        loadTotalData: function (data) {
            var self = this;

            if (data) {
                var unit = self.getUnit();

                $.each(self.reportColumns, function (key, value) {
                    self.domObject.find('.dx-widget-report-period-' + key).html(self.calculateChartHours(data[key]) + ' ' + unit);
                });
            } else {
                $.each(self.reportColumns, function (key, value) {
                    self.domObject.find('.dx-widget-report-period-' + key).html();
                });
            }
        },
        /**
         * If needed convert hours to days
         * @param {Number} value Input hours
         * @returns {Number} Output value, could be in days or hours
         */
        calculateChartHours: function (value) {
            if (this.timeoffIsAccrualHours == 1) {
                return value;
            } else {
                // Adding 0.00001 removes problem in javascript floating round problem
                return Math.round(((value / this.workingDayH) + 0.00001) * 100) / 100;
            }
        },
        /**
         * Event on usccessful data retrieval for chart 
         * @param {object} data Data retrieved form server
         * @returns {undefined}
         */
        onGetChartDataSuccess: function (data) {
            var self = this;

            this.timeoffIsAccrualHours = data.is_hours;

            self.loadTotalData(data.total);

            var categories = [];

            var chart_options = [];

            var order = 1;

            var bar_width = (0.8) / (Object.keys(self.reportColumns).length - 1);

            $.each(self.reportColumns, function (col_key, col) {
                // Sets color and text for bar or line
                var option = {
                    color: col.color,
                    label: col.title
                };

                // Fills data
                option.data = [];

                for (var i = 0; i < data.res.length; i++) {
                    var row = data.res[i];

                    if (col.is_bar && Number(row[col_key]) > 0) {
                        option.data.push([i, self.calculateChartHours(row[col_key])]);
                    } else {
                        option.data.push([i, self.calculateChartHours(row[col_key])]);
                    }
                }

                // Set bar or line settings
                if (col.is_bar) {
                    option.bars = {
                        show: true,
                        barWidth: bar_width,
                        order: order++
                    };
                } else {
                    option.lines = {show: true};
                    option.points = {show: true};
                }

                chart_options.push(option);
            });

            for (var i = 0; i < data.res.length; i++) {
                var row = data.res[i];

                categories.push([i, row.year + '/' + row.month]);
            }

            $.plot("#dx-widget-report-chart-" + self.uid, chart_options,
                    {
                        axisLabels: {
                            show: true
                        },
                        yaxis: {
                            axisLabel: self.getUnit(),
                            tickDecimals: 0,
                            minTickSize: 1,
                            min: 0
                        },
                        xaxis: {
                            ticks: categories
                        },
                        grid: {
                            hoverable: true
                        }
                    });

            self.hideLoading();
        },
        /**
         * Shows tooltip on chart hover
         * @param {object} event Event caller
         * @param {object} pos Mouse position
         * @param {object} item Hovered item
         * @param {DxBlockReport} self Report object
         * @returns {undefined}
         */
        onPlotHover: function (event, pos, item, self) {
            if (item) {
                var y = item.datapoint[1];
                
                // If days then do not round 
                if(this.timeoffIsAccrualHours ==  1){
                    y = y.toFixed(0);
                }

                $("#dx-widget-report-chart-tooltip-" + self.uid).html(item.series.label + ": " + y + ' ' + self.getUnit())
                        .css({top: pos.pageY + 20, left: pos.pageX + 5})
                        .fadeIn(200);
            } else {
                $("#dx-widget-report-chart-tooltip-" + self.uid).hide();
            }
        },
        /**
         * Initiates date picker for filter
         * @returns {undefined}
         */
        initFilterDatePicker: function () {
            var self = this;

            this.domObject.find('.dx-widget-report-filter-year-btn').click(function (event) {
                self.domObject.find('.dx-widget-report-filter-year-input').data('daterangepicker').toggle();
            });

            this.domObject.find('.dx-widget-report-filter-year-input').daterangepicker({
                locale: {
                    "format": this.dateFormat,
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
                "startDate": this.dateFrom,
                "endDate": this.dateTo,
                "showDropdowns": true
            }, function (start, end, label) {
                self.yearSelect(start, end, label, self);
            });
        },
        /**
         * Event callback when filter's year value is selected
         * @param {string} start Starting date
         * @param {string} end Ending date
         * @param {string} label Label
         * @param {DxBlockReport} self Report object
         * @returns {undefined}
         */
        yearSelect: function (start, end, label, self) {
            self.dateFrom = start;
            self.dateTo = end;

            self.domObject.find('.dx-widget-report-curr-year').html(start.format(self.dateFormat) + ' - ' + end.format(self.dateFormat));

            self.refreshChart();
        },
        /**
         * Event callback when filter's time off type value is selected
         * @param {object} e Event caller
         * @param {DxBlockReport} self Report object
         * @returns {undefined}
         */
        groupSelect: function (e, self) {
            var btn = $(e.target);

            self.groupId = btn.data('value');
            self.groupTitle = btn.data('title');

            self.domObject.find('.dx-widget-report-curr-group').html(self.groupTitle);

            self.refreshChart();
        },
        /**
         * Shows loading box
         * @returns {undefined}
         */
        showLoading: function () {
            this.inProgressCount++;
            show_page_splash(1);
        },
        /**
         * Hides loading box
         * @returns {undefined}
         */
        hideLoading: function () {
            this.isSending = false;
            hide_page_splash(1);
        },
        /**
         * Event when ajax request gets error
         * @param {array} data Data containing error information
         * @returns {undefined}
         */
        onAjaxError: function (data) {
            this.hideLoading();
        }
    });
})(jQuery);

$(document).ready(function () {
    // Initializes all report widgets
    $('.dx-widget-report-panel').DxBlockReport();
});
