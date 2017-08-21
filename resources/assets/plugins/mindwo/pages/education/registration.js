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

            this.registration = new $.DxEduRegistration($(this));
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

        this.groupOptionStack = [];

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

            self.domObject.find('.dx-edu-reg-btn-add-group').click(function(){
                self.domObject.find('#dx-edu-modal-group').modal('show');
            });

            self.domObject.find('.dx-edu-reg-btn-add-participant').on('click', function () {
                self.domObject.find('#dx-edu-modal-participant').modal('show');
            });

            $('.dx-edu-modal-group-select-course').on('change', function (){
                self.loadGroupsBySubject(self, this.value);
            });

            self.initGroupModalGroupSelect(self);
            $('.dx-edu-modal-group-select-course').change();

           /* self.domObject.find('.dx-edu-datetime-field').each(function(){
                $(this).val('');
            });

            self.domObject.find('.dx-edu-catalog-btn-search').on('click', function () {
                self.search(self);
            });*/

            hide_page_splash(1);
        },
        /**
         * Initializes group select box in group modal window
         */
        initGroupModalGroupSelect: function(self){
            var options = $('.dx-edu-modal-group-select-group option');
            
            for(var i = 0; i < options.length; i++){
                var subject_id = $(options[i]).data('subject-id');

                if(!(subject_id in self.groupOptionStack)){
                    self.groupOptionStack[subject_id] = [];
                }

                self.groupOptionStack[subject_id].push(options[i]);
                $(options[i]).remove();               
            }
        },
        /**
         * Loads groups in group modal
         */
        loadGroupsBySubject: function(self, value){
            var groupSelect = $('.dx-edu-modal-group-select-group');
            var options = groupSelect.find('option').remove();

            if(value in self.groupOptionStack){
                var groupOptions = self.groupOptionStack[value];
                
                for(var i = 0; i < groupOptions.length; i++){
                    groupSelect.append(groupOptions[i]);
                }
            } else {
                groupSelect.append('<option>Kursam nav pieejamu grupu</option>');
            }
        }
    });
})(jQuery);

// ajaxComplete ready
$(document).ready(function () {
    // Initializes chat window
    $('.dx-edu-registration-page').DxEduRegistration();
});
