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
		editBtnSelector: '.dx-edit-general',
		saveBtnSelector: '.dx-save-general',
		cancelBtnSelector: '.dx-cancel-general',
		names: []
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
		this.fields = $('[data-name]', this.root).filter(function()
		{
			if(self.options.names.length == 0 || self.options.names.indexOf($(this).data('name')) != -1)
				return true;
		});
		this.originalData = {};
		this.editButton = $(this.options.editBtnSelector, this.root);
		this.saveButton = $(this.options.saveBtnSelector, this.root);
		this.cancelButton = $(this.options.cancelBtnSelector, this.root);
		
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
					name: $(this).data('name'),
					display: ($(this).data('display') ? $(this).data('display') : 'raw')
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
					console.log(errorThrown);
				}
			});
		},
		
		/**
		 * Submit input field values to the server
		 */
		save: function()
		{
			var self = this;
			var fieldMetadata = {};
			var formData = process_data_fields(this.root.attr('id'));
			formData.append('model', this.root.data('model'));
			formData.append('item_id', this.root.data('item_id'));
			formData.append('list_id', this.root.data('list_id'));
			formData.append('edit_form_id', this.root.data('form_id'));
			
			// collect metadata of input fields
			this.fields.each(function()
			{
				fieldMetadata[$(this).data('name')] = {
					display: $(this).data('display') ? $(this).data('display') : 'raw'
				};
			});
			
			formData.append('field_metadata', JSON.stringify(fieldMetadata));
			
			show_page_splash(1);
			
			// submit a request
			$.ajax({
				type: 'POST',
				url: DX_CORE.site_url + 'freeform/' + this.root.data('item_id') + '?_method=PUT',
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
					console.log(errorThrown);
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