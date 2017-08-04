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
            self.domObject.find('#dx-edu-catalog-btn-filter-detailed').click(self.onClickToogleAdvancedFilter);

            self.domObject.find('select').multiselect({
                buttonWidth: '100%'
            });
            
            self.domObject.find('.dx-edu-datetime-field').daterangepicker();

            self.domObject.find('.dx-edu-time-field').timepicker({
                autoclose:!0,
                minuteStep:30,
                showSeconds:0,
                showMeridian:0});
        },
        /**
         * Shows or hides advanced filter
         */
        onClickToogleAdvancedFilter: function () {
            var icon = $(this).find('.fa');

            if (icon) {
                icon.toggleClass('fa-caret-down fa-caret-up');
            } else {
               icon.toggleClass('fa-caret-up fa-caret-down');
            }
        }
    });
})(jQuery);

// ajaxComplete ready
$(document).ready(function () {
    // Initializes chat window
    $('.dx-edu-catalog-page').DxEduCatalog();
});

//# sourceMappingURL=elix_education.js.map
