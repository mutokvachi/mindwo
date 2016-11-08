(function($)
{
	/**
	 * FreeForm - a jQuery plugin for working with arbitrary forms
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.FreeForm = function(opts)
	{
		var options = $.extend({}, $.fn.FreeForm.defaults, opts);
		
		return this.each(function()
		{
			new $.FreeForm(this, options);
		});
	};
	
	$.fn.FreeForm.defaults = {
		
	};
	
	/**
	 * FreeForm constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.FreeForm = function(root, opts)
	{
		// store object instance along with a root DOM element
		$.data(root, 'FreeForm', this);
		
		var self = this;
		this.root = $(root);
		this.options = opts;
		this.fields = $('[data-name]', this.root);
		this.originalData = {};
		this.editButton = $('.dx-edit-general', this.root);
		this.saveButton = $('.dx-save-general', this.root);
		this.cancelButton = $('.dx-cancel-general', this.root);
		
		// Bind callbacks to buttons
		this.editButton.click(function() { self.edit(); });
		this.saveButton.click(function() { self.save(); });
		this.cancelButton.click(function() { self.cancel(); });
	};
	
	/**
	 * FreeForm methods
	 */
	$.extend($.FreeForm.prototype, {
		/**
		 * Replace HTML with form input fields
		 */
		edit: function()
		{
			var self = this;
			
			// a structure for JSON request
			var request = {
				model: this.root.data('model'),
				item_id: this.root.data('item_id'),
				list_id: this.root.data('list_id'),
				fields: []
			};
			
			// collect names of input fields marked with data-name attribute
			this.fields.each(function()
			{
				self.originalData[$(this).data('name')] = $(this).html();
				request.fields.push({
					name: $(this).data('name')
				});
			});
			
			show_page_splash(1);
			
			// perform a request to the server
			$.ajax({
				type: 'POST',
				url: DX_CORE.site_url + 'freeform/' + request.item_id + '/edit',
				dataType: 'json',
				data: request,
				success: function(data)
				{
					if(typeof data.success != "undefined" && data.success == 0)
					{
						notify_err(data.error);
						hide_page_splash(1);
						return;
					}
					
					self.editButton.hide();
					self.saveButton.show();
					self.cancelButton.show();
					
					// replace original html content of marked elements with input fields
					for(var i = 0; i < data.fields.length; i++)
					{
						var name = data.fields[i].name;
						var input = data.fields[i].input;
						var elem = $('[data-name="' + name + '"]', self.root);
						if(elem.length)
							elem.html(input);
					}
					hide_page_splash(1);                                        
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(textStatus);
					console.log(jqXHR);
					hide_page_splash(1);
				}
			});
		},
		
		/**
		 * Submit input field values to the server
		 */
		save: function()
		{
			var self = this;
			
			// JSON structure
			var request = {
				model: this.root.data('model'),
				item_id: this.root.data('item_id'),
				list_id: this.root.data('list_id'),
				fields: []
			};
			
			// collect values of input fields
			this.fields.each(function()
			{
				request.fields.push({
					name: $(this).data('name'),
					data: $(this).find('[name]').val()
				});
			});
			
			show_page_splash(1);
			
			// submit a request
			$.ajax({
				type: 'POST',
				url: DX_CORE.site_url + 'freeform/' + request.item_id + '?_method=PUT',
				dataType: 'json',
				data: request,
				success: function(data)
				{
					if(typeof data.success != "undefined" && data.success == 0)
					{
						notify_err(data.error);
						hide_page_splash(1);
						return;
					}
					
					self.editButton.show();
					self.saveButton.hide();
					self.cancelButton.hide();
					
					// replace input fields with html data from server response
					for(var i = 0; i < data.fields.length; i++)
					{
						var name = data.fields[i].name;
						var html = data.fields[i].html;
						var elem = $('[data-name="' + name + '"]', self.root);
						if(elem.length)
							elem.html(html);
					}
					hide_page_splash(1);
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(textStatus);
					console.log(jqXHR);
					hide_page_splash(1);
				}
			});
		},
		
		/**
		 * Remove input fields and display original HTML
		 */
		cancel: function()
		{
			var self = this;
			this.editButton.show();
			this.saveButton.hide();
			this.cancelButton.hide();
			this.fields.each(function()
			{
				$(this).html(self.originalData[$(this).data('name')]);
			});
		}
	});
})(jQuery);
/**
 * Author:  Eugene Ostapenko <evo@olympsoft.com>
 * License: MIT
 * Created: 04.11.16, 19:33
 */
(function ($)
{
	/**
	 * InlineForm - a jQuery plugin that provides a way to work with AJAX form embedded into a page
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.InlineForm = function(opts)
	{
		var options = $.extend({}, $.fn.InlineForm.defaults, opts);
		
		return this.each(function()
		{
			new $.InlineForm(this, options);
		});
	};
	
	$.fn.InlineForm.defaults = {
		beforeSave: null,
		afterSave: null,
		empl_search_page_url: "/search"
	};
	
	/**
	 * InlineForm constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.InlineForm = function(root, opts)
	{
		$.data(root, 'InlineForm', this);
		var self = this;
		this.options = opts;
		this.root = $(root);
		this.tabs = $('.tab-pane', this.root);
		this.originalTabs = {};
		this.editButton = $('.dx-edit-profile', this.root);
		this.saveButton = $('.dx-save-profile', this.root);
		this.cancelButton = $('.dx-cancel-profile', this.root);
		this.deleteButton = $('.dx-delete-profile', this.root);
		this.requests;
                this.onRequestSuccess;
                this.onRequestFailed;
        
		// Bind callbacks to buttons
		this.editButton.click(function()
		{
			self.edit();
		});
		this.saveButton.click(function()
		{
			self.save();
		});
		this.cancelButton.click(function()
		{
			self.cancel();
		});
		this.deleteButton.click(function()
		{
			self.destroy();
		});
	};
	
	/**
	 * InlineForm methods
	 */
	$.extend($.InlineForm.prototype, {
                /**
                 * Resets and initializes all async request processing parameters
                 * @param {integer} total Total count of processes which will be processed asynchronously
                 */
                initRequest: function (total) {
                    this.requests = {
                        total: total,
                        succeeded: 0,
                        failed: 0
                    };

                    this.onRequestSuccess = [];
                    this.onRequestFailed = [];
                },
                /**
                 * Saves completed request status. If all request are finished, then execute success commands
                 * @param {boolean} is_success Parmeter if process wass successful
                 */
                setRequestStatus: function (is_success) {
                    if (is_success) {
                        this.requests.succeeded++;
                    } else {
                        this.requests.failed++;
                    }

                    if (this.requests.total === (this.requests.succeeded + this.requests.failed)) {
                        if (this.requests.failed === 0) {
                            for (var i = 0; i < this.onRequestSuccess.length; i++) {
                                this.onRequestSuccess[i].func(this.onRequestSuccess[i].args);
                            }
                        } else {
                            for (var i = 0; i < this.onRequestFailed.length; i++) {
                                this.onRequestFailed[i].func(this.onRequestFailed[i].args);
                            }
                        }

                        hide_page_splash(1);
                    }
                },
		/**
		 * Replace HTML with form input fields
		 */
		edit: function()
		{
			var self = this;
			
			// a structure for JSON request
			var request = {
				list_id: this.root.data('list_id'),
				tab_list: []
			};
			
			this.tabs.each(function()
			{
				self.originalTabs[$(this).data('tabTitle')] = $(this).html();
			});
			
			show_page_splash(1);
			
			// perform a request to the server
			$.ajax({
				type: 'POST',
				url: DX_CORE.site_url + 'inlineform/' + this.root.data('item_id') + '/edit',
				dataType: 'json',
				data: request,
				success: function(data)
				{
					if(typeof data.success != "undefined" && data.success == 0)
					{
						notify_err(data.error);
						hide_page_splash(1);
						return;
					}
					
					self.editButton.hide();
					
					var tabs = $($.parseHTML('<div>' + data.tabs + '</div>')).find('.tab-pane');
					
					// replace original html content of marked elements with input fields
					for(var i = 0; i < tabs.length; i++)
					{
						var tab = $(tabs[i]);
						var elem = $('[data-tab-title="' + tab.data('tabTitle') + '"]', self.root);
						if(elem.length)
							elem.html(tab.html());
					}
					
                                        window.DxEmpPersDocs.toggleDisable(false);
                                        
					hide_page_splash(1);
					
					$('.dx-stick-footer').show();
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(textStatus);
					console.log(jqXHR);
					hide_page_splash(1);
				}
			});
		},
		
		/**
		 * Submit input field values to the server
		 */
		save: function()
		{
			var self = this;
			var formData = process_data_fields(this.root.attr('id'));
			formData.append('item_id', this.root.data('item_id'));
			formData.append('list_id', this.root.data('list_id'));
			formData.append('edit_form_id', this.root.data('form_id'));
			formData.append('redirect_url', this.root.data('redirect_url'));
			
			var url = DX_CORE.site_url + 'inlineform';
			if(this.root.data('mode') != 'create')
			{
				url += '/' + this.root.data('item_id') + '?_method=PUT';
			}
			
			show_page_splash(1);
			
			// submit a request
			$.ajax({
				type: 'POST',
				url: url,
				dataType: 'json',
				processData: false,
				contentType: false,
				data: formData,
				success: function(data)
				{
                                    if(typeof data.success != "undefined" && data.success == 0)
                                    {
                                            notify_err(data.error);
                                            hide_page_splash(1);
                                            return;
                                    }

                                    if(self.root.data('mode') == 'create')
                                    {
                                        window.DxEmpPersDocs.userId = data.item_id;

                                        // Custom tab
                                        window.DxEmpPersDocs.onClickSaveDocs(function () {
                                            window.DxEmpPersDocs.toggleDisable(true);

                                            hide_page_splash(1);
                                            $('.dx-stick-footer').hide();
                                            window.location = data.redirect;
                                        });
                                    
                                        
                                        return;
                                    }

                                    // Custom tab
                                    window.DxEmpPersDocs.onClickSaveDocs(function () {
                                        window.DxEmpPersDocs.toggleDisable(true);
                                        
                                        self.editButton.show();

                                        var tabs = $($.parseHTML('<div>' + data.tabs + '</div>')).find('.tab-pane');

                                        // replace original html content of marked elements with input fields
                                        for(var i = 0; i < tabs.length; i++)
                                        {
                                                var tab = $(tabs[i]);
                                                var elem = $('[data-tab-title="' + tab.data('tabTitle') + '"]', self.root);
                                                if(elem.length)
                                                        elem.html(tab.html());
                                        }

                                        if(self.options.afterSave)
                                        {
                                                self.options.afterSave();
                                        }
                                    
                                        hide_page_splash(1);
                                        $('.dx-stick-footer').hide();

                                    });
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(jqXHR);
                                        console.log(errorThrown);
				}
			});
		},
		
		/**
		 * Remove input fields and display original HTML
		 */
		cancel: function()
		{
			if(this.root.data('mode') == 'create')
			{
				show_page_splash(1);
				window.location = this.options.empl_search_page_url;
				return;
			}
			
			this.editButton.show();
			
			for(var k in this.originalTabs)
			{
				this.tabs.filter('[data-tab-title="' + k + '"]').html(this.originalTabs[k]);
			}
			$('.dx-stick-footer').hide();
                        window.DxEmpPersDocs.cancelEditMode();
		},
		
		destroy: function()
		{
			if(!confirm(Lang.get('frame.confirm_delete')))
				return;
			
			var request = {
				edit_form_id: this.root.data('form_id'),
				item_id: this.root.data('item_id')
			};
			
			show_page_splash(1);
			
			$.ajax({
				type: 'POST',
				url: DX_CORE.site_url + 'inlineform/' + this.root.data('item_id') + '?_method=DELETE',
				dataType: 'json',
				data: request,
				success: function(data)
				{
					if(typeof data.success != "undefined" && data.success == 0)
					{
						notify_err(data.error);
						hide_page_splash(1);
						return;
					}
					
					window.location = data.redirect;
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(textStatus);
					console.log(jqXHR);
					hide_page_splash(1);
				}
			});
		}
	});
})(jQuery);

(function($)
{
	/**
	 * EmplLinksFix - a jQuery plugin that fix (re-init) links to other employees (manager, reporting manager)
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.EmplLinksFix = function(opts)
	{
		var options = $.extend({}, $.fn.EmplLinksFix.defaults, opts);
		return this.each(function()
		{
			new $.EmplLinksFix(this, options);
		});
	};
	
	$.fn.EmplLinksFix.defaults = {
		profile_url: "/employee/profile/"
	};
	
	/**
	 * EmplLinksFix constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.EmplLinksFix = function(root, opts)
	{
		$.data(root, 'EmplLinksFix', this);
		var self = this;
		this.options = opts;
		this.root = $(root);
                
		this.flds_managers = $('.dx-autocompleate-field[data-item-field=manager_id], .dx-autocompleate-field[data-item-field=reporting_manager_id]', this.root);
		this.tiles_managers = $('.employee-manager-tile', this.root);
                
                this.tiles_managers.each(function()
                {
                    if (!$(this).data("is-fix-init")) {
                        
                        $(this).click(function() {
                            self.show_tile_manager($(this).data("empl-id"));
                        });
                    
                        $(this).data("is-fix-init", 1);
                    }
                });
                                
                this.flds_managers.each(function()
                {
                    if (!$(this).data("is-fix-init")) {
                        var fld = $(this);
                        fld.find(".dx-rel-id-add-btn").off("click"); //remove original event handler which will open CMS form
                        
                        fld.find(".dx-rel-id-add-btn").click(function() {
                            self.show_manager(fld);
                        });
                        
                        // Reset tooltip from edit to view hint
                        fld.find(".dx-rel-id-add-btn").tooltipster('destroy');
                        fld.find(".dx-rel-id-add-btn").attr("title", Lang.get('empl_profile.hint_view_profile'));
                        
                        fld.find(".dx-rel-id-add-btn").tooltipster({
                            theme: 'tooltipster-light',
                            animation: 'grow',
                            maxWidth: 300
                        });
        
                        $(this).data("is-fix-init", 1);
                    }
                });
	};
	
	/**
	 * EmplLinksFix methods
	 */
	$.extend($.EmplLinksFix.prototype, {
		/**
		 * Show manager profile
		 */
		show_manager: function(fld)
		{
                    var item_id = fld.find(".dx-auto-input-id").val();
                    
                    if (item_id == 0) {
                        return;
                    }
                    show_page_splash(1);
                    window.location = this.options.profile_url + item_id;
		},
                show_tile_manager: function(empl_id)
                {
                    show_page_splash(1);
                    window.location = this.options.profile_url + empl_id;
                }
	});
})(jQuery);

$(document).ajaxComplete(function() {
    $(".dx-employee-profile.freeform").EmplLinksFix();
});

$(document).ready(function() {
    $(".dx-employee-profile.freeform").EmplLinksFix();
});
//# sourceMappingURL=elix_profile.js.map
