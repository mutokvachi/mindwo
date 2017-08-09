(function($)
{
	/**
	 * DxScheduler - a jQuery plugin that renders rooms and student groups scheduling UI
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.DxScheduler = function(opts)
	{
		var options = $.extend({}, $.fn.DxScheduler.defaults, opts);
		return this.each(function()
		{
			new $.DxScheduler(this, options);
		});
	};
	
	$.fn.DxScheduler.defaults = {
            root_url: getBaseUrl(),
            scheduler_url: "calendar/scheduler/",
            group_prefix: "G"
	};
	
	/**
	 * DxScheduler constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.DxScheduler = function(root, opts)
	{
            $.data(root, 'DxScheduler', this);
            var self = this;
            this.options = opts;
            this.root = $(root);
            this.subjects_list_id = this.root.data('subjects-list-id');
            this.groups_list_id = this.root.data("groups-list-id");
            this.days_list_id = this.root.data("days-list-id"); 
            this.rooms_list_id = this.root.data("rooms-list-id");
            
            this.room_id = this.root.data("room-id");
            this.current_date = this.root.data("crrent-date");
            
            var newSubjectOpen = function() {
                open_form('form', 0, self.subjects_list_id, 0, 0, "", 1, "", {
                    after_close: function(frm)
                    {
                        var new_id = parseInt(frm.find("[name=item_id]").val());
                        if (new_id > 0 ) {
                            // add tp container
                            var n = $("<div>");
                            n.addClass('dx-event');
                            n.attr("data-subject-id", new_id);
                            
                            var sp = $("<span>").addClass("dx-item-title").text(frm.find("[name=title]").val());
                            sp.appendTo(n);                            
                            
                            n.appendTo("#external-events");
                            addDr(n);
                        }
                    }
                });
            };  
            
            this.root.find(".dx-new-btn").click(function() {                
                newSubjectOpen();
            });
            
            var newGroupOpen = function() {
                open_form('form', 0, self.groups_list_id, 0, 0, "", 1, "", {
                    after_close: function(frm)
                    {
                        var new_id = parseInt(frm.find("[name=item_id]").val());
                        if (new_id > 0 ) {
                            show_page_splash();
                            $(".dx-stick-footer").removeClass('dx-page-in-edit-mode');
                            window.location.reload();
                        }
                    }
                });
            };  
            
            this.root.find(".dx-new-group-btn").click(function() {                
                newGroupOpen();
            });
            
            this.root.find(".dx-rooms-cbo").change(function (event) {
                event.preventDefault();

                show_page_splash(1);
                var url = self.options.root_url + self.options.scheduler_url + self.root.find('.dx-rooms-cbo option:selected').val();
                window.location.assign(encodeURI(url));
            });
            
            this.root.find(".dx-room-edit-btn").click(function() {
                var room_id = parseInt(self.root.find('.dx-rooms-cbo option:selected').val());
                if (room_id) {
                    open_form('form', room_id, self.rooms_list_id, 0, 0, "", 0, "", {
                        after_close: function(frm)
                        {
                            // ToDo: reload rooms cbo
                        }
                    });
                } 
                else {
                    notify_err("Vispirms izvēlieties telpu no saraksta!");
                }
            });
           
           /*
            var undo_changes = function() {
                show_page_splash();
                $(".dx-stick-footer").removeClass('dx-page-in-edit-mode');
                window.location.reload();
            };
            
            this.root.find(".dx-undo-btn").click(function() {
                PageMain.showConfirm(undo_changes, null, Lang.get('constructor.menu.confirm_title'), Lang.get('constructor.menu.confirm_msg'), Lang.get('constructor.menu.confirm_yes'), Lang.get('constructor.menu.confirm_no'), null);
            });
            
            
            
            this.root.find(".dx-save-btn").click(function() {
                var arr = $('#calendar').fullCalendar( 'clientEvents' );
                arr.forEach(function(el) {
                    console.log(el.id + "|" + el.title + "|" + el.start + "|" + el.end + "|" + el.resourceId);
                });
               
                return;
                saveData();
            });
            
            var updateOutput = function (e) {
                var list = e.length ? e : $(e.target);
                if (window.JSON) {
                    self.nodes = window.JSON.stringify(list.nestable('serialize'));                    
                    $('.dx-stick-footer').addClass('dx-page-in-edit-mode');
                    self.root.find(".dx-undo-btn").show();
                } else {
                    notify_err(Lang.get('constructor.menu.err_json_support'));
                }
                
                $(".dd-item").removeClass("parentError");
                $(".dd-item").filter(function() {
                    return $(this).find(".dd-list").length !== 0 && ($(this).attr("data-list-id") != "0" || ($(this).attr("data-url") && $(this).attr("data-url") != "javascript:;"));
                }).addClass("parentError");
            };
            */
           
            var addDr = function(el, gr) {
                    // store data so the calendar knows to render an event upon drop
                    el.data('event', {
                            title: $.trim(el.find(".dx-item-title").text()), // use the element's text as the event title
                            stick: true, // maintain when user navigates (see docs on the renderEvent method)
                            className: (gr) ? gr : '',
                            duration: "02:00",
                            dx_subj_id: el.data("subject-id"),
                            dx_group_id: el.data("group-id"),
                            dx_day_id: 0,
                    });

                    // make the event draggable using jQuery UI
                    el.draggable({
                            start: function() {
                                el.css('width', '300px;');
                            },
                            stop: function() {
                                el.css('width', 'auto');
                            },
                            zIndex: 999,
                            revert: true,      // will cause the event to go back to its
                            revertDuration: 0,  //  original position after the drag
                            appendTo: 'body',
                            containment: 'window',
                            scroll: false,
                            helper: 'clone'
                    });
                    
                    if (gr) {
                        el.find(".dx-group-edit").click(function() {
                            open_form('form', el.data("group-id"), self.groups_list_id, 0, 0, "", 0, "", {
                                after_close: function(frm)
                                {
                                    el.attr('data-subject-id', frm.find("[name=subject_id]").val());
                                    el.find(".dx-item-title").text(self.options.group_prefix + el.data("group-id") + ": " + frm.find("[dx_fld_name=subject_id]").val());
                                    
                                    el.data('event', {
                                            title: $.trim(el.find(".dx-item-title").text()), // use the element's text as the event title
                                            stick: true, // maintain when user navigates (see docs on the renderEvent method)
                                            className: 'group',
                                            duration: "02:00",
                                            dx_subj_id: el.data("subject-id"),
                                            dx_group_id: el.data("group-id"),
                                            dx_day_id: 0,
                                    });
                                }
                            });
                        });
                    }
                    else {
                        el.find(".dx-subj-edit").click(function() {
                            open_form('form', el.data("subject-id"), self.subjects_list_id, 0, 0, "", 0, "", {
                                after_close: function(frm)
                                {
                                    el.find(".dx-item-title").text(frm.find("[name=title]").val());
                                    
                                    el.data('event', {
                                            title: $.trim(el.find(".dx-item-title").text()), // use the element's text as the event title
                                            stick: true, // maintain when user navigates (see docs on the renderEvent method)
                                            className: '',
                                            duration: "02:00",
                                            dx_subj_id: el.data("subject-id"),
                                            dx_group_id: 0,
                                            dx_day_id: 0,
                                    });
                                }
                            });
                        });
                    }
            };
            
            var addDrCafe = function(el) {
                
                // store data so the calendar knows to render an event upon drop
                el.data('event', {
                        title: 'Kafijas pauze', // use the element's text as the event title
                        stick: true, // maintain when user navigates (see docs on the renderEvent method),
                        duration: "00:30",
                        className: "cafe",
                        color: "#d6df32"
                });

                // make the event draggable using jQuery UI
                el.draggable({                           
                        zIndex: 999,
                        revert: true,      // will cause the event to go back to its
                        revertDuration: 0  //  original position after the drag
                });
            };
            
            addDrCafe($('.dx-cafe'));
            $('#external-events .dx-event').each(function() {
                    addDr($(this));
            });
            
            $('#dx-groups-box .dx-group').each(function() {
                    addDr($(this), "group");
            });
            
            this.root.find(".dx-search-subj").on("keyup", function()
            {
                    if(!$(this).val())
                    {
                            $("#external-events").find(".dx-event").show();
                            return;
                    }
                    $("#external-events").find(".dx-event").hide();
                    $("#external-events").find(".dx-event:contains('" + $(this).val() + "')").show();

            });
            
            this.root.find(".dx-search-group").on("keyup", function()
            {
                    if(!$(this).val())
                    {
                            $("#dx-groups-box").find(".dx-event").show();
                            return;
                    }
                    $("#dx-groups-box").find(".dx-event").hide();
                    $("#dx-groups-box").find(".dx-event:contains('" + $(this).val() + "')").show();

            });
            
            var newGroupHtml = function(arr_data) {
                var new_el = $("<div>");
                new_el.addClass('dx-event').addClass('dx-group');
                new_el.attr("data-subject-id", arr_data.subj_id);
                new_el.attr("data-group-id", arr_data.group_id);
                
                var sp = $("<span class='dx-item-title'></span>");
                sp.text(arr_data.text);
                sp.appendTo(new_el);
                
                new_el.appendTo( "#dx-groups-box" );
                
                var a = $('<a class="pull-right" href="javascript:;"><i class="fa fa-edit dx-group-edit"></i></a>');
                a.appendTo(new_el);
                addDr(new_el, "group");
            };
            
            var newCafeToDb = function(event) {
                console.log("Create new coffe pause in db!");
            };
            
            var newGroupToDb = function(event) {
                console.log("Create new group in db!");
                
                var formData = new FormData();
                formData.append("subject_id", event.dx_subj_id);
                formData.append("start_time", event.start.format("YYYY-MM-DD HH:mm"));
                formData.append("end_time", event.end.format("YYYY-MM-DD HH:mm"));
                formData.append("room_id", (event.resourceId) ? event.resourceId : self.room_id);
                
                var request = new FormAjaxRequest (self.options.scheduler_url + "new_group", '', '', formData);

                request.callback = function(data) {
                    event.id = data.day_id;
                    event.title = self.options.group_prefix + data.group_id + ": " + event.title;
                    event.className="group";
                    event.dx_day_id = data.day_id;
                    event.dx_group_id = data.group_id;
                    $('#calendar').fullCalendar( 'updateEvent', event );

                    newGroupHtml({subj_id: event.dx_subj_id, group_id: data.group_id, text: event.title});
                };
                
                request.err_callback = function() {                    
                    $('#calendar').fullCalendar( 'removeEvents', function(ev) {                        
                        if (!ev.id) {
                            return true;
                        }
                        return false;
                    });
                };

                request.doRequest();
            };
            
            var newDayToDb = function(event) {
                console.log("Create new day for existing group in db!");
                
                var formData = new FormData();
                formData.append("group_id", event.dx_group_id);
                formData.append("start_time", event.start.format("YYYY-MM-DD HH:mm"));
                formData.append("end_time", event.end.format("YYYY-MM-DD HH:mm"));
                formData.append("room_id", (event.resourceId) ? event.resourceId : self.room_id);
                
                var request = new FormAjaxRequest (self.options.scheduler_url + "new_day", '', '', formData);

                request.callback = function(data) {
                    event.id = data.day_id;
                    event.dx_day_id = data.day_id;
                    $('#calendar').fullCalendar( 'updateEvent', event );
                };
                
                request.err_callback = function() {                    
                    $('#calendar').fullCalendar( 'removeEvents', function(ev) {                        
                        if (!ev.id) {
                            return true;
                        }
                        return false;
                    });
                };

                request.doRequest();
            };
            
            var updateCafeToDb = function(event) {
                console.log("Update existing coffe pause in db!");
            };
            
            var updateDayToDb = function(event) {
                console.log("Update existing day in db!");
                
                var formData = new FormData();
                formData.append("day_id", event.dx_day_id);
                formData.append("start_time", event.start.format("YYYY-MM-DD HH:mm"));
                formData.append("end_time", event.end.format("YYYY-MM-DD HH:mm"));
                formData.append("room_id", (event.resourceId) ? event.resourceId : self.room_id);
                
                var request = new FormAjaxRequest (self.options.scheduler_url + "update_day", '', '', formData);

                request.callback = function(data) {                    
                    // do nothing - everything ok
                };
                
                request.err_callback = function() {                    
                   // ToDo: here we need somehow to rollback UI changes
                };

                request.doRequest();
            };
            
            var cal_tools = 'prev,next,today';
            var def_view = '';
            var rooms_arr = null;
            
            if (this.room_id) {
                cal_tools = cal_tools + ',month,agendaWeek,agendaDay';
                def_view = 'agendaWeek';
            }
            else {
                cal_tools = cal_tools + ',timelineDay,timelineThreeDays';
                def_view = 'timelineThreeDays';
                rooms_arr = this.root.data('rooms-json');
            }
            
            $('#calendar').fullCalendar({
                        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
			now: self.current_date,
                        weekends: false,
			editable: true,
                        droppable: true,
			aspectRatio: 1.8,
			scrollTime: '00:00',
                        displayEventTime: false,
                        allDaySlot: false,
                        resourceLabelText: "Telpas",
			header: {
				left: 'title',
                                center: '',
                                right: cal_tools
			},
                        locale: Lang.getLocale(),
			defaultView: def_view,
			views: {
				timelineThreeDays: {
					type: 'timeline',
					duration: { days: 7 },
                                        buttonText: '5 dienas'
				}
			},
                        minTime: "09:00:00",
                        maxTime: "18:00:00",
                        businessHours: {
                            // days of week. an array of zero-based day of week integers (0=Sunday)
                            dow: [ 1, 2, 3, 4 ], // Monday - Thursday

                            start: '09:00', // a start time (10am in this example)
                            end: '18:00', // an end time (6pm in this example)
                        },
                        eventRender: function (event, element) {
                            element.addClass('context-menu-one');
                            element.attr('data-subject-id', event.dx_subj_id);
                            element.attr('data-group-id', event.dx_group_id);
                            element.attr('data-day-id', event.dx_day_id);
                            element.attr('data-event-id', event.id);
                        },
                        eventReceive : function(event) {                            
                            if (event.className == "cafe") {
                                newCafeToDb(event);
                                return;
                            }
                            
                            if (event.className == "group") {
                                newDayToDb(event);
                            }
                            else {
                                newGroupToDb(event);
                            }
                        },
                        eventResize: function(event, delta, revertFunc) {
                            if (event.className == "cafe") {
                                updateCafeToDb(event);
                            }
                            else {
                                updateDayToDb(event);
                            }
                        },
                        eventDrop: function( event, delta, revertFunc, jsEvent, ui, view ){
                            if (!event.id) {
                                return;
                            }
                            
                            if (event.className == "cafe") {
                                updateCafeToDb(event);
                            }
                            else {
                                updateDayToDb(event);
                            }
                        },
                        eventClick: function(calEvent, jsEvent, view) {

                            if (calEvent.className == "cafe") {
                                alert("Coffe pause opening will be implemented");
                                return;
                            }
                            
                            open_form('form', calEvent.dx_day_id, self.days_list_id, 0, 0, "", 0, "", {
                                after_close: function(frm)
                                {
                                    // ToDo: check if something changed and referesh
                                }
                            });
                        },
			resourceGroupField: 'organization',
			resources: rooms_arr,
			events: this.root.data('events-json')
            });  
           
            $.contextMenu({
                selector: '.context-menu-one', 
                callback: function(key, options) {
                    if (key == "subject") {
                        open_form('form', options.$trigger.data('subject-id'), self.subjects_list_id, 0, 0, "", 0, "", {after_close: function(frm) {
                            // ToDo: refresh all page
                        }});
                    }
                    
                    if (key == "group") {
                        open_form('form', options.$trigger.data('group-id'), self.groups_list_id, 0, 0, "", 0, "", {after_close: function(frm) {
                            // ToDo: refresh all page
                        }});
                    }
                    
                    if (key == "day") {
                        open_form('form', options.$trigger.data('day-id'), self.days_list_id, 0, 0, "", 0, "", {after_close: function(frm) {
                            // ToDo: refresh all page
                        }});
                    }
                    
                    if (key == "delete") {
                        
                    }
                },
                items: {
                    "subject": {name: "Pasākums", icon: "fa-graduation-cap"},
                    "group": {name: "Grupa", icon: "fa-users"},
                    "day": {name: "Nodarbība", icon: "fa-calendar-o"},                    
                    "sep1": "---------",
                    "delete": {name: "Dzēst nodarbību", icon: "fa-trash-o"}
                }
            });

            $('.context-menu-one').on('click', function(e){
                console.log('clicked', this);
            })    
            
            $(window).on('beforeunload', function()
            {
                if($(".dx-stick-footer").hasClass('dx-page-in-edit-mode'))
                {
                    hide_page_splash(1);
                    return 'Your changes have not been saved.';
                }
            });
            
            // adjust menu for vertical menu UI
            if (!$("body").hasClass("dx-horizontal-menu-ui")) {
                
                var adjust_menu = function() {
                    $(".page-sidebar-menu").css("padding-bottom", '80px');
                };
                PageMain.addResizeCallback(adjust_menu);
                
                adjust_menu();
                
                $(".dx-menu-builder-stick-title").css("font-size", "14px");
                /*
                var dv = $(".dx-menu-sites").find("div.col-sm-10");
                dv.css("margin-right", "-35px");
                dv.css("padding-left", "30px");
                */
            }
	};
})(jQuery);