(function($)
{
	/**
	 * DxComplect - a jQuery plugin that renders students groups complecting UI
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.DxComplect = function(opts)
	{
		var options = $.extend({}, $.fn.DxComplect.defaults, opts);
		return this.each(function()
		{
			new $.DxComplect(this, options);
		});
	};
	
	$.fn.DxComplect.defaults = {
            root_url: getBaseUrl(),
            complect_url: "calendar/complect/",
            group_prefix: "G"
	};
	
	/**
	 * DxComplect constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.DxComplect = function(root, opts)
	{
            $.data(root, 'DxComplect', this);
            var self = this;
            this.options = opts;
            this.root = $(root);
                        
            this.org_id = this.root.data("org-id");
            this.current_date = this.root.data("current-date"); 
            
            // Groups events handlers

            var openGroupInfo = function(org_id, group_id) {
                show_page_splash(1);
                $.getJSON( self.options.root_url + self.options.complect_url + "group/" + org_id + "/" + group_id, function( data ) {
                    var frm = $(".dx-group-popup");
                    frm.find('.modal-body').html(data.htm);

                    frm.find('.dx-group-info-cont').slimScroll({
                        height: '135px'
                    });

                    frm.find(".dx-group-info").DxGroupInfo();
                    hide_page_splash(1);
                    frm.modal('show');

                    frm.on('hidden.bs.modal', function () {
                        frm.off('hidden.bs.modal');			
                        refreshAllData();
                    });
                });
            };

            var setGroupsInfoHandlers = function() {                
                self.root.find(".dx-group-edit").click(function() {
                    openGroupInfo($(this).data('org-id'),  $(this).data('group-id'));                    
                });                
            };
            
            setGroupsInfoHandlers();
            // End groups events handlers

            // Groups filtering
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
            // End groups filtering

            // Data reloading
            this.root.find(".dx-orgs-cbo").change(function (event) {
                                
                event.preventDefault();

                show_page_splash(1);
                var url = self.options.root_url + self.options.complect_url + self.root.find('.dx-orgs-cbo option:selected').val();
                window.location.assign(encodeURI(url));
            });

            var refreshAllData = function() {
                show_page_splash(1);
                $.getJSON( self.options.root_url + self.options.complect_url + "groups_json/" + self.org_id, function( data ) {                    
                    
                    $("#dx-groups-box").empty();
                    $("#dx-groups-box").html(data.htm);
                    setGroupsInfoHandlers();
                    filterGroups();
                    
                    $('#calendar').fullCalendar( 'removeEvents');
                    $('#calendar').fullCalendar( 'refetchEvents' );
                });
            }
            // End data reloading

            // Calendar UI
            var cal_tools = 'prev,next,today,month,agendaWeek,agendaDay';
            var def_view = 'month';
                        
            var fullcal_params = {
                        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
                        now: self.current_date,
                        weekends: false,
                        editable: false,
                        droppable: false,
                        aspectRatio: 1.8,
                        scrollTime: '00:00',
                                    displayEventTime: false,
                                    allDaySlot: false,
                                    resourceLabelText: "Telpas",
                                    navLinks: true, // can click day/week names to navigate views
                        header: {
                            left: 'title',
                            center: '',
                            right: cal_tools
                        },
                        locale: Lang.getLocale(),
                        defaultView: def_view,
			
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
                        eventClick: function(calEvent, jsEvent, view) {
                            var orgs = calEvent.orgs.split(',');
                            openGroupInfo(orgs[0], calEvent.dx_group_id);                            
                        },
                        loading: function(isLoading, view) {
                            if (isLoading) {
                                show_page_splash(1);
                            }
                            else {
                                hide_page_splash(1);
                            }
                        },			         
			            events: {
                            url: self.options.root_url + self.options.complect_url + "events_json/" + self.org_id,
                            type: 'GET'
                        }
            };
            
            $('#calendar').fullCalendar(fullcal_params);
            // End Calendar UI
	};
})(jQuery);