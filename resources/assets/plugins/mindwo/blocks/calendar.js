
(function ($)
{
    /**
     * Creates jQuery plugin for calendar widget 
     * @returns DxCalendar
     */
    $.fn.DxCalendar = function ()
    {
        return this.each(function ()
        {
            new $.DxCalendar($(this));
        });
    };

    /**
     * Class for managing calendar widget
     * @type DxCalendar 
     */
    $.DxCalendar = function (domObject) {
        /**
         * Worflow control's DOM object which is related to this class
         */
        this.domObject = domObject;

        /**
         * Source ID
         */
        this.source_id = 0;

        /**
         * Parameter if holidays are shown
         */
        this.show_holidays = 1;

        /**
         * Parameter if birthdays are shown
         */
        this.show_birthdays = 1;
        
        /**
         * Parameter of employee profile url
         */
        this.profile_url = "";

        // Initializes class
        this.init();
    };

    /**
     * Initializes widget
     * @returns {undefined}
     */
    $.extend($.DxCalendar.prototype, {
        /**
         * Draws calendar component
         * @returns {undefined}
         */
        renderCalendar: function () {
            var self = this;

            $('.dx-widget-calendar', self.domObject).fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                lang: Lang.getLocale(),
                buttonIcons: false, // show the prev/next text
                weekNumbers: false,
                editable: false,
                eventLimit: true, // allow "more" link when too many events
                allDaySlot: true,
                eventRender: function (event, element) {
                    element.find('.fc-title').html(event.title);
                },
                events: function (start, end, timezone, callback) {
                    self.getEvents(start, end, timezone, callback, self);
                }
            });
        },
        /**
         * Get events from database
         * @param {string} start Starting date for calendar view interval
         * @param {string} end Ending date for calendar view interval
         * @param {string} timezone Current timze zone
         * @param {function} callback Callback method which send prepared event dtaa
         * @param {DxCalendar} self Current class
         * @returns {undefined}
         */
        getEvents: function (start, end, timezone, callback, self) {
            show_page_splash(1);

            $.ajax({
                url: DX_CORE.site_url + 'calendar/events',
                type: 'POST',
                dataType: 'json',
                data: {
                    start: start.format(),
                    end: end.format(),
                    source_id: self.source_id,
                    show_holidays: self.show_holidays,
                    show_birthdays: self.show_birthdays
                },
                success: function (result) {
                    if (result && result.success && result.success == 1) {
                        var events = self.prepareEvents(result.data, start.format('YYYY'), end.format('YYYY'));

                        callback(events);

                        self.initiPopovers(self);
                    }
                    hide_page_splash(1);
                },
                error: function () {
                    hide_page_splash(1);
                }
            });
        },
        /**
         * Initializes popovers
         * @param {DxCalendar} self Current class
         * @returns {undefined}
         */
        initiPopovers: function (self) {
            self.domObject.on("mouseenter", '.dx-widget-calendar-event', function () {
                var _this = this;

                // Hide all other popovers
                $('.dx-widget-calendar-event').not(this).popover('hide');

                $(this).popover("show");
                $(".popover").on("mouseleave", function () {
                    $(_this).popover('hide');
                });
            }).on("mouseleave", '.dx-widget-calendar-event', function () {
                var _this = this;
                setTimeout(function () {
                    if (!$(".popover:hover").length) {
                        $(_this).popover("hide");
                    }
                }, 300);
            });
        },
        /**
         * Prepare events from raw server data
         * @param {object} block Galerijas bloka elements
         * @param {int} startYear Starting year for filter
         * @param {int} endYear End year for filter 
         * @returns {undefined}
         */
        prepareEvents: function (events_raw, startYear, endYear) {
            var events_items = [];

            if (events_raw.enterprise) {
                var event;
                for (var key in events_raw.enterprise) {
                    event = events_raw.enterprise[key];

                    events_items.push({
                        title: event.title,
                        start: event.start,
                        end: event.end,
                        url: 'JavaScript: get_popup_item_by_id(' + event.id + ', "event", "Kalendāra notikums");',
                        color: event.color
                    });
                }
            }

            if (events_raw.holidays) {
                var holidayTitle = Lang.get('calendar.holiday');

                var event;
                for (var date in events_raw.holidays) {
                    var titles = events_raw.holidays[date];

                    var content = '';
                    for (var title in titles) {
                        content += '<div>' + title + ' (' + titles[title].join(', ') + ')</div>';
                    }

                    /* 
                     data-trigger="manual" \n\
                     data-animation="false" \n\
                     */

                    var eventHtml = '<div class="popovers dx-widget-calendar-event" style="width:100%; z-index:1000; position:relative" \n\
                        data-html="true" \n\
                        data-placement="auto" \n\
                        data-trigger="manual" \n\
                        data-animation="false" \n\
                        data-container="body" \n\
                        data-content="' + content + '" \n\
                        data-original-title="' + holidayTitle + '">\n\
                        <i class="fa fa-flag-o"></i> ' +
                            holidayTitle + '</div>';

                    events_items.push({
                        title: eventHtml,
                        start: date,
                        end: null,
                        color: '#EF4836',
                        allDay: true
                    });
                }
            }

            if (events_raw.birthdays) {
                var birthdaysTitle = Lang.get('calendar.birthdays');

                var event;
                for (var date in events_raw.birthdays) {
                    var birthdayUsers = events_raw.birthdays[date];

                    var content = '';
                    for (var b = 0; b < birthdayUsers.length; b++) {
                        var birthdayUser = birthdayUsers[b];
                        if (this.profile_url.length > 0) {
                            content += '<div><a href=\'' + DX_CORE.site_url + 'employee/profile/' + birthdayUser.id + '\'>' + birthdayUser.name + '</a></div>';
                        }
                        else {
                            content += '<div>' +  birthdayUser.name + '</div>';
                        }
                    }

                    var eventHtml = '<div class="popovers dx-widget-calendar-event" style="width:100%" \n\
                        data-html="true" \n\
                        data-trigger="manual" \n\
                        data-animation="false" \n\
                        data-container="body" \n\
                        data-placement="bottom" \n\
                        data-content="' + content + '" \n\
                        data-original-title="' + birthdaysTitle + '">\n\
                        <i class="fa fa-birthday-cake"></i> ' +
                            birthdaysTitle + '</div>';

                    events_items.push({
                        title: eventHtml,
                        start: date,
                        end: null,
                        color: '#f3c200',
                        allDay: true
                    });

                    for (var y = +startYear; y <= +endYear; y++) {

                    }
                }
            }

            return events_items;
        },
        /**
         * Initializes calendar widget
         * @returns {undefined}
         */
        init: function () {
            this.source_id = this.domObject.data('source_id');
            this.show_holidays = this.domObject.data('show_holidays');
            this.show_birthdays = this.domObject.data('show_birthdays');
            this.profile_url = this.domObject.data('profile-url');
            
            this.renderCalendar();

            this.domObject.data('dx_block_init', 1);
        }
    });
})(jQuery);

$(document).ready(function () {
    // Initializes all calendar widgets
    $(".dx-block-container-calendar[data-dx_block_init='0']").DxCalendar();
});

