(function ($) {
    /**
     * Creates jQuery plugin for education course detailed view
     * @returns DxEduCourse
     */
    $.fn.DxEduCourse = function () {
        return this.each(function () {
            if ($(this).data('dx_is_init') == 1) {
                return;
            }

            this.course = new $.DxEduCourse($(this));
        });
    };

    /**
     * Class for education module course detailed view
     * @type DxCryptoField 
     */
    $.DxEduCourse = function (domObject) {
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
    $.extend($.DxEduCourse.prototype, {
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
            self.domObject.find('.dx-edu-course-feedback-btn-save').on('click', function () {
                self.saveFeedback(self);
            });


        },
        saveFeedback: function (self) {
            show_page_splash(1);

            var data = {
                author: self.domObject.find('.dx-edu-course-feedback-author').val(),
                email: self.domObject.find('.dx-edu-course-feedback-email').val(),
                text: self.domObject.find('.dx-edu-course-feedback-text').val(),
                subject_id: self.domObject.data('dx-subject_id')
            };

            if (data.author.length <= 0 || data.text.length <= 0 || data.email.length <= 0) {
                hide_page_splash(1);
                notify_err("Lūdzu aizpildiet visus laukus!");
                return;
            }

            $.ajax({
                url: DX_CORE.site_url + 'edu/course_feedback',
                data: data,
                type: "post",
                success: function (res) {
                    hide_page_splash(1);

                    if (res && res.success && res.success == 1) {
                        notify_info('Atsauksme veiksmīgi pieņemta! Drīzumā tā tiks publicēta.');
                        self.domObject.find('.dx-edu-course-feedback-author').val('')
                        self.domObject.find('.dx-edu-course-feedback-email').val('');
                        self.domObject.find('.dx-edu-course-feedback-text').val('');
                    } else if (res && res.msg) {
                        notify_err(res.msg);
                    } else {
                        notify_err("Kļūda saglabājot atsauksmi");
                    }
                },
                error: function (err) {
                    hide_page_splash(1);

                    notify_err("Kļūda saglabājot atsauksmi");
                }
            });
        }
    });
})(jQuery);

// ajaxComplete ready
$(document).ready(function () {
    // Initializes window
    $('.dx-edu-course-page').DxEduCourse();
});
