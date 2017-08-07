(function ($) {
    /**
     * Creates jQuery plugin for education module catalog
     * @returns DxEduCatalog
     */
    $.fn.DxEduCatalog = function () {
        return this.each(function () {
            if ($(this).data('dx_is_init') == 1) {
                return;
            }

            this.chat = new $.DxEduCatalog($(this));
        });
    };

    /**
     * Class for education module catalog
     * @type DxCryptoField 
     */
    $.DxEduCatalog = function (domObject) {
        /**
         * Field's DOM object which is related to this class
         */
        this.domObject = domObject;

        // Initializes class
        this.init();
    };

    /**
     * Initializes component
     * @returns {undefined}
     */
    $.extend($.DxEduCatalog.prototype, {
        /**
         * Initializes field
         * @returns {undefined}
         */
        init: function () {
            var self = this;

            if (self.domObject.data('dx_is_init') == 1) {
                return;
            }

            self.domObject.data('dx_is_init', 1);

            // Opens chat window
            self.domObject.find('.dx-edu-catalog-btn-filter-detailed').on('click', function () {
                self.onClickToogleAdvancedFilter(self);
            });

            self.domObject.find('select').multiselect({
                buttonWidth: '100%'
            });

            self.domObject.find('.dx-edu-datetime-field').daterangepicker();

            self.domObject.find('.dx-edu-time-field').timepicker({
                template: 'dropdown',
                autoclose: true,
                minuteStep: 10,
                defaultTime: '0:00',
                showSeconds: false,
                showMeridian: false,
                showInputs: true
            });
        },
        /**
         * Shows or hides advanced filter
         */
        onClickToogleAdvancedFilter: function (self) {
            var icon = self.domObject.find('.dx-edu-catalog-btn-filter-detailed').find('.fa');

            icon.each(function () {
                if ($(this)) {
                    $(this).toggleClass('fa-caret-down fa-caret-up');
                } else {
                    $(this).toggleClass('fa-caret-up fa-caret-down');
                }
            });
        }
    });
})(jQuery);

// ajaxComplete ready
$(document).ready(function () {
    // Initializes chat window
    $('.dx-edu-catalog-page').DxEduCatalog();
});

//# sourceMappingURL=elix_education.js.map
