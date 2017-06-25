(function($)
{
	/**
	 * MenuBuilder - a jQuery plugin that renders menu drag & drop building UI
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.MenuBuilder = function(opts)
	{
		var options = $.extend({}, $.fn.MenuBuilder.defaults, opts);
		return this.each(function()
		{
			new $.MenuBuilder(this, options);
		});
	};
	
	$.fn.MenuBuilder.defaults = {
            root_url: getBaseUrl(),
            builder_url: "constructor/menu/",
            menu_list_id: 5
	};
	
	/**
	 * MenuBuilder constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.MenuBuilder = function(root, opts)
	{
            $.data(root, 'MenuBuilder', this);
            var self = this;
            this.options = opts;
            this.root = $(root);
            this.nodes = null;
            this.root_url = getBaseUrl();
            this.site_id = self.root.find(".dx-sites-cbo").val();
            this.itm_to_edit = null;
            
            var saveData = function(onsaved) {
                show_page_splash();
                var request = {
                        site_id: self.site_id,
                        items: self.nodes,
                        _method: 'put'
                };
                
                var save_url = self.options.root_url + self.options.builder_url + self.site_id;
                
                $.ajax({
                        type: 'post',
                        url: save_url,
                        dataType: 'json',
                        data: request,
                        success: function(data)
                        {
                            self.nodes = null
                            $(".dx-stick-footer").removeClass('dx-page-in-edit-mode');
                            
                            if (onsaved) {
                                onsaved.call(this);
                            }
                            else {
                                show_page_splash();
                                notify_info(Lang.get('constructor.menu.msg_saved'));
                                setTimeout(function(){ window.location.reload(true); }, 1000);
                            }
                        }
                });  
            };
            
            var validateChilds = function() {
                if ($(".dx-menu-builder").find(".parentError").length !== 0) {
                    notify_err(Lang.get('constructor.menu.err_parent'));
                    $('html, body').animate({
                        scrollTop: $(".dx-menu-builder").find(".parentError:last").offset().top - 66
                    }, 2000);
                    return false;
                }
                
                return true;
            };
            
            var editItemOpen = function () {
                var itm = self.itm_to_edit;
                show_page_splash();
                open_form('form', itm.attr("data-id"), self.options.menu_list_id, 0, 0, "", 0, "", {
                    after_close: function(frm)
                    {
                        var new_parent_id = frm.find("[name=parent_id]").val();
                        var new_order = frm.find("[name=order_index]").val();
                        
                        if (new_parent_id !== itm.attr("data-parent-id") || new_order !== itm.attr("data-order-index")) {
                            show_page_splash();
                            window.location.reload();
                            return;
                        }
                        
                        var new_title = frm.find("[name=title]").val();
                        var new_icon = frm.find("[name=fa_icon]").val();
                        
                        if (itm.attr("data-title") !== new_title) {
                            itm.attr("data-title", new_title);                            
                            itm.find(".dx-title:first").text(new_title);
                        }
                        
                        if (itm.attr("data-icon") !== new_icon) {
                            itm.find(".dx-icon:first").removeClass().addClass(new_icon).addClass("dx-icon");
                            itm.attr("data-icon", new_icon);
                        } 
                        
                        var new_color = frm.find("[name=color]").val();
                        if (itm.attr("data-color") !== new_color) {
                            itm.find(".dx-icon:first").css("color", new_color);
                            itm.find(".dx-title:first").css("color", new_color);
                            itm.attr("data-color", new_color);
                        }
                        self.itm_to_edit = null;
                    }
                });
            }
            
            var newItemOpen = function() {
                open_form('form', 0, self.options.menu_list_id, 0, 0, "", 1, "", {
                    after_close: function(frm)
                    {
                        var new_id = parseInt(frm.find("[name=item_id]").val());
                       if (new_id > 0 ) {
                            show_page_splash();
                            window.location.reload();
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
                if (self.nodes) {
                    if (!validateChilds()) {                        
                        return false;
                    }
                
                    saveData(newItemOpen);
                    return;
                }
                
                newItemOpen();
            });
            
            this.root.find(".dx-edit-menu-link").click(function() {
                self.itm_to_edit = $(this).closest(".dd-item");
                
                if (self.nodes) {
                    if (!validateChilds()) {
                        self.itm_to_edit = null;
                        return false;
                    }
                
                    saveData(editItemOpen);
                    return;
                }
                
                editItemOpen();
            });
            
            this.root.find(".dx-save-btn").click(function() {
                if (!self.nodes) {
                    notify_err(Lang.get('constructor.menu.err_no_changes'));
                    return false;
                }
                
                if (!validateChilds()) {
                    return false;
                }
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
            
            this.root.find('.dd').nestable({
                group: 1,
                maxDepth: 5,
                
            }).on('change', updateOutput).nestable('collapseAll');
            
            var loadSiteMenu = function() {
                var sel = self.root.find(".dx-sites-cbo");
                if (sel.val() == self.site_id) {
                    return false;
                }
                
                if (self.nodes) {
                    if (!validateChilds()) {
                        sel.val(self.site_id);
                        return false;
                    }
                
                    saveData(loadSiteMenu);
                }
                
                var url = self.options.root_url + self.options.builder_url + self.root.find(".dx-sites-cbo").val();
                show_page_splash(1);
                window.location.assign(encodeURI(url));
            };
            
            $(window).on('beforeunload', function()
            {
                if($(".dx-stick-footer").hasClass('dx-page-in-edit-mode'))
                {
                    hide_page_splash(1);
                    return 'Your changes have not been saved.';
                }
            });
            
            this.root.find(".dx-sites-cbo").change(loadSiteMenu);
            
            // adjust menu for vertical menu UI
            if (!$("body").hasClass("dx-horizontal-menu-ui")) {
                
                var adjust_menu = function() {
                    $(".page-sidebar-menu").css("padding-bottom", '80px');
                };
                PageMain.addResizeCallback(adjust_menu);
                
                adjust_menu();
                
                $(".dx-menu-builder-stick-title").css("font-size", "14px");
                
                var dv = $(".dx-menu-sites").find("div.col-sm-10");
                dv.css("margin-right", "-35px");
                dv.css("padding-left", "30px");
            }
	};
})(jQuery);