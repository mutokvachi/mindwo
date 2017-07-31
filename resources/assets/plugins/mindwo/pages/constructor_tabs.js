/**
 * ConstructorTabs - manages form tabs.
 */
Module.create('ConstructorTabs', {
	/**
	 * Default values of options
	 */
	defaults: {
		form_id: 0,
		list_id: 0,
		relatedHtml: ''
	},
	/**
	 * Constructor
	 */
	construct: function() {
		var self = this;
		
		this.activeTabButton = $('.dx-constructor-tab-button').first().addClass('active');
		this.activeTab = $('.dx-constructor-tab').first();
		this.tabAddModal = $('#fields_tab_add');
		this.tabEditModal = $('#fields_tab_edit');
		
		this.root.find('.dd-list').sortable({
			containment: 'parent'
		});
		
		this.root.on('click', '.dd-item a.title', function() {
			self.activate($(this).closest('.dd-item'));
		});
		
		this.root.find('.dd-item.custom-data').each(function() {
			self.initButton($(this));
		});
		
		$('.dx-tab-add-btn').click(function() {
			self.add();
		});
		
		$('.dx-tab-edit-btn').click(function() {
			self.edit();
		});
		
		$('.dx-tab-delete-btn').click(function() {
			self.del();
		});
		
		this.tabAddModal.on('click', '.dx-tab-add-save-btn', function() {
			self.addSave();
		});
		
		this.tabEditModal.on('click', '.dx-tab-edit-save-btn', function() {
			self.editSave();
		});
	},
	
	/**
	 * Initialize droppable plugin for specified tab button.
	 * @param button
	 */
	initButton(button)
	{
		var self = this;
		
		var timer;
		
		button.droppable({
			accept: '.dx-field, .dx-constructor-row',
			tolerance: 'pointer',
			over: function(event, ui) {
				var tab = $(this);
				
				timer = setTimeout(function() {
					self.activate(tab)
				}, 1000)
			},
			out: function(event, ui) {
				clearTimeout(timer);
			},
			deactivate: function(event, ui) {
				clearTimeout(timer);
			}
		});
	},
	
	/**
	 * Activate specified tab
	 * @param tab
	 */
	activate: function(tab) {
		$('.dx-constructor-tab:visible').hide();
		
		this.root.find('.dx-constructor-tab-button').removeClass('active');
		
		this.activeTabButton = tab;
		this.activeTabButton.addClass('active');
		
		this.activeTab = $('.dx-constructor-tab.tab-id-' + tab.data('id')).show();
		
		if(tab.hasClass('custom-data'))
		{
			this.activeTab.find('.dx-constructor-grid').data('ConstructorGrid').updateGrid();
		}
	},
	
	/**
	 * Handler for Add tab button
	 */
	add: function() {
		var self = this;
		open_form('form', 0, 16, 66, this.options.form_id, "", 1, "", {
			before_show: function(frm) {
				var order_index = (($('.dx-constructor-tab-button').length + 1) * 10) + 10;
				frm.find('input[name="order_index"]').val(order_index).prop('disabled', true);
			},
			after_close: function(frm) {
				var id = frm.find('input[name="item_id"]').val();
				
				if(id == 0)
				{
					return;
				}
				
				var title = frm.find('input[name="title"]').val();
				var order_index = frm.find('input[name="order_index"]').val();
				var is_custom_data = (frm.find('input[name="is_custom_data"]').bootstrapSwitch('state'));
				
				var button = $(self.options.tabButton);
				button.find('.title').text(title);
				button.addClass('tab-id-' + id);
				button.addClass(is_custom_data ? 'custom_data' : 'related-grid');
				button.attr('data-id', id);
				button.attr('data-order', order_index);
				button = button.appendTo('.dx-constructor-tab-buttons .dd-list');
				
				var tab = $('<div class="dx-constructor-tab" style="display: none"></div>').appendTo('.dx-constructor-tabs');
				tab.addClass('tab-id-' + id);
				tab.attr('data-tab-id', id);
				tab.attr('data-tab-title', title);
				tab.append('<h5>' + title + '</h5>');
				
				if(is_custom_data)
				{
					tab.addClass('custom-data');
					
					self.initButton(button);
					
					var grid = $('<div class="dx-constructor-grid"></div>').appendTo(tab);
					
					grid.ConstructorGrid({
						list_id: self.options.list_id
					});
					
					grid.data('ConstructorGrid').createRow();
					grid.data('ConstructorGrid').createRow();
					grid.data('ConstructorGrid').createRow();
					grid.data('ConstructorGrid').createRow();
				}
				else
				{
					tab.addClass('related-grid');
					
					var html = $(self.options.relatedHtml);
					
					var tmp = frm.find('input[dx_fld_name="grid_list_id"]');
					tmp.length && html.find('input[name="list_title"]').val(tmp.val());
					
					tmp = frm.find('input[dx_fld_name="grid_list_field_id"]');
					tmp.length && html.find('input[name="field_title"]').val(tmp.val());
					
					html.appendTo(tab);
				}
				
				self.activate(button);
			}
		});
	},
	
	/**
	 * Handler for Edit tab button
	 */
	edit: function() {
		var self = this;
		var tab = this.activeTab;
		var button = this.activeTabButton;
		
		open_form('form', tab.data('tabId'), 16, 66, this.options.form_id, "", 1, "", {
			before_show: function(frm) {
				frm.find('input[name="order_index"]').prop('disabled', true);
				frm.find('input[name="is_custom_data"]').bootstrapSwitch('toggleDisabled', true);
			},
			after_close: function(frm) {
				if(frm.find('input[name="item_id"]').val() == 0)
				{
					return;
				}
				var title = frm.find('input[name="title"]').val();
				button.find('.title').text(title);
				tab.attr('data-tab-title', title);
				tab.children('h5').text(title);
				
				if(tab.hasClass('related-grid'))
				{
					var tmp = frm.find('input[dx_fld_name="grid_list_id"]');
					tmp.length && tab.find('input[name="list_title"]').val(tmp.val());
					
					tmp = frm.find('input[dx_fld_name="grid_list_field_id"]');
					tmp.length && tab.find('input[name="field_title"]').val(tmp.val());
				}
			}
		});
	},
	
	/**
	 * Handler for Delete tab button
	 */
	del: function() {
		var self = this;
		var tab = this.activeTab;
		var button = this.activeTabButton;
		
		if(tab.hasClass('custom-data') && (tab.find('.dx-constructor-row .dx-field').length > 0))
		{
			toastr.info(Lang.get('constructor.tab_empty'));
			return;
		}
		
		var func = function() {
			var request = {
				_method: 'delete',
				id: tab.data('tabId')
			};
			
			$.ajax({
				type: 'post',
				url: '/constructor/register/tab/' + tab.data('tabId'),
				dataType: 'json',
				data: request,
				success: function(data)
				{
					hide_page_splash(1);
					
					tab.remove();
					button.remove();
					
					self.activate($('.dx-constructor-tab-button').first());
					
					toastr.success(Lang.get('constructor.tab_del_success'))
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(textStatus);
					console.log(jqXHR);
					hide_page_splash(1);
				}
			});
		};
		
		PageMain.showConfirm(func, null,
			Lang.get('constructor.confirm_action'),
			Lang.get('constructor.tab_del_confirm'),
			''
		);
	}
});
