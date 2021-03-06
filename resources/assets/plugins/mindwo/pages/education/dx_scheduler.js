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
            this.coffee_list_id = this.root.data("coffee-list-id");
            this.cbo_rooms_refreshing = false;
            this.publish_ids = "";
            this.popup_mode = "publish";
            
            this.room_id = this.root.data("room-id");
            this.current_date = this.root.data("current-date");
            
            var addSubjToDiv = function(new_id, title) {
                //<div class='dx-event' data-subject-id="{{ $subj->id }}"><span class="dx-item-title">{{ $subj->title_full }}</span><a class="pull-right" href="javascript:;"><i class="fa fa-edit dx-subj-edit"></i></a></div>
                var n = $("<div>");
                n.addClass('dx-event');
                n.attr("data-subject-id", new_id);

                var sp = $("<span>").addClass("dx-item-title").text(title);
                sp.appendTo(n);                            

                var a = $("<a class='pull-right dx-subj-edit' href='javascript:;'><i class='fa fa-edit'></i></a>");
                a.appendTo(n);
                n.appendTo("#external-events");
                addDr(n);
            };
            
            var newSubjectOpen = function() {
                open_form('form', 0, self.subjects_list_id, 0, 0, "", 1, "", {
                    after_close: function(frm)
                    {
                        var new_id = parseInt(frm.find("[name=item_id]").val());
                        if (new_id > 0 ) {
                            // add to container
                            addSubjToDiv(new_id, frm.find("[name=title]").val());
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
                            refreshAllData();
                        }
                    }
                });
            };  
            
            this.root.find(".dx-new-group-btn").click(function() {                
                newGroupOpen();
            });
            
            this.root.find(".dx-rooms-cbo").change(function (event) {
                
                if (self.cbo_rooms_refreshing) {
                    return false;
                }
                
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
                            refreshAllData();
                        }
                    });
                } 
                else {
                    notify_err("Vispirms izvēlieties telpu no saraksta!");
                }
            });
            
            this.root.find(".dx-room-new-btn").click(function() {
                open_form('form', 0, self.rooms_list_id, 0, 0, "", 1, "", {
                    after_close: function(frm)
                    {
                        var new_room_id = frm.find('[name=item_id]').val();
                        if (new_room_id) {
                            show_page_splash(1);
                            var url = self.options.root_url + self.options.scheduler_url + new_room_id;
                            window.location.assign(encodeURI(url));
                        }
                    }
                });
            });
           
            var addDr = function(el, gr) {
                    // store data so the calendar knows to render an event upon drop
                    el.data('event', {
                            title: $.trim(el.find(".dx-item-title").text()), // use the element's text as the event title
                            stick: true, // maintain when user navigates (see docs on the renderEvent method)
                            className: (gr) ? gr : '',
                            duration: "02:00",
                            start: "09:00",
                            dx_subj_id: el.data("subject-id"),
                            dx_group_id: el.data("group-id"),
                            dx_day_id: 0,
                            dx_coffee_id: 0,
                            dx_is_published: 0
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
                                    refreshAllData();
                                }
                            });
                        });
                    }
                    else {
                        el.find(".dx-subj-edit").click(function() {
                            open_form('form', el.data("subject-id"), self.subjects_list_id, 0, 0, "", 0, "", {
                                after_close: function(frm)
                                {
                                    refreshAllData();
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
                        duration: "00:15",
                        className: "cafe",
                        color: "#d6df32",
                        start: "09:00",
                        dx_subj_id: 0,
                        dx_group_id: 0,
                        dx_day_id: 0,
                        dx_coffee_id: 0,
                        dx_is_published: 0
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
            
            var filterGroups = function() {
                var crit = self.root.find(".dx-search-group").val();
                var status = self.root.find(".dx-group-filter-btn").attr("data-status");
                
                if(!crit && status === "all")
                {
                    $("#dx-groups-box").find(".dx-event").show();
                    return;
                }
                
                $("#dx-groups-box").find(".dx-event").hide();
                
                var stat_class = (status === "all") ? '' : (".dx-status-" + status);
                
                $("#dx-groups-box").find(".dx-event" + stat_class + ":contains('" + crit + "')").show();
                
            };
            
            this.root.find(".dx-search-group").on("keyup", function()
            {
                filterGroups();
            });
            
            this.root.find(".dx-group-filter-btn a").click(function() {
                self.root.find(".dx-group-filter-btn button").find(".btn-title").text($(this).text());
                self.root.find(".dx-group-filter-btn").attr("data-status", $(this).data("status"));
                filterGroups();
            });
            
            var newGroupHtml = function(arr_data) {
                var new_el = $("<div>");
                new_el.addClass('dx-event').addClass('dx-group').addClass('dx-status-' + arr_data.status);
                new_el.attr("data-subject-id", arr_data.subj_id);
                new_el.attr("data-group-id", arr_data.group_id);
                
                var ch = $("<input type='checkbox'/>");
                ch.appendTo(new_el);
                ch.change(function() {
                    showHideChoiceBtn(this.checked);
                });
                
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
                
                var formData = new FormData();                
                formData.append("start_time", event.start.format("YYYY-MM-DD HH:mm"));
                formData.append("end_time", event.end.format("YYYY-MM-DD HH:mm"));
                formData.append("room_id", (event.resourceId) ? event.resourceId : self.room_id);
                
                var request = new FormAjaxRequest (self.options.scheduler_url + "new_coffee", '', '', formData);

                request.callback = function(data) {
                    event.id = 'C' + data.coffee_id;
                    event.dx_subj_id = data.subject_id;
                    event.dx_day_id = data.day_id;
                    event.dx_group_id = data.group_id;
                    event.dx_coffee_id = data.coffee_id;
                    
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

                    newGroupHtml({subj_id: event.dx_subj_id, group_id: data.group_id, text: event.title, status: 'prepare'});
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
                var formData = new FormData(); 
                formData.append("coffee_id", event.dx_coffee_id);
                formData.append("start_time", event.start.format("YYYY-MM-DD HH:mm"));
                formData.append("end_time", event.end.format("YYYY-MM-DD HH:mm"));
                formData.append("room_id", (event.resourceId) ? event.resourceId : self.room_id);
                
                var request = new FormAjaxRequest (self.options.scheduler_url + "update_coffee", '', '', formData);

                request.callback = function(data) {
                    event.id = 'C' + data.coffee_id;
                    event.dx_subj_id = data.subject_id;
                    event.dx_day_id = data.day_id;
                    event.dx_group_id = data.group_id;
                    event.dx_coffee_id = data.coffee_id;
                    
                    $('#calendar').fullCalendar( 'updateEvent', event );
                };
                
                request.err_callback = function() {                    
                    // rollback UI changes
                    refreshAllData();
                };

                request.doRequest();
            };
            
            var updateDayToDbConfirmed = function(event) {
                var formData = new FormData();
                formData.append("day_id", event.dx_day_id);
                formData.append("start_time", event.start.format("YYYY-MM-DD HH:mm"));
                formData.append("end_time", event.end.format("YYYY-MM-DD HH:mm"));
                formData.append("room_id", (event.resourceId) ? event.resourceId : self.room_id);
                
                var request = new FormAjaxRequest (self.options.scheduler_url + "update_day", '', '', formData);

                request.callback = function() {                    
                    if (parseInt(event.dx_is_published)) {
                        refreshAllData();
                    }
                };
                
                request.err_callback = function() {                    
                   // rollback UI changes
                   refreshAllData();
                };

                request.doRequest();
            };
            
            var updateDayToDb = function(event) {
                console.log("Update existing day in db!");
                
                if (parseInt(event.dx_is_published)) {                    
                    PageMain.showConfirm(updateDayToDbConfirmed, event, Lang.get('calendar.scheduler.move_confirm_title'), Lang.get('calendar.scheduler.move_confirm_msg'), null, null, refreshAllData);                
                }
                else {
                    updateDayToDbConfirmed(event);
                }
                
            };
            
            var refreshRoomsCbo = function(cbo_json) {
                self.cbo_rooms_refreshing = true;
                var cur_val = $(".dx-rooms-cbo").val();
                var cur_org = "";
                var htm = "";
                var cbo = $(".dx-rooms-cbo");
                        
                cbo.empty();
               
                $.each(JSON.parse(cbo_json), function() {    
                    if (cur_org != this.organization) {                    
                        if (cur_org != "") {
                            htm = htm + "</optgroup>";
                        }
                        htm = htm  + "<optgroup label='" +  this.organization + "'>";
                        cur_org = this.organization;
                    }
                    
                    htm = htm + "<option value='" + this.id + "'>" + this.title + "</option>";                  
                });
                
                if (cur_org != "") {
                    htm = htm + "</optgroup>";
                }
                $(htm).appendTo(cbo);
                cbo.val(cur_val);
                self.cbo_rooms_refreshing = false;
            }
            
            var refreshAllData = function() {
                show_page_splash(1);
                $.getJSON( self.options.root_url + self.options.scheduler_url + "json/" + self.room_id, function( data ) {
                    $("#external-events").empty();
                    $.each(JSON.parse(data.subjects), function() {                        
                        addSubjToDiv(this.id, this.title_full);                       
                    });
                    
                    $("#dx-groups-box").empty();
                    $.each(JSON.parse(data.groups), function() {                        
                        newGroupHtml({subj_id: this.subject_id, group_id: this.id, text: this.title, status: this.status});                    
                    });
                    filterGroups();
                    refreshRoomsCbo(data.rooms_cbo);
                    
                    $('#calendar').fullCalendar( 'removeEvents');
                    $('#calendar').fullCalendar( 'removeResources');
                    $('#calendar').fullCalendar( 'refetchResources' );
                    $('#calendar').fullCalendar( 'refetchEvents' );
                    
                    $(".dx-publish-choice").hide();
                    $(".dx-complect-choice").hide();
                });
            }
            
            var cal_tools = 'prev,next,today';
            var def_view = '';
            
            if (this.room_id) {
                cal_tools = cal_tools + ',month,agendaWeek,agendaDay';
                def_view = 'agendaWeek';
            }
            else {
                cal_tools = cal_tools + ',timelineDay,timelineThreeDays';
                def_view = 'timelineThreeDays';
            }
            
            var fullcal_params = {
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
                        snapDuration: "00:15",
                        navLinks: true, // can click day/week names to navigate views
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
                        eventConstraint:{
                            start: '09:00', 
                            end: '18:00', 
                        },
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
                            element.attr('data-coffee-id', event.dx_coffee_id);
                            
                            if (event.className == "closed") {
                                event.overlap = false;
                            }
                            
                        },
                        eventReceive : function(event) {                            
                            if (event.className == "cafe") {
                                newCafeToDb(event);
                                return;
                            }
                            
                            var view = $('#calendar').fullCalendar('getView');
                            
                            if (view.name == "month") {
                                var day_events = $('#calendar').fullCalendar( 'clientEvents' , function(ev) {
                                    
                                    if (ev.id && ev.start.isSame(event.start)) {
                                                                                
                                        event.start = event.start.add(2, 'hours');
                                        event.end = event.end.add(2, 'hours');
                                    }                                    
                                    
                                });
                                
                                if (event.end.hour() > 18) {
                                    $('#calendar').fullCalendar( 'removeEvents', function(ev) {                        
                                        if (!ev.id) {
                                            return true;
                                        }
                                        return false;
                                    });
                                    notify_err("Norādītajā datumā visi iespējamie laiki jau ir aizpildīti ar pasākumiem!");                                        
                                    return;
                                }
                                    
                                $('#calendar').fullCalendar( 'updateEvent', event );
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
                                open_form('form', calEvent.dx_coffee_id, self.coffee_list_id, 0, 0, "", 0, "", {
                                    after_close: function(frm)
                                    {
                                        refreshAllData();
                                    }
                                });
                                return;
                            }
                            
                            open_form('form', calEvent.dx_day_id, self.days_list_id, 0, 0, "", 0, "", {
                                after_close: function(frm)
                                {
                                    refreshAllData();
                                }
                            });
                        },
                        loading: function(isLoading, view) {
                            if (isLoading) {
                                show_page_splash(1);
                            }
                            else {
                                hide_page_splash(1);
                            }
                        },
			resourceGroupField: 'organization',              
			events: {
                            url: self.options.root_url + self.options.scheduler_url + "events_json/" + self.room_id,
                            type: 'GET'
                        }
            };
            
            if (!this.room_id) {
                fullcal_params["resources"] = {
                            url: self.options.root_url + self.options.scheduler_url + "rooms_json/" + self.room_id,
                            type: 'GET'
                        };
            }
            $('#calendar').fullCalendar(fullcal_params);  
           
            $.contextMenu({
                selector: '.context-menu-one', 
                callback: function(key, options) {
                    if (key == "subject") {
                        open_form('form', options.$trigger.data('subject-id'), self.subjects_list_id, 0, 0, "", 0, "", {after_close: function(frm) {
                            refreshAllData();
                        }});
                    }
                    
                    if (key == "group") {
                        open_form('form', options.$trigger.data('group-id'), self.groups_list_id, 0, 0, "", 0, "", {after_close: function(frm) {
                            refreshAllData();
                        }});
                    }
                    
                    if (key == "day") {
                        open_form('form', options.$trigger.data('day-id'), self.days_list_id, 0, 0, "", 0, "", {after_close: function(frm) {
                            refreshAllData();
                        }});
                    }
                    
                    if (key == "coffee") {
                        open_form('form', options.$trigger.data('coffee-id'), self.coffee_list_id, 0, 0, "", 0, "", {after_close: function(frm) {
                            refreshAllData();
                        }});
                    }
                    
                    if (key == "delete") {
                        
                    }
                },
                items: {
                    "subject": {name: "Pasākums", icon: "fa-graduation-cap"},
                    "group": {name: "Grupa", icon: "fa-users"},
                    "day": {name: "Nodarbība", icon: "fa-calendar-o"},
                    "sep0": "---------",
                    "coffee": {name: "Kafijas pauze", icon: "fa-coffee", disabled: function(key, opt) { 
                        // this references the trigger element
                        console.log("Calculate coffee menu: " + opt.$trigger.html());
                        return !parseInt(opt.$trigger.data('coffee-id')); 
                    }}/*,
                    "sep1": "---------",
                    "delete": {name: "Dzēst", icon: "fa-trash-o"}*/
                }
            });  
            
            $(window).on('beforeunload', function()
            {
                if($(".dx-stick-footer").hasClass('dx-page-in-edit-mode'))
                {
                    hide_page_splash(1);
                    return 'Your changes have not been saved.';
                }
            });
            
            this.root.find(".dx-mark-set-all").click(function() {
                $("#dx-groups-box .dx-group input").prop("checked", true);
                showHideChoiceBtn(true);
            });
            
            this.root.find(".dx-mark-remove-all").click(function() {
                $("#dx-groups-box .dx-group input").prop("checked", false);
                showHideChoiceBtn(false);
            });
            
            var fillPublishIDs = function(grps) {
                var ids = "";
                grps.each(function() {
                    if (ids != "") {
                        ids = ids + ",";
                    } 

                    ids = ids + $(this).attr('data-group-id');
                });
                
                return ids;
            };
            
            var setErrEventHandlers = function(frm) {
                frm.find(".btn-edit-err-group").click(function() {
                    open_form('form', $(this).data('group-id'), self.groups_list_id, 0, 0, "", 0, "");
                });
                
                frm.find(".dx-err-action").click(function() {
                    open_form('form', $(this).data('item-id'), $(this).data('list-id'), 0, 0, "", 0, "");
                });
                
                frm.find(".dx-solved-group").click(function() {
                    var gr = $(this).closest(".dx-group");
                    gr.addClass('bounceOutLeft');
                    setTimeout(function(){ 
                        gr.hide();
                    }, 500); 
                    
                    var er = frm.find(".dx-problem-lbl").find(".dx-err-count");
                    var cnt = parseInt(er.text()) - 1;
                    er.text(cnt);
                    if (cnt == 0) {
                        clearErrFormState(frm);
                    }
                });
            };
            
            var clearErrFormState = function(frm) {
                frm.find(".alert-error").hide();
                frm.find(".dx-publish-progress").hide();
                frm.find(".alert-info").hide();
                frm.find(".dx-check-publish-btn").show();
                frm.find(".dx-check-btn").show();
                frm.find(".dx-cancel-btn").show();
                frm.find(".dx-form-close-btn").show();
                frm.find(".ext-cont").hide();
                frm.find(".dx-problem-lbl").hide();
            };
            
            var publishOrCheck = function(is_publish) {
                var frm = $(".dx-publish-popup");
                frm.find(".dx-publish-progress").show();
                frm.find(".dx-check-publish-btn").hide();
                frm.find(".dx-check-btn").hide();
                frm.find(".dx-cancel-btn").hide();
                frm.find(".dx-form-close-btn").hide();
                frm.find(".alert-error").hide();
                frm.find(".ext-cont").hide();
                frm.find(".dx-problem-lbl").hide();
                frm.find(".alert-info").hide();
                
                var formData = new FormData();                
                formData.append("groups_ids", self.publish_ids);
                formData.append("is_publish", is_publish);
                formData.append("mode", self.popup_mode);
                
                var request = new FormAjaxRequest (self.options.scheduler_url + "publish", '', '', formData);
                
                request.callback = function(data) {
                    $(".dx-publish-popup").find(".dx-publish-progress").hide();
                    if (data.err_count == 0) {
                        var inf = frm.find(".alert-info");
                        
                        if (is_publish) {
                            inf.text(Lang.get('calendar.scheduler.' + self.popup_mode + '.msg_ok'));
                        }
                        else {
                            inf.text(Lang.get('calendar.scheduler.' + self.popup_mode + '.msg_check_ok'));
                            frm.find(".dx-check-publish-btn").show();
                            frm.find(".dx-check-btn").show();
                        }
                        
                        inf.show();
                        frm.find(".dx-cancel-btn").text("Aizvērt").show();
                        frm.find(".dx-cancel-btn").show();
                        frm.find(".dx-form-close-btn").show();
                        
                        if (is_publish) {
                            refreshAllData();
                        }
                    }
                    else {
                        frm.find(".dx-cancel-btn").show();
                        frm.find(".dx-check-publish-btn").show();
                        frm.find(".dx-check-btn").show();
                        frm.find(".alert-error").show();
                        frm.find(".dx-problem-lbl").show().find(".dx-err-count").text(data.err_count);                        
                        frm.find(".dx-cancel-btn").show();
                        frm.find(".dx-form-close-btn").show();
                        frm.find(".ext-cont").html(data.err_htm).show();
                        setErrEventHandlers(frm);
                    }
                };
                
                request.err_callback = function() {                    
                    frm.find(".dx-check-publish-btn").show();
                    frm.find(".dx-check-btn").show();
                    frm.find(".dx-publish-progress").hide();
                    frm.find(".dx-cancel-btn").show();
                    frm.find(".dx-form-close-btn").show();
                };
                
                request.doRequest();
            };
            
            $(".dx-publish-popup").find(".dx-check-publish-btn").click(function() {
                PageMain.showConfirm(publishOrCheck, 1, Lang.get('calendar.scheduler.' + self.popup_mode + '.confirm_title'), Lang.get('calendar.scheduler.' + self.popup_mode + '.confirm_msg'));                
            });
            
            $(".dx-publish-popup").find(".dx-check-btn").click(function() {
                publishOrCheck(0);
            });
            
            var showHideChoiceBtn = function(is_checked) {
                if(is_checked || $("#dx-groups-box .dx-group input:checked").length > 0) {
                    $(".dx-publish-choice").show();
                    $(".dx-complect-choice").show();
                }
                else {
                    $(".dx-publish-choice").hide();
                    $(".dx-complect-choice").hide();
                }
            };
            
            $("#dx-groups-box .dx-group input").change(function() {
                showHideChoiceBtn(this.checked);
            });
            
            var setLabelsPublishPopup = function(frm, btn) {
                frm.find(".dx-publish-intro1").text(Lang.get('calendar.scheduler.' + btn + '.intro1'));
                frm.find(".dx-publish-intro2").text(Lang.get('calendar.scheduler.' + btn + '.intro2'));
                frm.find(".dx-publish-lbl-count").text(Lang.get('calendar.scheduler.' + btn + '.lbl_count'));
                frm.find(".dx-publish-ok").text(Lang.get('calendar.scheduler.' + btn + '.msg_ok'));
                frm.find(".dx-publish-err").text(Lang.get('calendar.scheduler.' + btn + '.msg_err'));
                frm.find(".dx-publish-err").text(Lang.get('calendar.scheduler.' + btn + '.msg_err'));
                frm.find(".dx-check-publish-btn").text(Lang.get('calendar.scheduler.' + btn + '.btn_publish'));
                frm.find(".modal-title").html("<i class='" + Lang.get('calendar.scheduler.' + btn + '.icon_class') + "'></i> " + Lang.get('calendar.scheduler.' + btn + '.popup_title'));
                
            };
            
            var openPublishPopup = function(e, is_all, btn) {
                var grps = null;
                var status = self.root.find(".dx-group-filter-btn").attr("data-status");
                var stat_class = (status === "all") ? '' : (".dx-status-" + status);
                
                if (!is_all) {
                    grps = $("#dx-groups-box").find(".dx-group" + stat_class).filter(function() {
                        return ($(this).find("input:checked").length > 0);
                    });
                    self.publish_ids = fillPublishIDs(grps);
                }
                else {
                    self.publish_ids = "";
                }
            
                if (self.publish_ids == "") {
                    grps = $("#dx-groups-box").find(".dx-group" + stat_class);
                    self.publish_ids = fillPublishIDs(grps);
                }
                else { 
                    if(e) {
                        e.stopPropagation();
                        self.publish_ids = "";
                        // Toggle dropdown if not already visible:
                        $('.dx-' + btn + '-btn-group').find(".dx-" + btn + "-choice").dropdown('toggle');
                        return;
                    }
                }
                
                if (!grps.length) {
                    notify_err("Nav neviena grupa sagatavošanā, ko varētu publicēt.");
                    return;
                }
                
                var frm = $(".dx-publish-popup");
                frm.find('.dx-total-groups').text(grps.length);
                
                clearErrFormState(frm);
                setLabelsPublishPopup(frm, btn);
                self.popup_mode = btn;
                frm.modal('show');
            };
            
            this.root.find(".dx-publish-marked").click(function() {
                openPublishPopup(null, false, 'publish');
            });
            
            this.root.find(".dx-publish-all").click(function() {
                openPublishPopup(null, true, 'publish');
            });

            this.root.find(".dx-publish-default").click(function(e) {
                openPublishPopup(e, false, 'publish');
            });
            
            this.root.find(".dx-complect-marked").click(function() {
                openPublishPopup(null, false, 'complect');
            });
            
            this.root.find(".dx-complect-all").click(function() {
                openPublishPopup(null, true, 'complect');
            });

            this.root.find(".dx-complect-default").click(function(e) {
                openPublishPopup(e, false, 'complect');
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