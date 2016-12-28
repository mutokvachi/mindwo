/**
 * Author:  Eugene Ostapenko <evo@olympsoft.com>
 * License: MIT
 * Created: 04.11.16, 19:33
 */
(function($)
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
		this.tabs = $('.tab-pane', this.root).not(".dx-grid-tab-pane");
		
		// detached fields, that are custom placed outside of main form (e.g. avatar in employee profile)
		this.fields = $('[data-name]', this.root);
		this.fieldNames = [];
		
		this.fields.each(function()
		{
			self.fieldNames.push($(this).data('name'));
		});
		
		this.originalTabs = {};
		this.originalFields = {};
		this.editButton = $('.dx-edit-profile', this.root);
		this.saveButton = $('.dx-save-profile', this.root);
		this.cancelButton = $('.dx-cancel-profile', this.root);
		this.deleteButton = $('.dx-delete-profile', this.root);
		this.requests = {};
		this.onRequestSuccess = [];
		this.onRequestFailed = [];
		
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
		initRequest: function(total)
		{
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
		 * @param {boolean} is_success Parmeter if process was successful
		 */
		setRequestStatus: function(is_success)
		{
			if(is_success)
			{
				this.requests.succeeded++;
			}
			else
			{
				this.requests.failed++;
			}
			
			if(this.requests.total === (this.requests.succeeded + this.requests.failed))
			{
				if(this.requests.failed === 0)
				{
					for(var i = 0; i < this.onRequestSuccess.length; i++)
					{
						this.onRequestSuccess[i].func(this.onRequestSuccess[i].args);
					}
				}
				else
				{
					for(var i = 0; i < this.onRequestFailed.length; i++)
					{
						this.onRequestFailed[i].func(this.onRequestFailed[i].args);
					}
				}
				
				hide_page_splash(1);
			}
		},
		
		storeOriginalData: function()
		{
			var self = this;
			
			this.fields = $('[data-name]', this.root);
			
			this.tabs.each(function()
			{
				self.originalTabs[$(this).data('tabTitle')] = $(this).html();
			});
			
			this.fields.each(function()
			{
				self.originalFields[$(this).data('name')] = $(this).html();
			});
			
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
				tab_list: [],
				field_list: this.fieldNames
			};
			
			this.storeOriginalData();
			
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
					
					var tabs = $($.parseHTML('<div>' + data.tabs + '</div>')).find('.tab-pane').not(".dx-grid-tab-pane");
					
					// replace original html content of marked elements with input fields
					for(var i = 0; i < tabs.length; i++)
					{
						var tab = $(tabs[i]);
						var elem = $('[data-tab-title="' + tab.data('tabTitle') + '"]', self.root);
						if(elem.length)
							elem.html(tab.html());
					}
					
					for(var name in data.fields)
					{
						$('[data-name="' + name + '"]').html(data.fields[name]);
					}
					
					if(self.root.data('has_users_documents_access') == 1)
					{
						window.DxEmpPersDocs.toggleDisable(false);
					}
                                        
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
			formData.append('field_list', JSON.stringify(this.fieldNames));
			
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
						if(self.root.data('has_users_documents_access') == 1)
						{
							window.DxEmpPersDocs.userId = data.item_id;
							
							// Custom tab
							window.DxEmpPersDocs.onClickSaveDocs(function()
							{								
								hide_page_splash(1);
								$('.dx-stick-footer').hide();
								window.location = data.redirect;
							});
						}
						else
						{                                                    
							hide_page_splash(1);
							$('.dx-stick-footer').hide();
							window.location = data.redirect;
						}
						
						return;
					}
					
					if(self.root.data('has_users_documents_access') == 1)
					{
						// Custom tab
						window.DxEmpPersDocs.onClickSaveDocs(function()
						{
							window.DxEmpPersDocs.toggleDisable(true);
							
							self.editButton.show();
							
							var tabs = $($.parseHTML('<div>' + data.tabs + '</div>')).find('.tab-pane').not(".dx-grid-tab-pane");
							
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
					}
					else
					{
						self.editButton.show();
						
						var tabs = $($.parseHTML('<div>' + data.tabs + '</div>')).find('.tab-pane').not(".dx-grid-tab-pane");
						
						// replace original html content of marked elements with input fields
						for(var i = 0; i < tabs.length; i++)
						{
							var tab = $(tabs[i]);
							var elem = $('[data-tab-title="' + tab.data('tabTitle') + '"]', self.root);
							if(elem.length)
								elem.html(tab.html());
						}
						
						for(var name in data.fields)
						{
							$('[data-name="' + name + '"]').html(data.fields[name]);
						}
						
						if(self.options.afterSave)
						{
							self.options.afterSave();
						}
						
						hide_page_splash(1);
						$('.dx-stick-footer').hide();
					}
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
			
			for(var name in this.originalFields)
			{
				this.fields.filter('[data-name="' + name + '"]').html(this.originalFields[name]);
			}
			
			$('.dx-stick-footer').hide();
			
			if(this.root.data('has_users_documents_access') == 1)
			{
				window.DxEmpPersDocs.cancelEditMode();
			}
		},
		
		/**
		 * Delete an item from storage.
		 */
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
