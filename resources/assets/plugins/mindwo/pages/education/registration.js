(function ($) {
    /**
     * Creates jQuery plugin for education course registration
     * @returns DxEduRegistration
     */
    $.fn.DxEduRegistration = function () {
        return this.each(function () {
            if ($(this).data('dx_is_init') == 1) {
                return;
            }

            this.chat = new $.DxEduRegistration($(this));
        });
    };

    /**
     * Class for education course registration
     * @type DxCryptoField 
     */
    $.DxEduRegistration = function (domObject) {
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
    $.extend($.DxEduRegistration.prototype, {
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

            show_page_splash(1);

           /* self.domObject.find('.dx-edu-datetime-field').each(function(){
                $(this).val('');
            });

            self.domObject.find('.dx-edu-catalog-btn-search').on('click', function () {
                self.search(self);
            });*/

            self.search(self);
        }
    });
})(jQuery);

// ajaxComplete ready
$(document).ready(function () {
    // Initializes chat window
    $('.dx-edu-registration-page').DxEduRegistration();
});
