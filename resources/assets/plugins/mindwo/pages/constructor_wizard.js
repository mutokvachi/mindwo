/**
 * Author:  Eugene Ostapenko <evo@olympsoft.com>
 * License: MIT
 * Created: 25.05.17, 17:48
 */
Module.create('ConstructorWizard', {
	defaults: {
		list_id: 0,
		view_id: 1,
		form_id: 0,
		step: 'names',
		steps: ['names', 'columns', 'fields', 'rights', 'workflows'],
		url: '/constructor/register',
		last_url: '/skats_'
	},
	
	/**
	 * Constructor
	 */
	construct: function() {
		var self = this;
		
		var initName = 'init_' + this.options.step;
		var submitName = 'submit_' + this.options.step;
		
		// init current step
		if($.isFunction(self[initName]))
		{
			self[initName]();
		}
		
		this.root.on('click', '.mt-element-step .link', function() {
			window.location = $(this).data('url');
		});
		
		// submit step
		this.root.on('click', '#submit_step', function() {
			if($.isFunction(self[submitName]))
			{
				self[submitName]();
			}
		});
		
		// go one step back
		this.root.on('click', '#prev_step', function() {
			window.location = self.getPrevUrl();
		});
		
		// Advanced settings button on the top of the page
		$('.dx-adv-btn').click(function() {
			view_list_item('form', self.options.list_id, 3, 0, 0, "", "", {
				after_close: function() {
					window.location.reload();
				}
			});
		});
	},
	
	getStepNumber: function() {
		return this.options.steps.indexOf(this.options.step);
	},
	
	getCurrentUrl: function() {
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
	
	getNextUrl: function(list_id) {
		if(this.getStepNumber() + 1 == this.options.steps.length)
		{
			return this.options.last_url + this.options.view_id;
		}
		
		list_id = typeof list_id !== 'undefined' ? list_id : this.options.list_id;
		
		return this.options.url + '/' + list_id + '/' + this.options.steps[this.getStepNumber() + 1];
	},
	
	getPrevUrl: function() {
		if(this.getStepNumber() == 1)
		{
			return this.options.url + '/' + this.options.list_id;
		}
		
		return this.options.url + '/' + this.options.list_id + '/' + this.options.steps[this.getStepNumber() - 1];
	},
	
	init_columns: function() {
		var self = this;
		
		$(".dx-view-edit-form").ViewEditor({
			view_container: $("#view_editor"),
			reloadBlockGrid: null,
			root_url: getBaseUrl(),
			load_tab_grid: null,
			onsave: function() {
				window.location = self.getNextUrl();
			}
		});
		
		this.viewEditor = $(".dx-view-edit-form").data('ViewEditor');
		
		$('.dx-new-field').click(function() {
			new_list_item(7, 17, self.options.list_id, "", "", {
				after_close: function(frm) {
					var item_id = $(frm).find('input[name="item_id"]').val();
					var item_name = $(frm).find('input[name="title_list"]').val();
					
					if(item_id == 0)
					{
						return;
					}
					
					var item = $(
						'<li class="dd-item" data-id="" data-list-id="" data-is-hidden="0" data-operation-id="0" data-criteria="" data-field-type="varchar" data-rel-list-id="" data-rel-field-id="" data-aggregation-id="0">' +
						'<div class="dd-handle dd3-handle"> </div>' +
						'<div class="dd3-content">' +
						'<div class="row">' +
						'<div class="col-md-10">' +
						'<b class="dx-fld-title"></b>' +
						'<i class="fa fa-filter dx-icon-filter"></i>' +
						'<i class="fa fa-eye-slash dx-icon-hidden"></i>' +
						'</div>' +
						'<div class="col-md-2">' +
						'<a href="javascript:;" title="' + Lang.get('grid.btn_remove_fld') + '" class="pull-right dx-cms-field-remove"><i class="fa fa-trash-o"></i></a>' +
						'<a href="javascript:;" title="' + Lang.get('grid.btn_add_fld') + '" class="pull-right dx-cms-field-add"><i class="fa fa-plus-square-o"></i></a>' +
						'</div>' +
						'</div>' +
						'</div>' +
						'</li>'
					);
					
					item.attr('data-id', item_id);
					item.attr('data-list-id', self.options.list_id);
					item.find('.dx-fld-title').text(item_name);
					
					item.appendTo('.dd.dx-available .dd-list');
					
					item.find('[title]').tooltipster({
						theme: 'tooltipster-light',
						animation: 'grow'
					});
					
					self.viewEditor.setFldEventHandlers(self.viewEditor.frm_el, item, self.viewEditor.options.view_container);
				}
			});
		});
	},
	
	init_fields: function() {
		var self = this;
		
		this.root.find('.dx-constructor-tab-buttons').ConstructorTabs({
			form_id: this.options.form_id,
			list_id: this.options.list_id
		});
		
		this.root.find('.dx-constructor-grid').ConstructorGrid({
			list_id: this.options.list_id
		});
		
		// handle row creation
		this.root.find('.dx-add-row-btn').click(function() {
			if($(this).parents('.dx-constructor-tabs').length)
			{
				var tab = self.root.find('.dx-constructor-tab:visible');
				
				if(tab.hasClass('related-grid'))
				{
					return;
				}
				
				tab.find('.dx-constructor-grid').data('ConstructorGrid').createRow();
				
				// window.scrollTo(0, document.body.scrollHeight);
			}
			
			else
			{
				self.root.find('.dx-constructor-form .dx-constructor-grid').data('ConstructorGrid').createRow();
			}
		});
		
		$('.dx-preview-btn').click(function() {
			self.submit_fields(function() {
				new_list_item(self.options.list_id, 0, 0, "", "");
			});
		});
	},
	
	init_rights: function() {
		var self = this;
		var rights = ['is_new_rights', 'is_edit_rights', 'is_delete_rights'];
		var tbody = $('.dx-constructor-roles-table tbody');
		
		this.root.on('click', '.dx-constructor-add-role', function() {
			new_list_item(23, 105, self.options.list_id, "", "", {
				after_close: function(frm) {
					var role_id = $(frm).find('input[name="id"]').val();
					
					if((typeof role_id == 'undefined') || role_id == 0)
					{
						return;
					}
					
					var role_name = $(frm).find('input[dx_fld_name="role_id"]').val();
					
					var tr = $(
						'<tr>' +
						'<td width="30%">' +
						'<i class="fa fa-key"></i> ' +
						'<a href="javascript:;" class="dx-constructor-edit-role" data-role_id="' + role_id + '">' + role_name + '</a>' +
						'</td>' +
						'<td></td>' +
						'</tr>'
					);
					
					var td = tr.children('td').last();
					
					for(var i = 0; i < rights.length; i++)
					{
						var right = rights[i];
						var input = $(frm).find('input[name="' + right + '"]');
						if(input.length && input.prop('checked'))
						{
							td.append('<label class="badge badge-default">' + Lang.get('constructor.' + right) + '</label> ');
						}
					}
					
					tr.appendTo(tbody);
				}
			});
		});
		
		this.root.on('click', '.dx-constructor-edit-role', function() {
			var a = $(this);
			var td = $(this).closest('td').next();
			var role_id = $(this).data('role_id');
			
			view_list_item('form', role_id, 23, 105, self.options.list_id, "", "", {
				after_close: function(frm) {
					a.text($(frm).find('input[dx_fld_name="role_id"]').val());
					
					td.empty();
					for(var i = 0; i < rights.length; i++)
					{
						var right = rights[i];
						var input = $(frm).find('input[name="' + right + '"]');
						if(input.length && input.prop('checked'))
						{
							td.append('<label class="badge badge-default">' + Lang.get('constructor.' + right) + '</label> ');
						}
					}
				}
			});
		});
	},
	
	submit_names: function() {
		var self = this;
		var listName = this.root.find('#list_name');
		var itemName = this.root.find('#item_name');
		var menuParentID = this.root.find('input[name=parent_id]');
		
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
			item_name: itemName.val(),
			menu_parent_id: menuParentID.val()
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
			success: function(data) {
				hide_page_splash(1);
				
				window.location = self.getNextUrl(data.list_id);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(textStatus);
				console.log(jqXHR);
				hide_page_splash(1);
			}
		});
	},
	
	submit_columns: function() {
		$(".dx-view-edit-form").data('ViewEditor').save();
	},
	
	submit_fields: function(onSuccess) {
		var self = this;
		
		show_page_splash(1);
		
		var request = {
			_method: 'put',
			tabs: [],
			items: []
		};
		
		this.root.find('.dx-constructor-tab-buttons .dd-item').each(function() {
			request.tabs.push($(this).data('id'));
		});
		
		this.root.find('.dx-constructor-form, .dx-constructor-tab.custom-data').each(function() {
			var tab = [];
			
			$(this).find('.dx-constructor-grid .columns').each(function() {
				var row = [];
				
				$(this).find('.dd-item').each(function() {
					row.push($(this).data('id'));
				});
				
				if(row.length)
				{
					tab.push(row);
				}
			});
			
			if(tab.length)
			{
				request.items[$(this).data('tabId')] = tab;
			}
		});
		
		$.ajax({
			type: 'post',
			url: this.getCurrentUrl(),
			dataType: 'json',
			data: request,
			success: function(data) {
				$('.dx-constructor-grid .dd-item.not-in-form').removeClass('not-in-form');
				$('.dx-fields-container .dd-item').addClass('not-in-form');
				
				if(typeof onSuccess === 'function')
				{
					onSuccess();
				}
				else
				{
					window.location = self.getNextUrl();
				}
				
				hide_page_splash(1);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(textStatus);
				console.log(jqXHR);
				hide_page_splash(1);
			}
		});
	},
	
	submit_rights: function() {
		window.location = this.getNextUrl();
	},
	
	/**
	 * Saves workflow data
	 */
	submit_workflows: function() {
		var self = this;
		
		if($('.dx-cms-workflow-form-input-title').val().trim().length > 0)
		{
			var workflow = $('.dx-cms-workflow-form')[0].workflow;
			
			workflow.saveCallback = function() {
				if(workflow.isGraphInit)
				{
					window.location = self.getNextUrl();
				}
			}
			
			workflow.save({self: workflow, initGraph: true});
		}
		else
		{
			window.location = self.getNextUrl();
		}
	}
});
