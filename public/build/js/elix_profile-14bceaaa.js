(function($)
{
	/**
	 * FreeForm - a jQuery plugin for working with arbitrary forms
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.FreeForm = function(root)
	{
		return this.each(function()
		{
			new $.FreeForm(this);
		});
	};
	
	/**
	 * FreeForm constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.FreeForm = function(root)
	{
		$.data(root, 'FreeForm', this);
		var self = this;
		this.root = $(root);
		this.fields = $('[data-name]', this.root);
		this.originalData = {};
		this.editButton = $('.dx-edit-general', this.root);
		this.saveButton = $('.dx-save-general', this.root);
		this.cancelButton = $('.dx-cancel-general', this.root);
		
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
		afterSave: null
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
		this.stickyPanel = $('.profile-sticky', this.root);
		
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
					self.stickyPanel.show(function()
					{
						self.stickyPanel.data('Sticky').init();
						self.stickyPanel.data('Sticky').update();
					});
					
					var tabs = $($.parseHTML('<div>' + data.tabs + '</div>')).find('.tab-pane');
					
					// replace original html content of marked elements with input fields
					for(var i = 0; i < tabs.length; i++)
					{
						var tab = $(tabs[i]);
						var elem = $('[data-tab-title="' + tab.data('tabTitle') + '"]', self.root);
						if(elem.length)
							elem.html(tab.html());
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
						window.location = data.redirect;
						return;
					}
					
					self.editButton.show();
					self.stickyPanel.hide();
					
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
			this.editButton.show();
			this.stickyPanel.hide();
			for(var k in this.originalTabs)
			{
				this.tabs.filter('[data-tab-title="' + k + '"]').html(this.originalTabs[k]);
			}
		},
		
		destroy: function()
		{
			if(!confirm('Are you sure?'))
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
					hide_page_splash(1);
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
/**
 * Author:  Eugene Ostapenko <evo@olympsoft.com>
 * License: MIT
 * Created: 04.11.16, 19:38
 */
(function($)
{
	$.fn.Sticky = function(opts)
	{
		var options = $.extend({}, $.Sticky.defaults, opts);
		return this.each(function()
		{
			new $.Sticky(this, options);
		});
	};
	
	$.Sticky = function(root, opts)
	{
		$.data(root, 'Sticky', this);
		var self = this;
		this.options = opts;
		this.root = $(root);
		this.placeholder = this.root.after('<div/>').next();
		
		this.root.width(this.root.parent().width());
		
		this.placeholder.css({
			position: this.root.css('position'),
			'float': this.root.css('float'),
			display: 'none'
		});
		
		this.init();
		this.update();
		
		$(window).resize(function()
		{
			self.init();
			self.update();
		});
		
		$(window).scroll(function()
		{
			self.init();
			self.update();
		});
	};
	
	$.Sticky.defaults = {
		side: 'top'
	};
	
	$.extend($.Sticky.prototype, {
		init: function()
		{
			this.placeholder.css({
				width: this.root.outerWidth(),
				height: this.root.outerHeight()
			});
			
			if(this.options.side == 'top')
			{
				if(this.root.hasClass('stuck'))
					this.top = this.placeholder.offset().top;
				
				else
					this.top = this.root.offset().top;
			}
			else
			{
				if(this.root.hasClass('stuck'))
					this.top = this.placeholder.offset().top - this.placeholder.height();
				
				else
					this.top = this.root.offset().top;
			}
			
			this.height = this.root.outerHeight();
		},
		
		stick: function()
		{
			this.root.addClass('stuck');
			this.placeholder.css('display', this.root.css('display'));
		},
		
		unstick: function()
		{
			this.root.removeClass('stuck');
			this.placeholder.css('display', 'none');
		},
		
		update: function()
		{
			var self = this;
			if(self.options.side == 'top')
			{
				if($(window).scrollTop() > self.top)
					self.stick();
				
				else
					self.unstick();
			}
			else
			{
				if($(window).scrollTop() + $(window).height() < self.top + self.height)
					self.stick();
				
				else
					self.unstick();
			}
		}
	});
})(jQuery);
//# sourceMappingURL=elix_profile.js.map
