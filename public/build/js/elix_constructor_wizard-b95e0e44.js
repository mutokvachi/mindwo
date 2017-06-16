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
		steps: ['names', 'columns', 'fields', 'rights'],
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
			
			this.root.find('.constructor-grid').ConstructorGrid({
				list_id: this.options.list_id
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
						
						if(role_id == 0)
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
					$('.constructor-grid .dd-item.not-in-form').removeClass('not-in-form');
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
		}
	});
})(jQuery);

//# sourceMappingURL=elix_constructor_wizard.js.map
