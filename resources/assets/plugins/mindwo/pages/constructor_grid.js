/**
 * Author:  Eugene Ostapenko <evo@olympsoft.com>
 * License: MIT
 * Created: 26.05.17, 23:09
 */

(function($)
{
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
		this.fieldsContainer = $('.dx-fields-container .columns');
		
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
		
		this.root.sortable({
			axis: 'y',
			handle: '.row-handle',
			containment: 'parent'
		});
		
		var sortableOpts = {
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
		
		this.root.find('.columns').sortable(sortableOpts);
		
		this.root.on('click', '.dx-constructor-row-remove', function()
		{
			var row = $(this).closest('.row-container');
			
			row.children('.columns').children().each(function()
			{
				$(this).appendTo(self.fieldsContainer).attr('class', 'col-md-12');
			});
			
			row.remove();
		});
		
		this.root.parent().on('click', '.dx-add-row-btn', function()
		{
			var row = $('<div class="row-container">' +
				'<div class="row-box row-handle"><i class="fa fa-arrows-v"></i></div>' +
				'<div class="row-box row-button"><a href="javascript:;" class="dx-constructor-row-remove"><i class="fa fa-times"></i></a></div>' +
				'<div class="row columns dd-list"></div>' +
				'</div>'
			).appendTo(self.root);
			
			row.find('.columns').sortable(sortableOpts);
		})
	};
	
	$.extend($.ConstructorGrid.prototype, {
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
			row.each(function()
			{
				var items = row.children().filter(function()
				{
					return $(this).css('position') !== 'absolute';
				});
				var count = items.length;
				var col = Math.floor(12 / count);
				
				items.each(function()
				{
					$(this).removeClass(function(index, className)
					{
						return (className.match(/(^|\s)col-\S+/g) || []).join(' ');
					});
					$(this).addClass('col-md-' + col);
				});
			});
		}
	});
})(jQuery);