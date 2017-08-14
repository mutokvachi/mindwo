(function($)
{
	/**
	 * ViewEditor - a jQuery plugin that inits view editor functionality (columns setting by drag & drop)
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.ViewEditor = function(opts)
	{
		var options = $.extend({}, $.fn.ViewEditor.defaults, opts);
		return this.each(function()
		{
			new $.ViewEditor(this, options);
		});
	};
	
	$.fn.ViewEditor.defaults = {
		view_container: null,
		reloadBlockGrid: null,
		root_url: "/",
		load_tab_grid: null,
		onsave: null
	};
	
	/**
	 * ViewEditor constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.ViewEditor = function(root, opts)
	{
		$.data(root, 'ViewEditor', this);
		var self = this;
		
		this.options = opts;
		this.root = $(root);
		
		if(this.root.hasClass("is-init"))
		{
			return; // editor is allready initialized
		}
		
		this.root.find(".dx-cms-nested-list").nestable();
		
		this.handleBtnSaveView(this.options.view_container);
		this.handleBtnCopy(this.options.view_container);
		this.handleBtnDelete(this.options.view_container);
		this.handleFieldOperation(this.options.view_container);
		
		var frm_el = this.root.closest(".dx-popup-modal");
		
		this.frm_el = frm_el;
		
		this.setFldEventHandlers(frm_el, frm_el, this.options.view_container);
		this.handleSearchField();
		this.handleIsMyCheck(this.root);
		
		this.root.find(".dx-view-btn-copy").show();
		this.root.find(".dx-view-btn-delete").show();
		
		this.root.addClass("is-init");
	};
	
	/**
	 * InlineForm methods
	 */
	$.extend($.ViewEditor.prototype, {
		
		/**
		 * Sets events for added/moved field
		 *
		 * @param {object} frm_el Fields UI forms HTML object
		 * @param {object} fld_el Field element HTML object
		 * @param {object} view_container Grid view main object's HTML element
		 * @returns {undefined}
		 */
		setFldEventHandlers: function(frm_el, fld_el, view_container)
		{
			var self = this;
			
			fld_el.find('.dx-cms-field-remove').click(function()
			{
				self.removeFld(frm_el, $(this), view_container);
			});
			
			fld_el.find('.dx-cms-field-add').click(function()
			{
				self.addFld(frm_el, $(this), view_container);
			});
			
			fld_el.find('.dx-fld-title').click(function()
			{
				self.openSettings($(this), view_container);
			});
		},
		
		/**
		 * Moves field from used section to available fields section
		 *
		 * @param {object} frm_el Fields UI forms HTML object
		 * @param {object} fld_el Field element HTML object
		 * @param {object} view_container Grid view main object's HTML element
		 * @returns {undefined}
		 */
		removeFld: function(frm_el, fld_el, view_container)
		{
			frm_el.find('.dx-fields-container .dx-available ol.dd-list').append(fld_el.closest('.dd-item').clone());
			fld_el.closest('.dd-item').remove();
			
			var new_el = frm_el.find('.dx-fields-container .dx-available ol.dd-list .dd-item').last();
			this.setFldEventHandlers(frm_el, new_el, view_container);
			
			this.clearSearchIfLast(frm_el, 'dx-used');
		},
		
		/**
		 * Moves field from available section to used fields section
		 *
		 * @param {object} frm_el Fields UI forms HTML object
		 * @param {object} fld_el Field element HTML object
		 * @param {object} view_container Grid view main object's HTML element
		 * @returns {undefined}
		 */
		addFld: function(frm_el, fld_el, view_container)
		{
			frm_el.find('.dx-fields-container .dx-used ol.dd-list').append(fld_el.closest('.dd-item').clone());
			fld_el.closest('.dd-item').remove();
			
			var new_el = frm_el.find('.dx-fields-container .dx-used ol.dd-list .dd-item').last();
			this.setFldEventHandlers(frm_el, new_el, view_container);
			
			this.clearSearchIfLast(frm_el, 'dx-available');
		},
		
		/**
		 * Sets handles for checkboxies (is default and is my view only)
		 *
		 * @param {object} frm_el Fields UI forms HTML object
		 * @returns {undefined}
		 */
		handleIsMyCheck: function(frm_el)
		{
			frm_el.find("input[name=is_my_view]").change(function()
			{
				if($(this).prop('checked'))
				{
					frm_el.find("input[name=is_default]").prop('checked', '').closest('span').hide();
				}
				else
				{
					frm_el.find("input[name=is_default]").closest('span').show();
				}
			});
			
			frm_el.find("input[name=is_default]").change(function()
			{
				if($(this).prop('checked'))
				{
					frm_el.find("input[name=is_my_view]").prop('checked', '').closest('span').hide();
				}
				else
				{
					frm_el.find("input[name=is_my_view]").closest('span').show();
				}
			});
		},
		
		/**
		 * Clear fields search input in case if no more fields in container (and show again all fields in container)
		 *      *
		 * @param {object} frm_el Fields UI forms HTML object
		 * @param {string} fields_class HTML class name of fields container (dx-used or dx-available)
		 * @returns {undefined}
		 */
		clearSearchIfLast: function(frm_el, fields_class)
		{
			if(frm_el.find('.dx-fields-container .' + fields_class + ' ol.dd-list .dd-item:visible').length == 0)
			{
				var txt = frm_el.find('.dx-fields-container .' + fields_class).closest('.portlet').find('input.dx-search');
				if(txt.val().length > 0)
				{
					txt.val('');
					txt.closest(".portlet").find(".dx-fields-container .dd-item").show();
					txt.focus();
				}
			}
		},
		
		/**
		 * Opens field's setting form
		 *
		 * @param {object} title_el Field item title HTML element
		 * @param {object} view_container Grid view main object's HTML element
		 * @returns {undefined}
		 */
		openSettings: function(title_el, view_container)
		{
			if(title_el.closest('.dx-cms-nested-list').hasClass('dx-used'))
			{
				var item = title_el.closest('.dd-item');
				var sett_el = view_container.find('.dx-popup-modal-settings');
				
				sett_el.find("input[name=is_hidden]").prop("checked", (item.attr("data-is-hidden") == "1") ? "checked" : "");
				sett_el.find("input[name=field_title]").val(title_el.text());
				sett_el.find("select[name=field_operation]").val(item.attr("data-operation-id"));
				
				if(item.attr("data-field-type") == "autocompleate" || item.attr("data-field-type") == "rel_id")
				{
					sett_el.find("select[name=field_operation]").attr("data-criteria", "auto");
					
					var auto_fld = sett_el.find("div.dx-autocompleate-field");
					auto_fld.attr("data-rel-list-id", item.attr('data-rel-list-id'));
					auto_fld.attr("data-rel-field-id", item.attr('data-rel-field-id'));
					auto_fld.attr("data-item-value", item.attr('data-criteria'));
					auto_fld.attr("data-field-id", item.attr('data-id'));
					
					var formData = new FormData();
					formData.append("list_id", item.attr('data-rel-list-id'));
					formData.append("txt_field_id", item.attr('data-rel-field-id'));
					formData.append("txt_field_id", item.attr('data-rel-field-id'));
					formData.append("value_id", item.attr('data-criteria'));
					
					show_form_splash();
					$.ajax({
						type: 'POST',
						url: DX_CORE.site_url + "view/auto_data",
						data: formData,
						processData: false,
						contentType: false,
						dataType: "json",
						async: false,
						success: function(data)
						{
							hide_form_splash();
							auto_fld.attr("data-min-length", data['count']);
							auto_fld.attr("data-item-text", data['txt']);
						}
					});
					
					AutocompleateField.initSelect(auto_fld);
				}
				else
				{
					sett_el.find("select[name=field_operation]").attr("data-criteria", "text");
					sett_el.find("input[name=criteria_value]").val(item.attr("data-criteria"));
				}
				
				this.showHideCriteria(sett_el, sett_el.find("select[name=field_operation]"));
				
				var btn_save = sett_el.find('.dx-settings-btn-save');
				
				btn_save.off("click");
				btn_save.click(function()
				{
					var oper_el = sett_el.find('select[name=field_operation]');
					
					var crit_val = "";
					if(oper_el.attr("data-criteria") == "text")
					{
						crit_val = sett_el.find('input[name=criteria_value]').val();
					}
					else
					{
						crit_val = parseInt(sett_el.find('input.dx-auto-input-id').val());
					}
					
					if(oper_el.val() && oper_el.find('option:selected').attr('data-is-criteria') != "0" && !crit_val)
					{
						notify_err(Lang.get('grid.error_filter_must_be_set'));
						return false;
					}
					
					item.attr("data-criteria", crit_val);
					item.attr("data-is-hidden", sett_el.find('input[name=is_hidden]').is(":checked") ? 1 : 0);
					item.attr("data-operation-id", oper_el.val());
					
					sett_el.modal('hide');
				});
				
				sett_el.modal('show');
			}
		},
		
		/**
		 * Handles event for show or hide criteria field depending on selected operation
		 *
		 * @param {object} view_container Grid view main object's HTML element
		 * @returns {undefined}
		 */
		handleFieldOperation: function(view_container)
		{
			var self = this;
			
			var sett_el = view_container.find('.dx-popup-modal-settings');
			sett_el.find('select[name=field_operation]').change(function()
			{
				self.showHideCriteria(sett_el, $(this));
			});
		},
		
		/**
		 * Shoe or hide criteria field depending on selected operation
		 * @param {object} sett_el Setting popup form HTML element
		 * @param {object} sel_el Operation select HTML element
		 * @returns {undefined}
		 */
		showHideCriteria: function(sett_el, sel_el)
		{
			if(sel_el.find('option:selected').attr('data-is-criteria') != "0")
			{
				if(sel_el.attr("data-criteria") == "text")
				{
					sett_el.find(".dx-criteria-text").show();
					sett_el.find(".dx-criteria-auto").hide();
					sett_el.find("input[name=criteria_value]").focus();
				}
				else
				{
					sett_el.find(".dx-criteria-text").hide();
					sett_el.find(".dx-criteria-auto").show();
					sett_el.find('.dx-auto-input-select2').select2("open");
				}
			}
			else
			{
				sett_el.find("input[name=criteria_value]").val('');
				sett_el.find(".dx-criteria-auto").hide();
				sett_el.find(".dx-criteria-text").hide();
				sett_el.find('.dx-auto-input-select2').select2('data', {id: 0, text: ""});
				sett_el.find("input.dx-auto-input-id").val(0);
			}
		},
		
		/**
		 * Handles fields searching functionality
		 * @returns {undefined}
		 */
		handleSearchField: function()
		{
			$("input.dx-search").on("keyup", function()
			{
				if(!$(this).val())
				{
					$(this).closest(".portlet").find(".dx-fields-container .dd-item").show();
					return;
				}
				$(this).closest(".portlet").find(".dx-fields-container .dd-item").hide();
				$(this).closest(".portlet").find(".dx-fields-container .dx-fld-title:contains('" + $(this).val() + "')").closest(".dd-item").show();
				
			});
		},
		
		/**
		 * Handles view copy function
		 *
		 * @param {object} view_container Grid view main object's HTML element
		 * @returns {undefined}
		 */
		handleBtnCopy: function(view_container)
		{
			var pop_el = $("#" + view_container.attr("id") + "_popup");
			pop_el.find(".dx-view-btn-copy").click(function()
			{
				var frm_el = pop_el.find(".dx-view-edit-form");
				frm_el.data('view-id', 0);
				pop_el.find(".dx-view-btn-copy").hide();
				pop_el.find(".dx-view-btn-delete").hide();
				pop_el.find("span.badge").html(Lang.get('grid.badge_new'));
				frm_el.find("input[name=view_title]").val(frm_el.find("input[name=view_title]").val() + " - " + Lang.get('grid.title_copy')).focus();
				
				frm_el.find('input[name=is_default]').prop("checked", '').show().closest('span').show();
				frm_el.find('input[name=is_my_view]').prop("checked", '').closest('span').show();
			});
		},
		
		/**
		 * Handles button "Delete" pressing
		 *
		 * @param {object} view_container Grid view main object's HTML element
		 * @returns {undefined}
		 */
		handleBtnDelete: function(view_container)
		{
			var self = this;
			var pop_el = $("#" + view_container.attr("id") + "_popup");
			pop_el.find(".dx-view-btn-delete").click(function()
			{
				PageMain.showConfirm(self.deleteView, {
					view_container: view_container,
					self: self
				}, null, Lang.get('grid.confirm_delete'), Lang.get('form.btn_yes'), Lang.get('form.btn_no'));
			});
		},
		
		/**
		 * Loads view after previous view deletion or new view creation
		 *
		 * @param {object} view_container Grid view main object's HTML element
		 * @param {integer} view_id View ID
		 * @returns {undefined}
		 */
		reloadAnotherView: function(view_container, view_id)
		{
			if(view_container.attr('dx_tab_id'))
			{
				if(this.options.load_tab_grid)
				{
					this.options.load_tab_grid(view_container.attr('dx_tab_id'), view_container.attr('dx_list_id'), view_id, view_container.attr('dx_rel_field_id'), view_container.attr('dx_rel_field_value'), view_container.attr('dx_form_htm_id'), 1, 5, 1);
				}
			}
			else
			{
				show_page_splash(1);
				var url = this.options.root_url + 'skats_' + view_id;
				window.location.assign(encodeURI(url));
			}
		},
		
		/**
		 * Handles view deletion functionality
		 *
		 * @param {object} view_container Grid view main object's HTML element
		 * @returns {undefined}
		 */
		deleteView: function(params)
		{
			var self = params.self;
			var view_container = params.view_container;
			
			var pop_el = $("#" + view_container.attr("id") + "_popup");
			var frm_el = pop_el.find(".dx-view-edit-form");
			
			var formData = new FormData();
			formData.append("view_id", frm_el.data('view-id'));
			formData.append("list_id", frm_el.data('list-id'));
			formData.append('tab_id', view_container.attr('dx_tab_id'));
			
			var request = new FormAjaxRequest('view/delete', "", "", formData);
			request.progress_info = true;
			
			request.callback = function(data)
			{
				if(data["success"] == 1)
				{
					pop_el.modal('hide');
					self.reloadAnotherView(view_container, data["view_id"]);
				}
			};
			
			// execute AJAX request
			request.doRequest();
		},
		
		/**
		 * Prepares JSON string with all fields included in view (in correct order)
		 *
		 * @param {object} block Fields container HTML element
		 * @returns {string}
		 */
		getFieldsState: function(block)
		{
			var ret_arr = new Array();
			
			block.find(".dd-item").each(function()
			{
				var item = {
					"field_id": $(this).attr('data-id'),
					"aggregation_id": $(this).attr('data-aggregation-id'),
					"list_id": $(this).attr('data-list-id'),
					"is_hidden": $(this).attr('data-is-hidden'),
					"operation_id": $(this).attr('data-operation-id'),
					"criteria": $(this).attr('data-criteria')
				};
				ret_arr.push(item);
			});
			
			return JSON.stringify(ret_arr);
		},
		
		/**
		 * Handles button event - save view data
		 *
		 * @param {object} view_container Grid view main object's HTML element
		 * @returns {undefined}
		 */
		handleBtnSaveView: function(view_container)
		{
			var self = this;
			var pop_el = $("#" + view_container.attr("id") + "_popup");
			pop_el.find(".dx-view-btn-save").click(function()
			{
				self.save();
			});
		},
		
		/**
		 * Save view data. Can be invoked externally.
		 */
		save: function()
		{
			var self = this;
			var view_container = this.options.view_container;
			var pop_el = $("#" + view_container.attr("id") + "_popup");
			var frm_el = pop_el.find(".dx-view-edit-form");
			var view_id = frm_el.data('view-id');
			var grid_el = view_container.find('.dx-grid-table').last();
			
			var formData = new FormData();
			formData.append("view_id", view_id);
			formData.append("list_id", frm_el.data('list-id'));
			formData.append("view_title", frm_el.find('input[name=view_title]').val());
			formData.append("is_default", frm_el.find('input[name=is_default]').is(":checked") ? 1 : 0);
			formData.append("is_my_view", frm_el.find('input[name=is_my_view]').is(":checked") ? 1 : 0);
			formData.append("fields", self.getFieldsState(frm_el.find('.dx-fields-container .dx-used')));
			formData.append('grid_id', grid_el.attr('id'));
			
			var request = new FormAjaxRequest('view/save', "", "", formData);
			request.progress_info = true;
			
			request.callback = function(data)
			{
				if(data["success"] == 1)
				{
					
					if(self.options.onsave)
					{
						self.options.onsave.call(this, data["view_id"]);
					}
					else
					{
						pop_el.modal('hide');
						pop_el.attr("id", pop_el.attr("id") + "_" + $(".dx-popup-modal").length);
						
						if(view_id == 0)
						{
							self.reloadAnotherView(view_container, data["view_id"]);
						}
						else
						{
							if(self.options.reloadBlockGrid)
							{
								self.options.reloadBlockGrid(grid_el.attr('id'), grid_el.data('tab_id'));
							}
						}
					}
				}
			};
			
			// execute AJAX request
			request.doRequest();
		}
	});
	
})(jQuery);

$(document).ajaxComplete(function(event, xhr, settings)
{
	$("input.dx-bool").ViewEditor();
});

$(document).ready(function()
{
	$("input.dx-bool").ViewEditor();
});
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
		this.tabAddRowBtn = $('.dx-constructor-tabs .dx-add-row-btn');
		
		this.activeTab.hasClass('related-grid') && this.tabAddRowBtn.hide();
		
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
			this.tabAddRowBtn.show();
		}
		else
		{
			this.tabAddRowBtn.hide();
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
				
				if(!$('.dx-constructor-tab-button').length)
				{
					$('.dx-constructor-form-tabs').show();
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
				
				var tab = $('<div class="dx-constructor-tab" style="display: none"></div>')
					.appendTo('.dx-constructor-tabs-wrap');
				
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
					
					grid.data('ConstructorGrid').createRow('columns');
					grid.data('ConstructorGrid').createRow('columns');
					grid.data('ConstructorGrid').createRow('columns');
					grid.data('ConstructorGrid').createRow('columns');
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
					
					var buttons = $('.dx-constructor-tab-button');
					
					if(buttons.length)
					{
						self.activate(buttons.first());
					}
					else
					{
						$('.dx-constructor-form-tabs').hide();
					}
					
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
	construct: function()
	{
		var self = this;
		
		var initName = 'init_' + this.options.step;
		var submitName = 'submit_' + this.options.step;
		
		// init current step
		if($.isFunction(self[initName]))
		{
			self[initName]();
		}
		
		this.root.on('click', '.mt-element-step .link', function()
		{
			window.location = $(this).data('url');
		});
		
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
	},
	
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
		
		this.viewEditor = $(".dx-view-edit-form").data('ViewEditor');
		
		$('.dx-new-field').click(function()
		{
			new_list_item(7, 17, self.options.list_id, "", "", {
				after_close: function(frm)
				{
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
	
	init_fields: function()
	{
		var self = this;
		
		this.root.find('.dx-constructor-tab-buttons').ConstructorTabs({
			form_id: this.options.form_id,
			list_id: this.options.list_id
		});
		
		this.root.find('.dx-constructor-grid').ConstructorGrid({
			list_id: this.options.list_id
		});
		
		// handle row creation
		this.root.find('.dx-add-row-btn').click(function()
		{
			var grid;
			
			if($(this).parents('.dx-constructor-tabs').length)
			{
				var tab = self.root.find('.dx-constructor-tab:visible');
				
				grid = tab.find('.dx-constructor-grid').data('ConstructorGrid');
			}
			
			else
			{
				grid = self.root.find('.dx-constructor-form .dx-constructor-grid').data('ConstructorGrid');
			}
			
			grid.createRow($(this).data('type'));
		});
		
		$('.dx-preview-btn').click(function()
		{
			self.submit_fields(function()
			{
				new_list_item(self.options.list_id, 0, 0, "", "");
			});
		});
	},
	
	init_rights: function()
	{
		var self = this;
		var rights = ['is_new_rights', 'is_edit_rights', 'is_delete_rights'];
		var tbody = $('.dx-constructor-roles-table tbody');
		
		this.root.on('click', '.dx-constructor-add-role', function()
		{
			new_list_item(23, 105, self.options.list_id, "", "", {
				after_close: function(frm)
				{
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
		
		this.root.on('click', '.dx-constructor-edit-role', function()
		{
			var a = $(this);
			var td = $(this).closest('td').next();
			var role_id = $(this).data('role_id');
			
			view_list_item('form', role_id, 23, 105, self.options.list_id, "", "", {
				after_close: function(frm)
				{
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
	
	submit_names: function()
	{
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
			tabs: [],
			items: []
		};
		
		this.root.find('.dx-constructor-tab-buttons .dd-item').each(function()
		{
			request.tabs.push($(this).data('id'));
		});
		
		this.root.find('.dx-constructor-form, .dx-constructor-tab.custom-data').each(function()
		{
			var tab = [];
			
			$(this).find('.dx-constructor-grid .dx-constructor-row').each(function()
			{
				var row = [];
				var label;
				
				if($(this).find('.columns').length)
				{
					$(this).find('.dd-item').each(function()
					{
						row.push($(this).data('id'));
					});
				}
				
				else if((label = $(this).find('.dx-constructor-label')) && label.length && label.val().length)
				{
					row.push({
						label: label.val()
					})
				}
				
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
			success: function(data)
			{
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
	
	/**
	 * Saves workflow data
	 */
	submit_workflows: function()
	{
		var self = this;
		
		if($('.dx-cms-workflow-form-input-title').val().trim().length > 0)
		{
			var workflow = $('.dx-cms-workflow-form')[0].workflow;
			
			workflow.saveCallback = function()
			{
				if(workflow.isGraphInit)
				{
					window.location = self.getNextUrl();
				}
			};
			
			workflow.save({self: workflow, initGraph: true});
		}
		else
		{
			window.location = self.getNextUrl();
		}
	}
});

//# sourceMappingURL=elix_constructor_wizard.js.map
