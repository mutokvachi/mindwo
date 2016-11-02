/**
 * FreeForm - a jQuery plugin for working with arbitrary forms
 *
 * @param root
 * @returns {*}
 * @constructor
 */
$.fn.FreeForm = function(root)
{
	return this.each(function(){
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
	this.editButton.click(function() {
		self.edit();
	});
	this.saveButton.click(function() {
		self.save();
	});
	this.cancelButton.click(function() {
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
	edit: function() {
		var self = this;
		
		// a structure for JSON request
		var request = {
			model: this.root.data('model'),
			item_id: this.root.data('item_id'),
			list_id: this.root.data('list_id'),
			fields: []
		};
		
		// collect names of input fields marked with data-name attribute
		this.fields.each(function() {
			self.originalData[$(this).data('name')] = $(this).html();
			request.fields.push({
				name: $(this).data('name')
			});
		});
		
		// perform a request to the server
		$.ajax({
			type: 'POST',
			url: DX_CORE.site_url + 'freeform/' + request.item_id + '/edit',
			dataType: 'json',
			data: request,
			success: function(data)
			{
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
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				console.log(textStatus);
				console.log(jqXHR);
			}
		});
	},
	
	/**
	 * Submit input field values to the server
	 */
	save: function() {
		var self = this;
		
		// JSON structure
		var request = {
			model: this.root.data('model'),
			item_id: this.root.data('item_id'),
			list_id: this.root.data('list_id'),
			fields: []
		};
		
		// collect values of input fields
		this.fields.each(function() {
			request.fields.push({
				name: $(this).data('name'),
				data: $(this).find('[name]').val()
			});
		});
		
		// submit a request
		$.ajax({
			type: 'POST',
			url: DX_CORE.site_url + 'freeform/' + request.item_id + '?_method=PUT',
			dataType: 'json',
			data: request,
			success: function(data)
			{
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
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				console.log(textStatus);
				console.log(jqXHR);
			}
		});
	},
	
	/**
	 * Remove input fields and display original HTML
	 */
	cancel: function() {
		var self = this;
		this.editButton.show();
		this.saveButton.hide();
		this.cancelButton.hide();
		this.fields.each(function() {
			$(this).html(self.originalData[$(this).data('name')]);
		});
	}
});
    
