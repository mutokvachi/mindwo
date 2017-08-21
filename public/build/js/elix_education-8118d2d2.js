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

            this.catalog = new $.DxEduCatalog($(this));
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

            show_page_splash(1);

            // Opens chat window
            self.domObject.find('.dx-edu-catalog-btn-filter-detailed').on('click', function () {
                self.onClickToogleAdvancedFilter(self);
            });

            self.domObject.find('select').multiselect({
                buttonWidth: '100%'
            });

            self.domObject.find('.dx-edu-datetime-field').daterangepicker({
                locale: {
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
                "showDropdowns": true,
                "linkedCalendars": false
            });

            self.domObject.find('.dx-edu-datetime-field').each(function(){
                $(this).val('');
            });

            self.domObject.find('.dx-edu-time-field').timepicker({
                template: 'dropdown',
                autoclose: true,
                minuteStep: 10,
                defaultTime: '',
                showSeconds: false,
                showMeridian: false,
                showInputs: true
            });

            self.domObject.find('.dx-edu-catalog-btn-search').on('click', function () {
                self.search(self);
            });

            self.domObject.find('.dx-edu-catalog-btn-filter-clear').on('click',function () {
                self.clearFilter(self);
            })

            self.search(self);
        },
        clearFilter: function (self) {
            self.domObject.find('.dx-edu-catalog-filter-text').val('');
            self.domObject.find('.dx-edu-catalog-filter-tag').multiselect('deselectAll', false);
            self.domObject.find('.dx-edu-catalog-filter-program').multiselect('deselectAll', false);
            self.domObject.find('.dx-edu-catalog-filter-module').multiselect('deselectAll', false);
            self.domObject.find('.dx-edu-catalog-filter-teacher').multiselect('deselectAll', false);
            self.domObject.find('.dx-edu-catalog-filter-tag').multiselect('refresh');
            self.domObject.find('.dx-edu-catalog-filter-program').multiselect('refresh');
            self.domObject.find('.dx-edu-catalog-filter-module').multiselect('refresh');
            self.domObject.find('.dx-edu-catalog-filter-teacher').multiselect('refresh');
            self.domObject.find('.dx-edu-catalog-filter-date').val('');
            self.domObject.find('.dx-edu-catalog-filter-time_from').val('');
            self.domObject.find('.dx-edu-catalog-filter-time_to').val('');
            self.domObject.find('.dx-edu-catalog-filter-only_free').bootstrapSwitch('state', false);
            self.domObject.find('.dx-edu-catalog-filter-show_full').bootstrapSwitch('state', true);
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
        },
        search: function (self) {
            show_page_splash(1);

            var datePicker = self.domObject.find('.dx-edu-catalog-filter-date').data('daterangepicker');
            var datePickerIsEmpty = self.domObject.find('.dx-edu-catalog-filter-date').val() == '';

            var data = {
                text: self.domObject.find('.dx-edu-catalog-filter-text').val(),
                tag: self.domObject.find('.dx-edu-catalog-filter-tag').val(),
                program: self.domObject.find('.dx-edu-catalog-filter-program').val(),
                module: self.domObject.find('.dx-edu-catalog-filter-module').val(),
                teacher: self.domObject.find('.dx-edu-catalog-filter-teacher').val(),
                date_from: datePickerIsEmpty ? '' : datePicker.startDate.format('YYYY-MM-DD'),
                date_to: datePickerIsEmpty ? '' : datePicker.endDate.format('YYYY-MM-DD'),
                time_from: self.domObject.find('.dx-edu-catalog-filter-time_from').val(),
                time_to: self.domObject.find('.dx-edu-catalog-filter-time_to').val(),
                only_free: self.domObject.find('.dx-edu-catalog-filter-only_free').bootstrapSwitch('state') ? 1 : 0,
                show_full: self.domObject.find('.dx-edu-catalog-filter-show_full').bootstrapSwitch('state') ? 1 : 0,
            };

            $.ajax({
                url: DX_CORE.site_url + 'edu/catalog/search',
                data: data,
                type: "get",
                success: function (res) {
                    hide_page_splash(1);

                    if (res && res.success && res.success == 1) {
                        self.domObject.find('.dx-edu-catalog-body').html(res.html);
                    } else if (res && res.msg) {
                        notify_err(res.msg);
                    } else {
                        notify_err("Kļūda ielādējot datus");
                    }
                },
                error: function (err) {
                    hide_page_splash(1);

                    notify_err("Kļūda ielādējot datus");
                }
            });
        }
    });
})(jQuery);

// ajaxComplete ready
$(document).ready(function () {
    // Initializes window
    $('.dx-edu-catalog-page').DxEduCatalog();
});

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

//# sourceMappingURL=elix_education.js.map
