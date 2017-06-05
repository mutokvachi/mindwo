/**
 * Author:  Eugene Ostapenko <evo@olympsoft.com>
 * License: MIT
 * Created: 26.05.17, 23:09
 */

(function($)
{
	/**
	 *
	 * @param opts
	 * @constructor
	 */
	$.fn.ConstructorGrid = function(opts)
	{
		var options = $.extend({}, $.fn.ConstructorGrid.defaults, opts);
		
		return this.each(function()
		{
			new $.ConstructorGrid(this, options);
		});
	};
	
	$.fn.ConstructorGrid.defaults = {};
	
	$.ConstructorGrid = function(root, opts)
	{
		$.data(root, 'ConstructorGrid', this);
		
		var self = this;
		this.root = $(root);
		this.options = opts;
		
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
			receive: function(event, ui)
			{
				if($(this).children().length > 4)
				{
					$(ui.sender).sortable('cancel');
				}
			}
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
			}
		});
		
		// form rows
		this.root.sortable({
			axis: 'y',
			handle: '.row-handle',
			containment: 'parent'
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
			self.removeRow($(this).closest('.row-container'))
		});
		
		// handle row creation
		this.root.parent().on('click', '.dx-add-row-btn', function()
		{
			self.createRow();
		});
	};
	
	$.extend($.ConstructorGrid.prototype, {
		createRow: function()
		{
			var row = $('<div class="row-container">' +
				'<div class="row-box row-handle"><i class="fa fa-arrows-v"></i></div>' +
				'<div class="row-box row-button">' +
				'<a href="javascript:;" class="dx-constructor-row-remove" title="' + Lang.get('constructor.remove_row') + '"><i class="fa fa-times"></i></a>' +
				'</div>' +
				'<div class="row columns dd-list"></div>' +
				'</div>'
			).appendTo(this.root);
			
			row.find('.dx-constructor-row-remove').tooltipster({
				theme: 'tooltipster-light',
				animation: 'grow'
			});
			
			row.find('.columns').sortable(this.sortableOpts);
		},
		removeField: function(field)
		{
			field.appendTo(this.fieldsContainer).attr('class', 'col-md-12');
		},
		removeRow: function(row)
		{
			var self = this;
			
			row.children('.columns').children().each(function()
			{
				self.removeField($(this));
			});
			
			row.remove();
		},
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
		saveProperties()
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
		updateGrid: function()
		{
			var self = this;
			
			this.root.find('.row.columns').each(function()
			{
				self.updateRow($(this));
			});
		},
		updateRow: function(row)
		{
			var items = row.children().filter(function()
			{
				return $(this).css('position') !== 'absolute';
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
})(jQuery);