(function($)
{
	/**
	 * FreeForm - a jQuery plugin for working with arbitrary forms
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.FreeForm = function(opts)
	{
		var options = $.extend({}, $.fn.FreeForm.defaults, opts);
		
		return this.each(function()
		{
			new $.FreeForm(this, options);
		});
	};
	
	$.fn.FreeForm.defaults = {
		editBtnSelector: '.dx-edit-general',
		saveBtnSelector: '.dx-save-general',
		cancelBtnSelector: '.dx-cancel-general',
		names: []
	};
	
	/**
	 * FreeForm constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.FreeForm = function(root, opts)
	{
		// store object instance along with a root DOM element
		$.data(root, 'FreeForm', this);
		
		var self = this;
		this.root = $(root);
		this.options = opts;
		this.fields = $('[data-name]', this.root).filter(function()
		{
			if(self.options.names.length == 0 || self.options.names.indexOf($(this).data('name')) != -1)
				return true;
		});
		this.originalData = {};
		this.editButton = $(this.options.editBtnSelector, this.root);
		this.saveButton = $(this.options.saveBtnSelector, this.root);
		this.cancelButton = $(this.options.cancelBtnSelector, this.root);
		
		// Bind callbacks to buttons
		this.editButton.click(function() { self.edit(); });
		this.saveButton.click(function() { self.save(); });
		this.cancelButton.click(function() { self.cancel(); });
	};
	
	/**
	 * FreeForm methods
	 */
	$.extend($.FreeForm.prototype, {
		/**
		 * Replace HTML with form input fields
		 */
		edit: function()
		{
			var self = this;
			
			// a structure for JSON request
			var request = {
				model: this.root.data('model'),
				item_id: this.root.data('item_id'),
				list_id: this.root.data('list_id'),
				fields: []
			};
			
			// collect names of input fields marked with data-name attribute
			this.fields.each(function()
			{
				self.originalData[$(this).data('name')] = $(this).html();
				request.fields.push({
					name: $(this).data('name'),
					display: ($(this).data('display') ? $(this).data('display') : 'raw')
				});
			});
			
			show_page_splash(1);
			
			// perform a request to the server
			$.ajax({
				type: 'POST',
				url: DX_CORE.site_url + 'freeform/' + request.item_id + '/edit',
				dataType: 'json',
				data: request,
				success: function(data)
				{
					if(typeof data.success != "undefined" && data.success == 0)
					{
						notify_err(data.error);
						hide_page_splash(1);
						return;
					}
					
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
					hide_page_splash(1);                                        
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(textStatus);
					console.log(jqXHR);
					console.log(errorThrown);
				}
			});
		},
		
		/**
		 * Submit input field values to the server
		 */
		save: function()
		{
			var self = this;
			var fieldMetadata = {};
			var formData = process_data_fields(this.root.attr('id'));
			formData.append('model', this.root.data('model'));
			formData.append('item_id', this.root.data('item_id'));
			formData.append('list_id', this.root.data('list_id'));
			formData.append('edit_form_id', this.root.data('form_id'));
			
			// collect metadata of input fields
			this.fields.each(function()
			{
				fieldMetadata[$(this).data('name')] = {
					display: $(this).data('display') ? $(this).data('display') : 'raw'
				};
			});
			
			formData.append('field_metadata', JSON.stringify(fieldMetadata));
			
			show_page_splash(1);
			
			// submit a request
			$.ajax({
				type: 'POST',
				url: DX_CORE.site_url + 'freeform/' + this.root.data('item_id') + '?_method=PUT',
				dataType: 'json',
				processData: false,
				contentType: false,
				data: formData,
				success: function(data)
				{
					if(typeof data.success != "undefined" && data.success == 0)
					{
						notify_err(data.error);
						hide_page_splash(1);
						return;
					}
					
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
					hide_page_splash(1);
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(textStatus);
					console.log(jqXHR);
					console.log(errorThrown);
				}
			});
		},
		
		/**
		 * Remove input fields and display original HTML
		 */
		cancel: function()
		{
			var self = this;
			this.editButton.show();
			this.saveButton.hide();
			this.cancelButton.hide();
			this.fields.each(function()
			{
				$(this).html(self.originalData[$(this).data('name')]);
			});
		}
	});
})(jQuery);
/**
 * Author:  Eugene Ostapenko <evo@olympsoft.com>
 * License: MIT
 * Created: 04.11.16, 19:33
 */
(function($)
{
	/**
	 * InlineForm - a jQuery plugin that provides a way to work with AJAX form embedded into a page
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.InlineForm = function(opts)
	{
		var options = $.extend({}, $.fn.InlineForm.defaults, opts);
		
		return this.each(function()
		{
			new $.InlineForm(this, options);
		});
	};
	
	$.fn.InlineForm.defaults = {
		beforeSave: null,
		afterSave: null,
		empl_search_page_url: "/search"
	};
	
	/**
	 * InlineForm constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.InlineForm = function(root, opts)
	{
		$.data(root, 'InlineForm', this);
		var self = this;
		this.options = opts;
		this.root = $(root);
		this.tabs = $('.tab-pane', this.root);
		
		// detached fields, that are custom placed outside of main form (e.g. avatar in employee profile)
		this.fields = $('[data-name]', this.root);
		this.fieldNames = [];
		
		this.fields.each(function()
		{
			self.fieldNames.push($(this).data('name'));
		});
		
		this.originalTabs = {};
		this.originalFields = {};
		this.editButton = $('.dx-edit-profile', this.root);
		this.saveButton = $('.dx-save-profile', this.root);
		this.cancelButton = $('.dx-cancel-profile', this.root);
		this.deleteButton = $('.dx-delete-profile', this.root);
		this.requests = {};
		this.onRequestSuccess = [];
		this.onRequestFailed = [];
		
		// Bind callbacks to buttons
		this.editButton.click(function()
		{
			self.edit();
		});
		this.saveButton.click(function()
		{
			self.save();
		});
		this.cancelButton.click(function()
		{
			self.cancel();
		});
		this.deleteButton.click(function()
		{
			self.destroy();
		});
	};
	
	/**
	 * InlineForm methods
	 */
	$.extend($.InlineForm.prototype, {
		/**
		 * Resets and initializes all async request processing parameters
		 * @param {integer} total Total count of processes which will be processed asynchronously
		 */
		initRequest: function(total)
		{
			this.requests = {
				total: total,
				succeeded: 0,
				failed: 0
			};
			
			this.onRequestSuccess = [];
			this.onRequestFailed = [];
		},
		/**
		 * Saves completed request status. If all request are finished, then execute success commands
		 * @param {boolean} is_success Parmeter if process was successful
		 */
		setRequestStatus: function(is_success)
		{
			if(is_success)
			{
				this.requests.succeeded++;
			}
			else
			{
				this.requests.failed++;
			}
			
			if(this.requests.total === (this.requests.succeeded + this.requests.failed))
			{
				if(this.requests.failed === 0)
				{
					for(var i = 0; i < this.onRequestSuccess.length; i++)
					{
						this.onRequestSuccess[i].func(this.onRequestSuccess[i].args);
					}
				}
				else
				{
					for(var i = 0; i < this.onRequestFailed.length; i++)
					{
						this.onRequestFailed[i].func(this.onRequestFailed[i].args);
					}
				}
				
				hide_page_splash(1);
			}
		},
		
		storeOriginalData: function()
		{
			var self = this;
			
			this.fields = $('[data-name]', this.root);
			
			this.tabs.each(function()
			{
				self.originalTabs[$(this).data('tabTitle')] = $(this).html();
			});
			
			this.fields.each(function()
			{
				self.originalFields[$(this).data('name')] = $(this).html();
			});
			
		},
		
		/**
		 * Replace HTML with form input fields
		 */
		edit: function()
		{
			var self = this;
			
			// a structure for JSON request
			var request = {
				list_id: this.root.data('list_id'),
				tab_list: [],
				field_list: this.fieldNames
			};
			
			this.storeOriginalData();
			
			show_page_splash(1);
			
			// perform a request to the server
			$.ajax({
				type: 'POST',
				url: DX_CORE.site_url + 'inlineform/' + this.root.data('item_id') + '/edit',
				dataType: 'json',
				data: request,
				success: function(data)
				{
					if(typeof data.success != "undefined" && data.success == 0)
					{
						notify_err(data.error);
						hide_page_splash(1);
						return;
					}
					
					self.editButton.hide();
					
					var tabs = $($.parseHTML('<div>' + data.tabs + '</div>')).find('.tab-pane');
					
					// replace original html content of marked elements with input fields
					for(var i = 0; i < tabs.length; i++)
					{
						var tab = $(tabs[i]);
						var elem = $('[data-tab-title="' + tab.data('tabTitle') + '"]', self.root);
						if(elem.length)
							elem.html(tab.html());
					}
					
					for(var name in data.fields)
					{
						$('[data-name="' + name + '"]').html(data.fields[name]);
					}
					
					if(self.root.data('has_users_documents_access') == 1)
					{
						window.DxEmpPersDocs.toggleDisable(false);
					}
					
					hide_page_splash(1);
					
					$('.dx-stick-footer').show();
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(textStatus);
					console.log(jqXHR);
					hide_page_splash(1);
				}
			});
		},
		
		/**
		 * Submit input field values to the server
		 */
		save: function()
		{
			var self = this;
			var formData = process_data_fields(this.root.attr('id'));
			formData.append('item_id', this.root.data('item_id'));
			formData.append('list_id', this.root.data('list_id'));
			formData.append('edit_form_id', this.root.data('form_id'));
			formData.append('redirect_url', this.root.data('redirect_url'));
			formData.append('field_list', JSON.stringify(this.fieldNames));
			
			var url = DX_CORE.site_url + 'inlineform';
			if(this.root.data('mode') != 'create')
			{
				url += '/' + this.root.data('item_id') + '?_method=PUT';
			}
			
			show_page_splash(1);
			
			// submit a request
			$.ajax({
				type: 'POST',
				url: url,
				dataType: 'json',
				processData: false,
				contentType: false,
				data: formData,
				success: function(data)
				{
					if(typeof data.success != "undefined" && data.success == 0)
					{
						notify_err(data.error);
						hide_page_splash(1);
						return;
					}
					
					if(self.root.data('mode') == 'create')
					{
						if(self.root.data('has_users_documents_access') == 1)
						{
							window.DxEmpPersDocs.userId = data.item_id;
							
							// Custom tab
							window.DxEmpPersDocs.onClickSaveDocs(function()
							{
								window.DxEmpPersDocs.toggleDisable(true);
								
								hide_page_splash(1);
								$('.dx-stick-footer').hide();
								window.location = data.redirect;
							});
						}
						else
						{
							hide_page_splash(1);
							$('.dx-stick-footer').hide();
							window.location = data.redirect;
						}
						
						return;
					}
					
					if(self.root.data('has_users_documents_access') == 1)
					{
						// Custom tab
						window.DxEmpPersDocs.onClickSaveDocs(function()
						{
							window.DxEmpPersDocs.toggleDisable(true);
							
							self.editButton.show();
							
							var tabs = $($.parseHTML('<div>' + data.tabs + '</div>')).find('.tab-pane');
							
							// replace original html content of marked elements with input fields
							for(var i = 0; i < tabs.length; i++)
							{
								var tab = $(tabs[i]);
								var elem = $('[data-tab-title="' + tab.data('tabTitle') + '"]', self.root);
								if(elem.length)
									elem.html(tab.html());
							}
							
							if(self.options.afterSave)
							{
								self.options.afterSave();
							}
							
							hide_page_splash(1);
							$('.dx-stick-footer').hide();
							
						});
					}
					else
					{
						self.editButton.show();
						
						var tabs = $($.parseHTML('<div>' + data.tabs + '</div>')).find('.tab-pane');
						
						// replace original html content of marked elements with input fields
						for(var i = 0; i < tabs.length; i++)
						{
							var tab = $(tabs[i]);
							var elem = $('[data-tab-title="' + tab.data('tabTitle') + '"]', self.root);
							if(elem.length)
								elem.html(tab.html());
						}
						
						for(var name in data.fields)
						{
							$('[data-name="' + name + '"]').html(data.fields[name]);
						}
						
						if(self.options.afterSave)
						{
							self.options.afterSave();
						}
						
						hide_page_splash(1);
						$('.dx-stick-footer').hide();
					}
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(jqXHR);
					console.log(errorThrown);
				}
			});
		},
		
		/**
		 * Remove input fields and display original HTML
		 */
		cancel: function()
		{
			if(this.root.data('mode') == 'create')
			{
				show_page_splash(1);
				window.location = this.options.empl_search_page_url;
				return;
			}
			
			this.editButton.show();
			
			for(var k in this.originalTabs)
			{
				this.tabs.filter('[data-tab-title="' + k + '"]').html(this.originalTabs[k]);
			}
			
			for(var name in this.originalFields)
			{
				this.fields.filter('[data-name="' + name + '"]').html(this.originalFields[name]);
			}
			
			$('.dx-stick-footer').hide();
			
			if(this.root.data('has_users_documents_access') == 1)
			{
				window.DxEmpPersDocs.cancelEditMode();
			}
		},
		
		/**
		 * Delete an item from storage.
		 */
		destroy: function()
		{
			if(!confirm(Lang.get('frame.confirm_delete')))
				return;
			
			var request = {
				edit_form_id: this.root.data('form_id'),
				item_id: this.root.data('item_id')
			};
			
			show_page_splash(1);
			
			$.ajax({
				type: 'POST',
				url: DX_CORE.site_url + 'inlineform/' + this.root.data('item_id') + '?_method=DELETE',
				dataType: 'json',
				data: request,
				success: function(data)
				{
					if(typeof data.success != "undefined" && data.success == 0)
					{
						notify_err(data.error);
						hide_page_splash(1);
						return;
					}
					
					window.location = data.redirect;
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(textStatus);
					console.log(jqXHR);
					hide_page_splash(1);
				}
			});
		}
	});
})(jQuery);

(function($)
{
	/**
	 * EmplLinksFix - a jQuery plugin that fix (re-init) links to other employees (manager, reporting manager)
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.EmplLinksFix = function(opts)
	{
		var options = $.extend({}, $.fn.EmplLinksFix.defaults, opts);
		return this.each(function()
		{
			new $.EmplLinksFix(this, options);
		});
	};
	
	$.fn.EmplLinksFix.defaults = {
		profile_url: "/employee/profile/"
	};
	
	/**
	 * EmplLinksFix constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.EmplLinksFix = function(root, opts)
	{
		$.data(root, 'EmplLinksFix', this);
		var self = this;
		this.options = opts;
		this.root = $(root);
                
		this.flds_managers = $('.dx-autocompleate-field[data-item-field=manager_id], .dx-autocompleate-field[data-item-field=reporting_manager_id]', this.root);
		this.tiles_managers = $('.employee-manager-tile', this.root);
                
                this.tiles_managers.each(function()
                {
                    if (!$(this).data("is-fix-init")) {
                        
                        $(this).click(function() {
                            self.show_tile_manager($(this).data("empl-id"));
                        });
                    
                        $(this).data("is-fix-init", 1);
                    }
                });
                                
                this.flds_managers.each(function()
                {
                    if (!$(this).data("is-fix-init")) {
                        var fld = $(this);
                        fld.find(".dx-rel-id-add-btn").off("click"); //remove original event handler which will open CMS form
                        
                        fld.find(".dx-rel-id-add-btn").click(function() {
                            self.show_manager(fld);
                        });
                        
                        // Reset tooltip from edit to view hint
                        fld.find(".dx-rel-id-add-btn").tooltipster('destroy');
                        fld.find(".dx-rel-id-add-btn").attr("title", Lang.get('empl_profile.hint_view_profile'));
                        
                        fld.find(".dx-rel-id-add-btn").tooltipster({
                            theme: 'tooltipster-light',
                            animation: 'grow',
                            maxWidth: 300
                        });
        
                        $(this).data("is-fix-init", 1);
                    }
                });
	};
	
	/**
	 * EmplLinksFix methods
	 */
	$.extend($.EmplLinksFix.prototype, {
		/**
		 * Show manager profile
		 */
		show_manager: function(fld)
		{
                    var item_id = fld.find(".dx-auto-input-id").val();
                    
                    if (item_id == 0) {
                        return;
                    }
                    show_page_splash(1);
                    window.location = this.options.profile_url + item_id;
		},
                show_tile_manager: function(empl_id)
                {
                    show_page_splash(1);
                    window.location = this.options.profile_url + empl_id;
                }
	});
})(jQuery);

$(document).ajaxComplete(function() {
    $(".dx-employee-profile.freeform").EmplLinksFix();
});

$(document).ready(function() {
    $(".dx-employee-profile.freeform").EmplLinksFix();
});
/**
 * 
 * @type Window.DxEmpPersDocs|window.DxEmpPersDocs Contains logic for viewing and editing employee's documents
 */
window.DxEmpPersDocs = window.DxEmpPersDocs || {
    /**
     * Row counter, used to identify rows
     */
    rowCount: 0,
    /**
     * User ID which is loaded
     */
    userId: 0,
    /**
     * Date format used in system. This format is used to initialize date picker
     */
    dateFormat: '',
    /**
     * Locale used in system. This format is used to initializes date picker
     */
    locale: 'en',
    /**
     * Registers ID for table where documents are saved
     */
    empDocListId: 0,
    /**
     * Registers field ID where documents are saved
     */
    empDocFldId: 0,
    /**
     * Parameter if component is initialized
     */
    isInit: false,
    /**
     * Clone of view which contains view state with data saved in database.
     * If edit mode is canceled, then this view is replaced with edit view and all made changes are lost
     */
    viewClone: '',
    /**
     * Callback function which is called after successful components initialization
     * @param {type} data Data which is sent to callback function
     */
    callbackOnInitiSuccess: function (data) {},
    /**
     * Callback function which is called after successful data save
     * @param {type} data Data which is sent to callback function
     */
    callbackOnSaveSuccess: function (data) {},
    /**
     * Callback function which is called after process is exited with error
     * @param {type} data Data which is sent to callback function
     */
    callbackOnError: function (data) {},
    /**
     * Initializes component
     */
    init: function (callbackOnInitiSuccess) {
        if (callbackOnInitiSuccess) {
            window.DxEmpPersDocs.callbackOnInitiSuccess = callbackOnInitiSuccess;
        } else {
            window.DxEmpPersDocs.callbackOnInitiSuccess = function () {};
        }

        window.DxEmpPersDocs.userId = ($('#dx-emp-pers-docs-panel').attr('data-user-id') == '' ? 0 : $('#dx-emp-pers-docs-panel').attr('data-user-id'));
        window.DxEmpPersDocs.dateFormat = $('#dx-emp-pers-docs-panel').attr('data-date-format');
        window.DxEmpPersDocs.locale = $('#dx-emp-pers-docs-panel').attr('data-locale');
        window.DxEmpPersDocs.empDocListId = $('#dx-emp-pers-docs-panel').attr('data-emp-docs-list-id');
        window.DxEmpPersDocs.empDocFldId = $('#dx-emp-pers-docs-panel').attr('data-emp-docs-fld-id');
        $("#dx-emp-pers-docs-country").change(window.DxEmpPersDocs.onChangeCountry);
        window.DxEmpPersDocs.loadEmployeeData();
    },
    /**
     * Enter edit mode by saving view state in memory. 
     * It is needed to revert changes if edit mode is canceled
     */
    enterEditMode: function () {
        window.DxEmpPersDocs.viewClone = $('#dx-emp-pers-docs-panel').clone(true, true);
    },
    /**
     * Cancels edit mode by loading previous view state
     */
    cancelEditMode: function () {
        $('#dx-emp-pers-docs-panel').replaceWith(window.DxEmpPersDocs.viewClone);
        window.DxEmpPersDocs.viewClone = null;
        window.DxEmpPersDocs.toggleDisable(true);
    },
    /**
     * Loads employee document data from server
     */
    loadEmployeeData: function () {
        $.ajax({
            url: '/employee/personal_docs/get/employee_docs/' + window.DxEmpPersDocs.userId,
            type: "get",
            success: window.DxEmpPersDocs.onSuccessLoadEmployeeData,
            error: window.DxEmpPersDocs.onAjaxError
        });
    },
    /**
     * Evenet ahndler on successful employee data retrieval
     * @param {array} data Employee document data which ir retrieved
     */
    onSuccessLoadEmployeeData: function (data) {
        if (data != '') {
            var data_rows = JSON.parse(data);
            // Prepares dropdown list options
            for (var i = 0; i < data_rows.length; i++) {
                window.DxEmpPersDocs.createNewDocRow(false, data_rows[i]);
            }
        }

        $("#dx-emp-pers-docs-country").trigger('change');
    },
    /**
     * Draws document row
     * @param {boolean} is_new Argument if row is new and doesnt contain any data
     * @param {array} data Data which will be used to draw row. Can contains saved data or if new then document type
     */
    createNewDocRow: function (is_new, data) {
        // Gets template for row and converts it as jquery object
        var new_row_html = $($('#dx-emp-pers-docs-new-row').html());
        if (is_new) {
            new_row_html = window.DxEmpPersDocs.setDocTypeValue(new_row_html, data);
        } else {
            new_row_html = window.DxEmpPersDocs.setValues(new_row_html, data);
        }

        // Append row to table
        if (is_new) {
            $('#dx-emp-pers-docs-table').append(new_row_html);
        } else {
            $('#dx-emp-pers-docs-table-history').append(new_row_html);
        }

        // Bind all rquired events for row elements
        window.DxEmpPersDocs.bindDocRowEvenets(new_row_html);
        // Increase row counter
        window.DxEmpPersDocs.rowCount++;
    },
    /**
     * Initiates date picker control in row
     * @param {DOMElement} new_row_html Row's DOM elemenet
     * @param {string} value Date which will be set in date picker
     * @returns {DOMElement} Edited row with initialized date picker
     */
    initValidToDatePicker: function (new_row_html, value) {
        var picker = new_row_html.find('.dx-emp-pers-docs-validto-input');
        picker.attr('id', 'dx-emp-pers-docs-validto-input-' + window.DxEmpPersDocs.rowCount);
        picker.val(value);
        picker.datetimepicker({
            lang: window.DxEmpPersDocs.locale,
            format: window.DxEmpPersDocs.dateFormat,
            timepicker: 0,
            dayOfWeekStart: 1,
            closeOnDateSelect: true
        });
        new_row_html.find('.dx-emp-pers-docs-validto-input-calc').click({picker_num: window.DxEmpPersDocs.rowCount}, function (e) {
            jQuery('#dx-emp-pers-docs-validto-input-' + e.data.picker_num).datetimepicker('show');
        });
        return new_row_html;
    },
    /**
     * Sets data for new document row
     * @param {DOMElement} new_row_html Row's DOM elemenet
     * @param {array} data_row Data array for input values
     * @returns {DOMElement} Row containing values
     */
    setDocTypeValue: function (new_row_html, data_row) {
        // Prepare "valid to" date picker            
        new_row_html = window.DxEmpPersDocs.initValidToDatePicker(new_row_html, '');
        new_row_html.attr('id', 'dx-emp-pers-docs-row-' + data_row.id);
        new_row_html.find('.dx-emp-pers-docs-type-input').val(data_row.id);
        new_row_html.find('.dx-emp-pers-docs-type-label').html(data_row.name);
        return new_row_html;
    },
    /**
     * Sets data for already saved document row
     * @param {DOMElement} new_row_html Row's DOM elemenet
     * @param {array} data_row Data array for input values
     * @returns {DOMElement} Row containing values
     */
    setValues: function (new_row_html, data_row) {
        new_row_html = window.DxEmpPersDocs.initValidToDatePicker(new_row_html, data_row.valid_to);
        new_row_html.attr('id', 'dx-emp-pers-docs-row-' + data_row.doc_id);
        new_row_html.find('.dx-emp-pers-docs-id-input').val(data_row.id);
        new_row_html.find('.dx-emp-pers-docs-type-input').val(data_row.doc_id);
        new_row_html.find('.dx-emp-pers-docs-type-label').html(data_row.personal_document.name);
        new_row_html.find('.dx-emp-pers-docs-docnr-input').val(data_row.doc_nr);
        new_row_html.find('.dx-emp-pers-docs-publisher-input').val(data_row.publisher);
        window.DxEmpPersDocs.setFileValue(new_row_html, data_row.id, data_row.file_name);

        return new_row_html;
    },
    /**
     * Sets saved document link in file input box
     * @param {DOMElement} new_row_html Row's DOM elemenet
     * @param {integer} row_id ID for document row in database
     * @param {string} file_name Saved name for the file
     */
    setFileValue: function (new_row_html, row_id, file_name) {
        if (file_name && file_name != null) {
            var file_link = "<a href='JavaScript: download_file(" + row_id + " , " + window.DxEmpPersDocs.empDocListId + ", " + window.DxEmpPersDocs.empDocFldId + ");'>" + file_name + "</a>";
            new_row_html.find('.dx-emp-pers-docs-file-input-download').html(file_link);
            new_row_html.find('.dx-emp-pers-docs-file-input-isset').val(1);
        }
    },
    /**
     * Clears documents row data
     * @param {object} e Event arguments which contains event caller
     */
    clearDocRow: function (e) {
        var row = $(e.target).parents('.dx-emp-pers-docs-row');
        row.find('.dx-emp-pers-docs-id-input').val(0);
        row.find('.dx-emp-pers-docs-docnr-input').val('');
        row.find('.dx-emp-pers-docs-validto-input').val('');
        row.find('.dx-emp-pers-docs-publisher-input').val('');
        row.find('.dx-emp-pers-docs-file-input-remove-btn').trigger('click');
    },
    /**
     * Gets data from inputs for data saving
     * @returns {FormData} Data retrieved from input fields
     */
    getDataForSave: function () {
        var rows = $('#dx-emp-pers-docs-table .dx-emp-pers-docs-row');
        var data = {
            user_id: window.DxEmpPersDocs.userId,
            rows: []
        };
        var formData = new FormData();
        for (var i = 0; i < rows.length; i++) {
            var row = $(rows[i]);
            var row_data = {};
            row_data.id = row.find('.dx-emp-pers-docs-id-input').val();
            row_data.document_type = row.find('.dx-emp-pers-docs-type-input').val();
            row_data.publisher = row.find('.dx-emp-pers-docs-publisher-input').val();
            row_data.valid_to = row.find('.dx-emp-pers-docs-validto-input').val();
            row_data.doc_nr = row.find('.dx-emp-pers-docs-docnr-input').val();
            row_data.file_remove = $.trim(row.find('.dx-emp-pers-docs-file-input-download').html()) === '';
            var file = row.find('.dx-emp-pers-docs-file-input-file').prop("files")[0];
            formData.append('file' + i, file);
            data.rows.push(row_data);
        }


        formData.append('doc_country_id', $('#dx-emp-pers-docs-country').val());
        formData.append('data', JSON.stringify(data));
        return formData;
    },
    /**
     * Binds click event for clear button
     * @param {DOMElement} new_row_html Row's DOM elemenet
     */
    bindDocRowEvenets: function (new_row_html) {
        new_row_html.find('.dx-emp-pers-docs-clear-btn').click(window.DxEmpPersDocs.clearDocRow);
    },
    /**
     * Event when changing country from dropdown which requests document types associated with selected country
     * @param {object} e Event arguments which contains event caller
     */
    onChangeCountry: function (e) {
        var country_id = $(e.target).val();
        $.ajax({
            url: DX_CORE.site_url + 'employee/personal_docs/get/docs_by_country/' + country_id,
            type: "get",
            success: window.DxEmpPersDocs.onSuccessChangeCountry,
            error: window.DxEmpPersDocs.onAjaxError
        });
    },
    /**
     * Event on successful document type retrieval when changing country
     * @param {array} data Document types associated with country
     */
    onSuccessChangeCountry: function (data) {
        var docs = JSON.parse(data);
        window.DxEmpPersDocs.drawRows(docs);
        window.DxEmpPersDocs.finishInit();
    },
    /**
     * Draws rows when country is changed
     * @param {array} docs Document types associated with selected country
     */
    drawRows: function (docs) {
        // Moves all existing rows to hidden history div
        $('#dx-emp-pers-docs-table').contents().appendTo('#dx-emp-pers-docs-table-history');
        // Iterates through all the document types
        for (var d = 0; d < docs.length; d++) {
            var doc = docs[d];
            var existing_row = $('#dx-emp-pers-docs-row-' + doc.id);
            // Check if row exist in history div
            if (existing_row.length > 0) {
                // Move existing document type row into visible view
                existing_row.appendTo('#dx-emp-pers-docs-table');
            } else {
                // Creates new row if it doesn't exist for document type
                window.DxEmpPersDocs.createNewDocRow(true, doc);
            }
        }
        // Initiates tooltips because they are not initiated because rows are created dynamicaly
        $('#dx-emp-pers-docs-table').find('[data-tooltip-title]').each(function (i, obj) {
            $(obj).attr('title', $(obj).data('tooltip-title'));
            $(obj).tooltip();
        });
    },
    /**
     * Finishes initialization
     */
    finishInit: function () {
        if (!window.DxEmpPersDocs.isInit) {
            window.DxEmpPersDocs.isInit = true;
            if (window.DxEmpPersDocs.userId == 0) {
                window.DxEmpPersDocs.toggleDisable(false);
            } else {
                window.DxEmpPersDocs.toggleDisable(true);
            }
            window.DxEmpPersDocs.callbackOnInitiSuccess();
        }
    },
    /**
     * Saves data
     * @param {function} callbackOnSaveSuccess Callback function for successful saving
     * @param {function} callbackOnError Callback function when error happens on data save
     */
    onClickSaveDocs: function (callbackOnSaveSuccess, callbackOnError) {
        if (callbackOnSaveSuccess) {
            window.DxEmpPersDocs.callbackOnSaveSuccess = callbackOnSaveSuccess;
        } else {
            window.DxEmpPersDocs.callbackOnSaveSuccess = function () {};
        }
        if (callbackOnError) {
            window.DxEmpPersDocs.callbackOnError = callbackOnError;
        } else {
            window.DxEmpPersDocs.callbackOnError = function () {};
        }

        var form_data = window.DxEmpPersDocs.getDataForSave();
        $.ajax({
            url: DX_CORE.site_url + 'employee/personal_docs/save',
            data: form_data,
            type: "post",
            processData: false,
            dataType: "json",
            contentType: false,
            success: window.DxEmpPersDocs.onSuccessSave,
            error: window.DxEmpPersDocs.onAjaxError
        });
    },
    /**
     * Event on successful data save
     * @param {array} data_rows Data returned about saved document rows
     */
    onSuccessSave: function (data_rows) {
        // Set id for rows and update file input control value
        for (var i = 0; i < data_rows.length; i++) {
            var data_row = data_rows[i];
            var row = $('#dx-emp-pers-docs-row-' + data_row.doc_id);

            if (row.length > 0) {
                row.find('.dx-emp-pers-docs-id-input').val(data_row.id);
                row.find('.dx-emp-pers-docs-file-input-remove-btn').trigger('click');
                window.DxEmpPersDocs.setFileValue(row, data_row.id, data_row.file_name);
            }
        }

        $('#dx-emp-pers-docs-table-history').empty();
        window.DxEmpPersDocs.callbackOnSaveSuccess();
    },
    /**
     * Event when ajax request gets error
     * @param {array} data Data containing error information
     */
    onAjaxError: function (data) {
        window.DxEmpPersDocs.finishInit();
        window.DxEmpPersDocs.callbackOnError();
    },
    /**
     * Swicthed edit and view modes
     * @param {boolean} is_disabled If true then view mode is set else edit mode is set
     */
    toggleDisable: function (is_disabled) {
        if (!is_disabled) {
            window.DxEmpPersDocs.enterEditMode();
        }

        var rows = $('#dx-emp-pers-docs-table .dx-emp-pers-docs-row');
        for (var i = 0; i < rows.length; i++) {
            var row = $(rows[i]);
            row.find('.dx-emp-pers-docs-publisher-input').prop('disabled', is_disabled);
            row.find('.dx-emp-pers-docs-validto-input').prop('disabled', is_disabled);
            row.find('.dx-emp-pers-docs-validto-input-calc').prop('disabled', is_disabled);
            row.find('.dx-emp-pers-docs-docnr-input').prop('disabled', is_disabled);
            if (is_disabled) {
                row.find('.dx-emp-pers-docs-file-input-set-btn').hide();
                if (row.find('.dx-emp-pers-docs-file-input-file').prop("files")[0]) {
                    row.find('.dx-emp-pers-docs-file-input-remove-btn').hide();
                }

                row.find('.dx-emp-pers-docs-clear-btn').hide();
            } else {
                row.find('.dx-emp-pers-docs-validto-input').datetimepicker('destroy');
                row.find('.dx-emp-pers-docs-validto-input').datetimepicker({
                    lang: window.DxEmpPersDocs.locale,
                    format: window.DxEmpPersDocs.dateFormat,
                    timepicker: 0,
                    dayOfWeekStart: 1,
                    closeOnDateSelect: true
                });

                row.find('.dx-emp-pers-docs-file-input-set-btn').show();
                if (row.find('.dx-emp-pers-docs-file-input-file').prop("files")[0]) {
                    row.find('.dx-emp-pers-docs-file-input-remove-btn').show();
                }

                row.find('.dx-emp-pers-docs-clear-btn').show();
            }
        }

        $('#dx-emp-pers-docs-country').prop('disabled', is_disabled);
    }
};
//# sourceMappingURL=elix_profile.js.map
