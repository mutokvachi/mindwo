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
            scheduler_url: "calendar/scheduler/"
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
            this.group_list_id = this.root.data('group-list-id');
            this.gr_count = 0;
            
            var saveData = function(onsaved) {
                show_page_splash();
                var request = {
                        site_id: self.site_id,
                        items: self.nodes,
                        _method: 'put'
                };
                
                var save_url = self.options.root_url + self.options.scheduler_url + 'save';
                
                $.ajax({
                        type: 'post',
                        url: save_url,
                        dataType: 'json',
                        data: request,
                        success: function(data)
                        {                            
                            $(".dx-stick-footer").removeClass('dx-page-in-edit-mode');
                            
                            if (onsaved) {
                                onsaved.call(this);
                            }
                            else {
                                show_page_splash();
                                notify_info(Lang.get('constructor.menu.msg_saved'));                                
                            }
                        }
                });  
            };
            
            var newItemOpen = function() {
                open_form('form', 0, self.group_list_id, 0, 0, "", 1, "", {
                    after_close: function(frm)
                    {
                        var new_id = parseInt(frm.find("[name=item_id]").val());
                        if (new_id > 0 ) {
                            // add tp container
                            var n = $("<div>");
                            n.addClass('dx-event');
                            n.text(frm.find("[name=title]").val());
                            n.appendTo("#external-events");
                            addDr(n);
                        }
                    }
                });
            };
            
            var undo_changes = function() {
                show_page_splash();
                $(".dx-stick-footer").removeClass('dx-page-in-edit-mode');
                window.location.reload();
            };
            
            this.root.find(".dx-undo-btn").click(function() {
                PageMain.showConfirm(undo_changes, null, Lang.get('constructor.menu.confirm_title'), Lang.get('constructor.menu.confirm_msg'), Lang.get('constructor.menu.confirm_yes'), Lang.get('constructor.menu.confirm_no'), null);
            });
            
            this.root.find(".dx-new-btn").click(function() {                
                newItemOpen();
            });
            
            this.root.find(".dx-save-btn").click(function() {                
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
            
            var initDrag = function(el) {
                    // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
                    // it doesn't need to have a start or end
                    var eventObject = {
                            title: $.trim(el.text()), // use the element's text as the event title
                            stick: true
                    };
                    // store the Event Object in the DOM element so we can get to it later
                    el.data('eventObject', eventObject);
                    // make the event draggable using jQuery UI
                    el.draggable({
                            zIndex: 999,
                            revert: true, // will cause the event to go back to its
                            revertDuration: 0 //  original position after the drag
                    });
            };
            
            var addDr = function(el, gr) {
                // store data so the calendar knows to render an event upon drop
                    el.data('event', {
                            title: $.trim(el.text()), // use the element's text as the event title
                            stick: true, // maintain when user navigates (see docs on the renderEvent method)
                            className: (gr) ? gr : ''
                    });

                    // make the event draggable using jQuery UI
                    el.draggable({
                            start: function() {
                                el.css('max-width', '200px');
                            },
                            stop: function() {
                                el.css('max-width', 'none');
                            },
                            zIndex: 999,
                            revert: true,      // will cause the event to go back to its
                            revertDuration: 0  //  original position after the drag
                    });
            };
            
            var addDrCafe = function(el) {
                
                // store data so the calendar knows to render an event upon drop
                    el.data('event', {
                            title: 'Kafijas pauze', // use the element's text as the event title
                            stick: true, // maintain when user navigates (see docs on the renderEvent method),
                            duration: "00:30",
                            className: "cafe"
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


            var addEvent = function(title) {
                    title = title.length === 0 ? "Untitled Event" : title;
                    var html = $('<div class="external-event label label-default">' + title + '</div>');
                    jQuery('#event_box').append(html);
                    initDrag(html);
            };

            $('#external-events div.external-event').each(function() {
                    initDrag($(this));
            });

            $('#event_add').unbind('click').click(function() {
                    var title = $('#event_title').val();
                    addEvent(title);
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

            //predefined events
            /*
            $('#event_box').html("");
            addEvent("K-A Korupcijas novēršana");
            addEvent("K-A Korupcijas sekas");
            addEvent("K-A Budžetu droša plānošana");
            addEvent("U-B Biznesa vadība");
            addEvent("U-C Efektivitāte");
            addEvent("U-C Dienas plānošana");
            */
            $('#calendar').fullCalendar({
                        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
			now: '2017-05-17',
                        weekends: false,
			editable: true,
                        droppable: true,
			aspectRatio: 1.8,
			scrollTime: '00:00',
                        displayEventTime: false,
			header: {
				left: 'title',
                                center: '',
                                right: 'prev,next,today,timelineDay,timelineThreeDays,month,agendaWeek,agendaDay,listMonth'
			},
                        locale: Lang.getLocale(),
			defaultView: 'timelineDay',
			views: {
				timelineThreeDays: {
					type: 'timeline',
					duration: { days: 3 }
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
                        drop: function(date, allDay) { // this function is called when something is dropped
                                /*
                                // retrieve the dropped element's stored Event Object
                                var originalEventObject = $(this).data('eventObject');
                                // we need to copy it, so that multiple events don't have a reference to the same object
                                var copiedEventObject = $.extend({}, originalEventObject);

                                // assign it the date that was reported
                                copiedEventObject.start = date;
                                copiedEventObject.allDay = allDay;
                                copiedEventObject.className = $(this).attr("data-class");

                                // render the event on the calendar
                                // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
                                $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
                                */
                                // remove the element from the "Draggable Events" list
                                //$(this).remove();                                
                                if (!$(this).hasClass('dx-group') && !$(this).hasClass('dx-cafe')) {                                    
                                    self.gr_count++;
                                    var newEl = $(this).clone().addClass('dx-group').css('max-width', 'none').appendTo( "#dx-groups-box" );
                                    newEl.text("G" + self.gr_count + ": " + newEl.text());
                                    addDr(newEl, "group");
                                }
                        },
                        eventReceive : function(event) {
                            if (event.className=="group" || event.className=="cafe") {
                                return;
                            }
                            
                            event.title = "G" + self.gr_count + ": " + event.title;
                            event.className="group";
                            $('#calendar').fullCalendar( 'updateEvent', event );
                        },
			resourceGroupField: 'building',
			resources: [
				{ id: 'a', building: 'Valsts administrācijas skola', title: 'Telpa 342' },
				{ id: 'b', building: 'Valsts administrācijas skola', title: 'Telpa 356' },
				{ id: 'c', building: 'Finanšu ministrija', title: 'Telpa 133'}				
			],
			events: [
				{ id: '1', resourceId: 'b', start: '2017-05-15T09:00:00', end: '2017-05-15T11:00:00', title: 'K-A Korupcijas novēršana' },
				{ id: '2', resourceId: 'c', start: '2017-05-16T15:00:00', end: '2017-05-16T18:00:00', title: 'K-A Pareiza pieeja plānošanā' },
				{ id: '3', resourceId: 'a', start: '2017-05-17T14:00:00', end: '2017-05-17T15:00:00', title: 'K-A Korupcijas novēršana' },
				{ id: '4', resourceId: 'b', start: '2017-05-17T16:30:00', end: '2017-05-17T17:30:00', title: 'K-A Efektīva vadība' }
			]
		});  
           
            
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