/**
 * Author:  Eugene Ostapenko <evo@olympsoft.com>
 * License: MIT
 * Created: 25.05.17, 17:48
 */

(function($)
{
	$.fn.ConstructorWizard = function(opts)
	{
		var options = $.extend({}, $.fn.ConstructorWizard.defaults, opts);
		
		return this.each(function()
		{
			new $.ConstructorWizard(this, options);
		});
	};
	
	$.fn.ConstructorWizard.defaults = {
		list_id: 0,
		view_id: 1,
		step: 'names',
		steps: ['names', 'columns', 'fields', 'rights', 'menu'],
		url: '/constructor/register',
		last_url: '/skats_'
	};
	
	$.ConstructorWizard = function(root, opts)
	{
		$.data(root, 'ConstructorWizard', this);
		
		var self = this;
		this.root = $(root);
		this.options = opts;
		
		var initName = 'init_' + this.options.step;
		var submitName = 'submit_' + this.options.step;
		
		// init current step
		if($.isFunction(this[initName]))
		{
			this[initName]();
		}
		
		// submit step
		this.root.on('click', '#submit_step', function()
		{
			if($.isFunction(self[submitName]))
			{
				self[submitName]();
			}
		});
		
		// go one step back
		this.root.on('click', '#prev_step', function()
		{
			window.location = self.getPrevUrl();
		});
	};
	
	$.extend($.ConstructorWizard.prototype, {
		getStepNumber: function()
		{
			return this.options.steps.indexOf(this.options.step);
		},
		
		getCurrentUrl: function()
		{
			if(!this.options.list_id)
			{
				return this.options.url;
			}
			
			if(this.options.step == 'names')
			{
				return this.options.url + '/' + this.options.list_id;
			}
			
			return this.options.url + '/' + this.options.list_id + '/' + this.options.step;
		},
		
		getNextUrl: function(list_id)
		{
			if(this.getStepNumber() + 1 == this.options.steps.length)
			{
				return this.options.last_url + this.options.view_id;
			}
			
			list_id = typeof list_id !== 'undefined' ? list_id : this.options.list_id;
			
			return this.options.url + '/' + list_id + '/' + this.options.steps[this.getStepNumber() + 1];
		},
		
		getPrevUrl: function()
		{
			if(this.getStepNumber() == 1)
			{
				return this.options.url + '/' + this.options.list_id;
			}
			
			return this.options.url + '/' + this.options.list_id + '/' + this.options.steps[this.getStepNumber() - 1];
		},
		
		init_fields: function()
		{
			this.root.find('.dd-item').draggable({
				handle: '.dd-handle',
				revert: 'invalid',
				helper: 'clone'
			});
			
			this.root.find('.droppable-grid td').droppable({
				accept: '.dd-item',
				drop: function(event, ui)
				{
					$(this).append(ui.draggable);
					ui.draggable.addClass('dropped');
				}
			});
			
			this.root.on('click', '.dx-cms-field-remove', function()
			{
				$(this)
					.closest('.dropped')
					.removeClass('dropped')
					.appendTo('.dd-list');
			});
		},
		
		submit_names: function()
		{
			var self = this;
			var listName = this.root.find('#list_name');
			var itemName = this.root.find('#item_name');
			
			if(listName.length && !listName.val())
			{
				toastr.error('Please enter register name.');
				return false;
			}
			
			if(itemName.length && !itemName.val())
			{
				toastr.error('Please enter item name.');
				return false;
			}
			
			show_page_splash(1);
			
			var request = {
				list_name: listName.val(),
				item_name: itemName.val()
			};
			
			if(this.options.list_id)
			{
				request._method = 'put';
			}
			
			$.ajax({
				type: 'post',
				url: this.getCurrentUrl(),
				dataType: 'json',
				data: request,
				success: function(data)
				{
					hide_page_splash(1);
					window.location = self.getNextUrl(data.list_id);
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(textStatus);
					console.log(jqXHR);
					hide_page_splash(1);
				}
			});
		},
		
		submit_columns: function()
		{
			window.location = this.getNextUrl();
		},
		
		submit_fields: function()
		{
			var self = this;
			
			show_page_splash(1);
			
			var request = {
				_method: 'put',
				items: []
			};
			
			this.root.find('.droppable-grid td').each(function()
			{
				var dd = $(this).children('.dd-item');
				
				if(dd.length)
				{
					var item = {
						id: dd.data('id'),
						row: $(this).parent().prevAll().length + 1,
						col: $(this).prevAll().length + 1
					};
					request.items.push(item);
				}
			});
			
			$.ajax({
				type: 'post',
				url: this.getCurrentUrl(),
				dataType: 'json',
				data: request,
				success: function(data)
				{
					hide_page_splash(1);
					window.location = self.getNextUrl();
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(textStatus);
					console.log(jqXHR);
					hide_page_splash(1);
				}
			});
		},
		
		submit_rights: function()
		{
			window.location = this.getNextUrl();
		},
		
		submit_menu: function()
		{
			window.location = this.getNextUrl();
		}
	});
})(jQuery);
