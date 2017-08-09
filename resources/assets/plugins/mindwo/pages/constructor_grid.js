/**
 * ConstructorGrid - a plugin that manages grid of constructor and allows form to be designed
 * by dragging and dropping elements.
 */
Module.create('ConstructorGrid', {
	defaults: {
		'rowHtml': ''
	},
	/**
	 * Constructor
	 */
	construct: function()
	{
		var self = this;
		
		this.wizard = $('.dx-constructor-wizard').data('ConstructorWizard');
		
		this.fieldsContainer = $('.dx-fields-container .columns');
		
		this.dialog = $('#fields_popup');
		this.dialogId = this.dialog.find('[name="field_id"]');
		this.dialogField = this.dialog.find('[name="title_form"]');
		this.dialogHidden = this.dialog.find('[name="is_hidden"]');
		
		// configuration of Sortable plugin for use inside of form rows
		this.sortableOpts = {
			connectWith: '.columns',
			handle: '.dd-handle',
			placeholder: 'placeholder',
			forcePlaceholderSize: true,
			forceHelperSize: true,
			cursorAt: {
				left: 30,
				top: 15
			},
			over: function(event, ui)
			{
				self.updateGrid();
			},
			out: function(event, ui)
			{
				self.updateGrid();
			},
			deactivate: function(event, ui)
			{
				self.updateRow($(this));
			},
			change: function(event, ui)
			{
				self.updateGrid();
			},
			receive: function(event, ui)
			{
				if($(this).children().length > 4)
				{
					$(ui.sender).sortable('cancel');
				}
				self.updateGrid();
			},
			helper: this.sortHelper,
			start: this.onSortStart,
			stop: this.onSortStop
		};
		
		// fields list at left side
		this.fieldsContainer.sortable({
			connectWith: '.columns',
			handle: '.dd-handle',
			placeholder: 'placeholder',
			forcePlaceholderSize: true,
			receive: function(event, ui)
			{
				$(ui.item).attr('class', 'col-md-12');
			},
			start: this.onSortStart,
			stop: this.onSortStop
		});
		
		// form rows
		this.root.sortable({
			// axis: 'y',
			// containment: 'parent',
			connectWith: '.dx-constructor-grid',
			handle: '.row-handle',
			helper: this.sortHelper,
			start: this.onSortStart,
			stop: this.onSortStop
		});
		
		// form row columns
		this.root.find('.columns').sortable(this.sortableOpts);
		
		this.root.on('click', '.dx-cms-field-remove', function()
		{
			var row = $(this).closest('.dd-list');
			$(this).tooltipster('hide');
			self.removeField($(this).closest('.dd-item').parent());
			self.updateRow(row);
		});
		
		this.root.on('click', '.dx-cms-field-edit', function()
		{
			var item = $(this).closest('.dd-item');
			
			if(item.hasClass('not-in-form'))
			{
				self.wizard.submit_fields(function()
				{
					self.showProperties(item);
				});
			}
			else
			{
				self.showProperties(item);
			}
		});
		
		this.dialog.on('click', '.dx-view-btn-save', function()
		{
			self.saveProperties();
		});
		
		// handle row deletion
		this.root.on('click', '.dx-constructor-row-remove', function()
		{
			$(this).tooltipster('hide');
			self.removeRow($(this).closest('.dx-constructor-row'))
		});
	},
	
	/**
	 * When a field or a row is started to drag, highlight appropriate tabs where it can be dropped to.
	 * @param event
	 * @param ui
	 */
	onSortStart: function(event, ui)
	{
		$('.dx-constructor-tab-buttons').addClass('highlight');
	},
	
	/**
	 * Remove highlights on drag stop.
	 * @param event
	 * @param ui
	 */
	onSortStop: function(event, ui)
	{
		$('.dx-constructor-tab-buttons').removeClass('highlight');
	},
	
	/**
	 * Clones element being dragged. It is needed to drag elements between hidden tabs.
	 * @param event
	 * @param element
	 * @returns {*}
	 */
	sortHelper: function(event, element)
	{
		var el = $(element.clone()).appendTo(document.body);
		return el.get(0);
	},
	
	/**
	 * Add a new row to the grid.
	 */
	createRow: function(type)
	{
		var html = (type === 'columns') ? this.options.rowHtml : this.options.labelHtml;
		
		var row = $(html).appendTo(this.root);
		
		row.find('.dx-constructor-row-remove').tooltipster({
			theme: 'tooltipster-light',
			animation: 'grow'
		});
		
			if(type === 'columns')
		{
			row.find('.columns').sortable(this.sortableOpts);
		}
	},
	
	/**
	 * Handle deletion of a field.
	 * @param field
	 */
	removeField: function(field)
	{
		if(field.hasClass('dx-field'))
		{
			field.appendTo(this.fieldsContainer).attr('class', 'col-md-12');
		}
		else
		{
			field.remove();
		}
	},
	
	/**
	 * Handle removal of a row.
	 * @param row
	 */
	removeRow: function(row)
	{
		var self = this;
		
		row.children('.columns').children().each(function()
		{
			self.removeField($(this));
		});
		
		row.remove();
	},
	
	/**
	 * Open modal dialog with properties of a field.
	 * @param item
	 */
	showProperties: function(item)
	{
		var fieldId = item.data('id');
		var field = item.find('.dx-fld-title').text();
		var hidden = item.data('hidden');
		
		this.dialogId.val(fieldId);
		this.dialogField.val(field);
		
		if((hidden && !this.dialogHidden.prop('checked')) || (!hidden && this.dialogHidden.prop('checked')))
		{
			this.dialogHidden.click();
		}
		
		this.dialog.modal('show');
	},
	
	/**
	 * Update properties of a field via ajax call.
	 */
	saveProperties: function()
	{
		var self = this;
		
		var item = $('.dd-item[data-id="' + self.dialogId.val() + '"]');
		
		//show_page_splash(1);
		
		var request = {
			_method: 'put',
			field_id: self.dialogId.val(),
			title_form: self.dialogField.val(),
			is_hidden: self.dialogHidden.prop('checked') ? '1' : '0'
		};
		
		$.ajax({
			type: 'post',
			url: '/constructor/register/' + self.options.list_id + '/field_update',
			dataType: 'json',
			data: request,
			success: function(data)
			{
				item.find('.dx-fld-title').text(self.dialogField.val());
				
				if(self.dialogHidden.prop('checked'))
				{
					item.data('hidden', '1');
					item.addClass('dx-field-hidden');
				}
				else
				{
					item.data('hidden', '0');
					item.removeClass('dx-field-hidden');
				}
				
				//hide_page_splash(1);
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				console.log(textStatus);
				console.log(jqXHR);
				hide_page_splash(1);
			}
		});
		
		self.dialog.modal('hide');
	},
	
	/**
	 * Update all rows.
	 */
	updateGrid: function()
	{
		var self = this;
		
		this.root.find('.row.columns').each(function()
		{
			self.updateRow($(this));
		});
	},
	
	/**
	 * Calculate correct width of fields and apply grid classes to them.
	 * @param row
	 */
	updateRow: function(row)
	{
		var items = row.children().filter(function()
		{
			return $(this).css('position') !== 'absolute' && $(this).css('display') !== 'none';
		});
		
		var count = items.length;
		
		if(!count)
		{
			return;
		}
		
		var col = Math.floor(12 / count);
		
		items.each(function()
		{
			$(this).removeClass(function(index, className)
			{
				return (className.match(/(^|\s)col-\S+/g) || []).join(' ');
			});
			$(this).addClass('col-md-' + col);
		});
	}
});