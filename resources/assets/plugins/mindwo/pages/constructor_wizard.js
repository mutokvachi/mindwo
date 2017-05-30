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
		if($.isFunction(self[initName]))
		{
			self[initName]();
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
		
		// Advanced settings button on the top of the page
		$('.dx-adv-btn').click(function()
		{
			view_list_item('form', self.options.list_id, 3, 0, 0, "", "", {
				after_close: function()
				{
					window.location.reload();
				}
			});
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
		
		init_columns: function()
		{
			var self = this;
			
			$(".dx-view-edit-form").ViewEditor({
				view_container: $("#view_editor"),
				reloadBlockGrid: null,
				root_url: getBaseUrl(),
				load_tab_grid: null,
				onsave: function()
				{
					window.location = self.getNextUrl();
				}
			});
			
			$('.dx-new-field').click(function()
			{
				var field_closed = function(frm)
				{
					// update here fields list
					// add in form in new row as last item too
					
					// get meta data from frm with jquery find
					
					// all cms forms have field item_id if it is 0 then item is not saved
					alert(frm.html());
				};
				
				// if list_id = 0 then save list first with ajax then continue
				new_list_item(7, 17, self.options.list_id, "", "", {
					after_close: field_closed
				});
			});
		},
		
		init_fields: function()
		{
			var self = this;
			
			this.root.find('.constructor-grid').ConstructorGrid();
			
			$('.dx-preview-btn').click(function()
			{
				// if list_id = 0 then try to save with AJAX (must be register title provided)
				// for new registers user object_id = 140
				self.submit_fields(function()
				{
					new_list_item(self.options.list_id, 0, 0, "", "");
				});
			});
		},
		
		init_rights: function()
		{
			var self = this;
			
			this.root.find('.dx-constructor-add-role').click(function()
			{
				new_list_item(23, 105, self.options.list_id, "", "", {
					after_close: function(frm)
					{
					
					}
				});
			});
		},
		
		submit_names: function()
		{
			var self = this;
			var listName = this.root.find('#list_name');
			var itemName = this.root.find('#item_name');
			
			if(listName.length && !listName.val())
			{
				toastr.error(Lang.get('constructor.enter_name'));
				return false;
			}
			
			if(itemName.length && !itemName.val())
			{
				toastr.error(Lang.get('constructor.enter_item_name'));
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
			$(".dx-view-edit-form").data('ViewEditor').save();
		},
		
		submit_fields: function(onSuccess)
		{
			var self = this;
			
			show_page_splash(1);
			
			var request = {
				_method: 'put',
				items: []
			};
			
			this.root.find('.constructor-grid .columns').each(function()
			{
				var row = [];
				
				$(this).find('.dd-item').each(function()
				{
					row.push($(this).data('id'));
				});
				
				if(row.length)
				{
					request.items.push(row);
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
					if(typeof onSuccess === 'function')
					{
						onSuccess();
					}
					else
					{
						window.location = self.getNextUrl();
					}
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
