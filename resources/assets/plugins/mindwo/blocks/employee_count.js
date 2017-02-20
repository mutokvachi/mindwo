
(function ($)
{
    /**
     * Creates jQuery plugin for calendar widget 
     * @returns DxBlockEmployeeCount
     */
    $.fn.DxBlockEmployeeCount = function ()
    {
        return this.each(function ()
        {
            new $.DxBlockEmployeeCount($(this));
        });
    };

    /**
     * Class for managing calendar widget
     * @type DxBlockEmployeeCount 
     */
    $.DxBlockEmployeeCount = function (domObject) {
        /**
         * Worflow control's DOM object which is related to this class
         */
        this.domObject = domObject;

        /**
         * Curretn date format
         */
        this.dateFormat = 0;

        // Initializes class
        this.init();
    };

    /**
     * Initializes widget
     * @returns {undefined}
     */
    $.extend($.DxBlockEmployeeCount.prototype, {
        /**
         * Get events from database
         * @param {string} start Starting date for calendar view interval
         * @param {string} end Ending date for calendar view interval
         * @param {string} timezone Current timze zone
         * @param {function} callback Callback method which send prepared event dtaa
         * @param {DxBlockEmployeeCount} self Current class
         * @returns {undefined}
         */
        filterData: function (self, date) {
            show_page_splash(1);

            $.ajax({
                url: DX_CORE.site_url + 'widget/eployeecount/get/view',
                type: 'POST',
                dataType: 'json',
                data: {
                    date: date,
                    widget_name: self.widgetName
                },
                success: function (result) {
                    if (result && result.success && result.success == 1) {
                        $('dx-widget-employeecount-body').html(result.view);
                        $('dx-widget-employeecount-total').html(result.total_count);
                    }
                    hide_page_splash(1);
                },
                error: function (e) {
                    hide_page_splash(1);
                }
            });
        },
        getTodayDate: function () {
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth() + 1; //January is 0!
            var yyyy = today.getFullYear();

            if (dd < 10) {
                dd = '0' + dd
            }

            if (mm < 10) {
                mm = '0' + mm
            }

            today = yyyy + '-' + mm + '-' + dd;

            return today;
        },
        /**
         * Initializes widget
         * @returns {undefined}
         */
        init: function () {
            var self = this;

            self.dateFormat = self.domObject.data('date_format');
            self.widgetName = self.domObject.data('widget_name');

            $('.dx-widget-employeecount-filter', this.domObject).datetimepicker({
                minView: 'month',
                startView: 'month',
                timepicker: false,
                closeOnDateSelect: true,
                locale: Lang.getLocale(),
                onSelectDate: function (ct, $i) {
                    self.changeDateFilter(self, ct, $i);
                }
            });

            this.domObject.data('dx_block_init', 1);
        },
        changeDateFilter: function (self, ct, $i) {
            var date = ct.dateFormat('Y-m-d');
            var displayDate = ct.dateFormat(self.dateFormat);
            var today = self.getTodayDate();

            $('.dx-widget-employeecount-filter-label', self.domObject).html(today == date ? Lang.get('date_range.flt_today') : displayDate);
            
            self.filterData(self, date);
        }
    });
})(jQuery);

$(document).ready(function () {
    // Initializes all report widgets
    $(".dx-widget-employeecount[data-dx_block_init='0']").DxBlockEmployeeCount();
});
