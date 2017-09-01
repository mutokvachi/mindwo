(function($)
{
	/**
	 * DxGroupInfo - a jQuery plugin that renders students group information form UI
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.DxGroupInfo = function(opts)
	{
		var options = $.extend({}, $.fn.DxGroupInfo.defaults, opts);
		return this.each(function()
		{
			new $.DxGroupInfo(this, options);
		});
	};
	
	$.fn.DxGroupInfo.defaults = {
            root_url: getBaseUrl(),
            complect_url: "calendar/complect/",
            group_prefix: "G"
	};
	
	/**
	 * DxGroupInfo constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.DxGroupInfo = function(root, opts)
	{
            $.data(root, 'DxGroupInfo', this);
            var self = this;
            this.options = opts;
            this.root = $(root);
                        
            this.org_id = this.root.data("org-id");
            this.group_id = this.root.data("group-id"); 
            this.is_ajax = parseInt(this.root.data("is-ajax"));
            this.ajax_timer_id = 0;

            // Employees filtering
            var filterEmpl = function(obj) {
                var crit = self.root.find(".dx-search-data[data-obj=" + obj + "]").val();
                                
                if(!crit)
                {
                    $("#dx-" + obj + "-box").find(".dx-empl-info").show();
                    refreshCount(obj);
                    return;
                }
                
                $("#dx-" + obj + "-box").find(".dx-empl-info").hide();
                
                $("#dx-" + obj + "-box").find(".dx-empl-info:contains('" + crit + "')").show();
                
                refreshCount(obj);
            };

            var filterEmplAjax = function() {
                var crit = self.root.find(".dx-search-data[data-obj=avail]").val();
                /*
                if (crit.length > 0 && crit.length < 3) {
                    return; // do not search small phrases
                }
                */
                self.root.find(".dx-empl-count-avail").html("Notiek meklēšana...");
                $.getJSON( self.options.root_url + self.options.complect_url + "search_empl_json/" + self.org_id + "/" + self.group_id + "?criteria=" + escape(crit), function( data ) {
                    refillEmpl("avail", data.htm); 
                    
                    refreshCount("avail");
                    self.ajax_timer_id = 0;
                });

            };

            var refreshCount = function(obj) {
                var visible_cnt = $("#dx-" + obj +"-box .dx-empl-info:visible").length;
                var total_cnt = parseInt(self.root.attr('data-total-' + obj));

                var txt = Lang.get('calendar.complect.lbl_' + obj + '_cnt');
                if (total_cnt == visible_cnt) {
                    txt = txt + ": " + visible_cnt;
                }
                else {
                    txt = txt + " " + visible_cnt + " " + Lang.get('calendar.complect.lbl_cnt_from') + " " + total_cnt;
                }
                self.root.find(".dx-empl-count-" + obj).text(txt);
            };
            
            this.root.find(".dx-search-data").on("keyup", function()
            {
                var obj = $(this).data('obj');

                if (!self.is_ajax || obj === "members") {
                    filterEmpl(obj);
                }
                else {
                    if (self.ajax_timer_id > 0) {
                        clearTimeout(self.ajax_timer_id);
                    }
                    self.ajax_timer_id = setTimeout(filterEmplAjax, 500);
                }
            });            
            // End employees filtering

            // Move/remove from group
            var validateMembersCount = function() {
                var total_cnt = parseInt(self.root.attr('data-total-members'));
                var limit = parseInt(self.root.attr('data-places-quota'));

                return (total_cnt < limit);
            };

            var addToGroup = function() {

                if (!validateMembersCount()) {
                    notify_err('Grupai nevar pievienot dalībnieku, jo ir sasniegts dalībnieku limits!');
                    return;
                }

                var empl_el = $(this).closest(".dx-empl-info");
                var formData = new FormData();                
                formData.append("org_id", self.org_id);
                formData.append("group_id", self.group_id);
                formData.append("empl_id", empl_el.data("empl-id"));
                
                var request = new FormAjaxRequest (self.options.complect_url + "add_member", '', '', formData);

                request.callback = function(data) {
                    var new_el = empl_el.clone(true, true);
                    new_el.addClass("dx-is-member").removeClass("dx-is-avail");
                    empl_el.addClass("dx-is-member").removeClass("dx-is-avail");

                    new_el.prependTo($("#dx-members-box"));
                    self.root.attr('data-total-members', parseInt(self.root.attr('data-total-members')) + 1);

                    var btn_rem = new_el.find(".dx-empl-remove");
                    if (btn_rem.hasClass('dx-dont-tooltipster')) {
                        btn_rem.tooltipster({
                            theme: 'tooltipster-light',
                            animation: 'grow'
                        });
                        btn_rem.removeClass('dx-dont-tooltipster');
                    }
                    refreshCount("members");
                };
                
                request.doRequest();
            };

            this.root.find(".dx-empl-add").click(addToGroup);

            var removeFromGroup = function() {
                var empl_el = $(this).closest(".dx-empl-info");
                var formData = new FormData();                
                formData.append("org_id", self.org_id);
                formData.append("group_id", self.group_id);
                formData.append("empl_id", empl_el.data("empl-id"));
                
                var request = new FormAjaxRequest (self.options.complect_url + "remove_member", '', '', formData);

                request.callback = function(data) {
                    $("#dx-avail-box").find('.dx-empl-info[data-empl-id=' + empl_el.data('empl-id') + ']').removeClass('dx-is-member');

                    empl_el.remove();
                    self.root.attr('data-total-members', parseInt(self.root.attr('data-total-members')) - 1);

                    refreshCount("members");
                };
                
                request.doRequest();
            };

            this.root.find(".dx-empl-remove").click(removeFromGroup);           
            // End move/remove from group

            // Employees profiles
            var new_empl_btn = this.root.closest(".modal-dialog").find('.dx-new-empl-btn');
            if (parseInt(new_empl_btn.attr('data-is-init')) == 0) {
                new_empl_btn.click(function() {
                    open_form('form', 0, self.root.data('empl-list-id'), 0, 0, "", 1, "", {
                        after_close: function(frm)
                        {
                            var new_id = parseInt(frm.find("[name=item_id]").val());
                            if (new_id) {
                                refreshEmpl();
                            }
                        }
                    });
                });
                new_empl_btn.attr('data-is-init', 1);
            }

            var openEmplForm = function() {
                var el_empl = $(this).closest(".dx-empl-info");
                var empl_id = el_empl.data("empl-id");

                open_form('form', empl_id, self.root.data('empl-list-id'), 0, 0, "", 0, "", {
                    after_close: function(frm)
                    {
                        refreshEmpl();
                    }
                });
            };
            
            this.root.find(".dx-empl-name").click(openEmplForm);
            // End employees profiles

            // Data reloading 
            var refillEmpl = function(obj, htm) {
                var el_box = $("#dx-" + obj + "-box");
                el_box.empty();
                el_box.html(htm);                
                el_box.find(".dx-empl-name").click(openEmplForm);
                el_box.find(".dx-empl-add").click(addToGroup);
                el_box.find(".dx-empl-remove").click(removeFromGroup);
            }
            
            var refreshEmpl = function() {
                show_page_splash(1);
                var crit = self.root.find(".dx-search-data[data-obj=avail]").val();
                $.getJSON( self.options.root_url + self.options.complect_url + "empl_json/" + self.org_id + "/" + self.group_id + "/" + self.is_ajax + "?criteria=" + escape(crit), function( data ) {
                    refillEmpl("avail", data.htm_avail);
                    refillEmpl("members", data.htm_members);

                    if (!self.is_ajax) {
                        filterEmpl("avail");
                    }
                    else {
                        refreshCount("avail");
                    }

                    filterEmpl("members");
                    
                    hide_page_splash(1);
                });
            }
            // End data reloading
	};
})(jQuery);